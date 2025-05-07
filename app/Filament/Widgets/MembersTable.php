<?php

namespace App\Filament\Widgets;

use App\Models\User; // Ensure the User model is imported
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MembersTable extends BaseWidget
{
    protected static ?string $model = User::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) {
                $user = Auth::user();

                if (!$user) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->where('team_id', $user->team_id);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('created_at')->label('Joined On')->date(),
            ]);
    }
}
