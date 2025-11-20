<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\SongAnalytics;

class SongAnalyticsController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (! $handle) {
            return back()->with('import_result', 'Unable to open uploaded file');
        }
        $header = null;
        $rows = [];
        $skipped = 0;

        // Read CSV into memory first so we can compare against last import before inserting
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (count($row) === 0) {
                continue;
            }
            if (! $header) {
                // normalize headers
                $header = array_map(function ($h) {
                    return Str::of($h)->trim()->lower()->replace(' ', '_')->__toString();
                }, $row);
                continue;
            }

            $data = array_combine($header, $row);
            if (! $data) {
                $skipped++;
                continue;
            }

            // Map common columns; be tolerant to header naming
            $name = $data['name'] ?? $data['track_name'] ?? $data['title'] ?? null;
            $externalId = $data['id'] ?? $data['track_id'] ?? null;
            $teamId = isset($data['team_id']) ? (int) $data['team_id'] : (\Filament\Facades\Filament::getTenant()?->id ?? $request->user()->current_team_id ?? null);
            $streams = isset($data['#_streams']) ? (int) $data['#_streams'] : (int) ($data['#streams'] ?? $data['streams'] ?? 0);
            $streams_pct = $data['streams_%'] ?? $data['streams%'] ?? null;
            $streams_change = isset($data['#_streams_change']) ? (int) $data['#_streams_change'] : (int) ($data['streams_change'] ?? null);
            $streams_change_pct = $data['streams_change_%'] ?? $data['streams_change%'] ?? null;
            $downloads = isset($data['#_downloads']) ? (int) $data['#_downloads'] : (int) ($data['#downloads'] ?? $data['downloads'] ?? 0);
            $downloads_pct = $data['downloads_%'] ?? $data['downloads%'] ?? null;
            $downloads_change = isset($data['#_downloads_change']) ? (int) $data['#_downloads_change'] : (int) ($data['downloads_change'] ?? null);
            $downloads_change_pct = $data['downloads_change_%'] ?? $data['downloads_change%'] ?? null;

            if (! $name || empty($externalId)) {
                $skipped++;
                continue;
            }

            $rows[] = [
                'name' => $name,
                'external_id' => is_numeric($externalId) ? (int) $externalId : null,
                'streams' => is_numeric($streams) ? (int) $streams : null,
                'streams_pct' => is_numeric($streams_pct) ? (float) $streams_pct : null,
                'streams_change' => is_numeric($streams_change) ? (int) $streams_change : null,
                'streams_change_pct' => is_numeric($streams_change_pct) ? (float) $streams_change_pct : null,
                'downloads' => is_numeric($downloads) ? (int) $downloads : null,
                'downloads_pct' => is_numeric($downloads_pct) ? (float) $downloads_pct : null,
                'downloads_change' => is_numeric($downloads_change) ? (int) $downloads_change : null,
                'downloads_change_pct' => is_numeric($downloads_change_pct) ? (float) $downloads_change_pct : null,
                'team_id' => $teamId,
            ];
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->with('import_result', "No valid rows found. Skipped: $skipped");
        }

        // If any row in the CSV matches the latest imported stats for that team+external_id, ignore the entire file
        foreach ($rows as $r) {
            $previous = SongAnalytics::where('team_id', $r['team_id'])
                ->where('external_id', $r['external_id'])
                ->orderByDesc('imported_at')
                ->first();

            if ($previous) {
                $same = (
                    ($previous->streams === $r['streams']) &&
                    ($previous->streams_pct === $r['streams_pct']) &&
                    ($previous->streams_change === $r['streams_change']) &&
                    ($previous->streams_change_pct === $r['streams_change_pct']) &&
                    ($previous->downloads === $r['downloads']) &&
                    ($previous->downloads_pct === $r['downloads_pct']) &&
                    ($previous->downloads_change === $r['downloads_change']) &&
                    ($previous->downloads_change_pct === $r['downloads_change_pct'])
                );

                if ($same) {
                    return back()->with('import_result', "Import ignored: row for external_id {$r['external_id']} (team {$r['team_id']}) matches previous import");
                }
            }
        }

        // Insert only rows that are new or changed
        $importedAt = now();
        $inserted = 0;

        foreach ($rows as $r) {
            SongAnalytics::create(array_merge($r, ['imported_at' => $importedAt]));
            $inserted++;
        }

        return back()->with('import_result', "Inserted: $inserted, Skipped: $skipped");
    }

    public function export(Request $request)
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? $request->user()->current_team_id ?? null;

        $query = SongAnalytics::query();
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        if ($request->query('start_date')) {
            $query->whereDate('imported_at', '>=', $request->query('start_date'));
        }
        if ($request->query('end_date')) {
            $query->whereDate('imported_at', '<=', $request->query('end_date'));
        }
        if ($request->query('q')) {
            $query->where('name', 'like', '%'.$request->query('q').'%');
        }

        $rows = $query->orderByDesc('imported_at')->get();

        $filename = 'song_analytics_export_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['name','external_id','streams','streams_pct','streams_change','streams_change_pct','downloads','downloads_pct','downloads_change','downloads_change_pct','team_id','imported_at'];

        $callback = function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($rows as $r) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = $r->{$col} ?? '';
                }
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
