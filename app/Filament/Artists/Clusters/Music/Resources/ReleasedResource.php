<?php

namespace App\Filament\Artists\Clusters\Music\Resources;

use App\Filament\Artists\Clusters\Music\Resources\ReleasedResource\Pages;
use App\Models\Album;
use App\Models\Approval;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReleasedResource extends Resource
{
    // Approval does not have a team relationship, so disable tenancy ownership
    protected static ?string $tenantOwnershipRelationshipName = null;

    protected static ?string $model = Approval::class;

    protected static ?string $navigationIcon = 'heroicon-s-check-badge';

    protected static ?string $navigationLabel = 'Releases';

    protected static ?string $cluster = \App\Filament\Artists\Clusters\Music\Music::class;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->type === 'Admin';
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id'),
            TextColumn::make('approvable_type')->label('Type'),
            TextColumn::make('approvable_id'),
            TextColumn::make('status')->badge(),
            TextColumn::make('submitted_at')->dateTime(),
            TextColumn::make('reviewed_at')->dateTime(),
        ])->actions([
            Tables\Actions\Action::make('approve')
                ->visible(fn ($record) => $record->status === 'pending' && Auth::user()->can('approve', $record->approvable))
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Approval $record) {
                    if ($record->approvable instanceof Album) {
                        $album = $record->approvable;
                        $album->approve(Auth::id());
                        // notify owner
                        if ($album->user_id && $album->status === 'approved') {
                            optional($album->user)->notify(new \App\Filament\Index\Notifications\AlbumApproved($album));
                        } elseif ($album->user_id && $album->status === 'released') {
                            optional($album->user)->notify(new \App\Filament\Index\Notifications\AlbumApproved($album));
                            optional($album->user)->notify(new \App\Filament\Index\Notifications\AlbumReleased($album));
                        }
                    }
                }),
            Tables\Actions\Action::make('reject')
                ->visible(fn ($record) => $record->status === 'pending' && Auth::user()->can('reject', $record->approvable))
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason')->required(),
                ])
                ->action(function (Approval $record, array $data) {
                    if ($record->approvable instanceof Album) {
                        $album = $record->approvable;
                        $album->reject(Auth::id(), $data['reason']);
                    }
                    $record->status = 'rejected';
                    $record->rejection_reason = $data['reason'];
                    $record->reviewed_at = now();
                    $record->approved_by = Auth::id();
                    $record->save();
                }),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReleased::route('/'),
        ];
    }
}
