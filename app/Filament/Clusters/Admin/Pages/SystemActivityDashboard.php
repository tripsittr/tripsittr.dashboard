<?php

namespace App\Filament\Clusters\Admin\Pages;

use App\Filament\Clusters\Admin;
use App\Models\ActivityLog;
use Illuminate\Notifications\DatabaseNotification;
use Filament\Pages\Page;
use Filament\Tables; 
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;

class SystemActivityDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.admin.system-activity-dashboard';
    protected static ?string $cluster = Admin::class;
    protected static ?string $navigationIcon = 'heroicon-s-queue-list';
    protected static ?string $navigationLabel = 'System Activity';
    protected static ?int $navigationSort = 60;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->type === 'Admin';
    }

    public function getHeading(): string
    {
        return 'System Activity & Notifications';
    }

    public function table(Table $table): Table
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        return $table
            ->query(ActivityLog::query()->when($tenant, fn($q)=> $q->where('team_id',$tenant->id)))
            ->defaultSort('id','desc')
            ->columns([
                TextColumn::make('created_at')->since()->label('When')->sortable(),
                TextColumn::make('action')->label('Action')->searchable()->wrap(),
                TextColumn::make('entity_type')->label('Entity')->sortable(),
                TextColumn::make('entity_id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('User')->placeholder('System'),
            ])
            ->filters([
                SelectFilter::make('action')->options(
                    ActivityLog::query()->select('action')->distinct()->pluck('action','action')->toArray()
                )->multiple(),
                SelectFilter::make('entity_type')->options(
                    ActivityLog::query()->select('entity_type')->distinct()->pluck('entity_type','entity_type')->toArray()
                )->multiple(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public function getUnreadNotificationsProperty()
    {
        return Auth::user()?->unreadNotifications()->limit(25)->get();
    }

    public function markNotificationRead(string $id): void
    {
        $n = Auth::user()?->notifications()->where('id',$id)->first();
        if ($n) { $n->markAsRead(); }
    }
}
