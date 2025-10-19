<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ActivityTimelineWidget extends Widget
{
    protected static string $view = 'filament.widgets.activity-timeline-widget';
    protected static ?int $sort = 10;

    public ?string $filterAction = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?string $filterUser = null;
    public ?string $filterTeam = null;

    protected $queryString = [
        'filterAction' => ['except' => null],
        'dateFrom' => ['except' => null],
        'dateTo' => ['except' => null],
        'filterUser' => ['except' => null],
        'filterTeam' => ['except' => null],
    ];

    protected function getViewData(): array
    {
        $teamId = optional(Auth::user()?->currentTeam)->id;
        $query = ActivityLog::query()->when($teamId, fn($q)=> $q->where('team_id', $teamId));
        if ($this->filterAction) { $query->where('action', 'like', '%'.$this->filterAction.'%'); }
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        if ($this->filterUser) { $query->where('user_id', $this->filterUser); }
        if ($this->filterTeam) { $query->where('team_id', $this->filterTeam); }
        $actions = $query->latest()->limit(50)->with('user')->get()->map(fn($row)=> [
            'id' => $row->id,
            'type' => $row->action,
            'label' => $row->action,
            'user' => $row->user?->name,
            'time' => $row->created_at?->diffForHumans(),
        ]);
        $actionChoices = ActivityLog::query()->select('action')->distinct()->pluck('action','action')->toArray();
        $userChoices = ActivityLog::query()->select('user_id')->whereNotNull('user_id')->distinct()->pluck('user_id','user_id')->toArray();
        $teamChoices = ActivityLog::query()->select('team_id')->whereNotNull('team_id')->distinct()->pluck('team_id','team_id')->toArray();
        return [
            'items' => $actions,
            'actionChoices' => $actionChoices,
            'filterAction' => $this->filterAction,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'userChoices' => $userChoices,
            'teamChoices' => $teamChoices,
            'filterUser' => $this->filterUser,
            'filterTeam' => $this->filterTeam,
        ];
    }
}
