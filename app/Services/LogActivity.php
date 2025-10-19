<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class LogActivity
{
    public static function record(string $action, string $entityType, int $entityId, array $changes = [], ?int $teamId = null): void
    {
        ActivityLog::create([
            'team_id' => $teamId ?? Filament::getTenant()?->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'changes' => empty($changes) ? null : $changes,
            'user_id' => Auth::id(),
        ]);
    }
}
