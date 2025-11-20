<?php

namespace App\Filament\Artists\Clusters\Music\Widgets;

use App\Models\SongAnalytics;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SongAnalyticsTableWidget extends Widget
{
    protected static string $view = 'filament.clusters.music.widgets.song-analytics-table';

    protected int|string|array $columnSpan = 'full';

    public function getData(): array
    {
        $teamId = \Filament\Facades\Filament::getTenant()?->id ?? optional(Auth::user())->current_team_id;

        $query = SongAnalytics::query()
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId));

        if (request('start_date')) {
            $query->whereDate('imported_at', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->whereDate('imported_at', '<=', request('end_date'));
        }

        if (request('q')) {
            $query->where('name', 'like', '%'.request('q').'%');
        }

        $rows = $query->orderByDesc('imported_at')->paginate(15)->withQueryString();

        return [
            'rows' => $rows,
        ];
    }
}
