<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Models\Team;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateTeam extends CreateRecord {
    protected static string $resource = TeamResource::class;

    protected static ?string $tenantRelationshipName = 'users';

    protected function handleRecordCreation(array $data): Team {
        $user = auth()->user();

        if (!$user) {
            throw new \Exception('User not authenticated.');
        }

        Log::info('Creating team for user', ['user_id' => $user->id]);

        $team = Team::create($data);
        $team->users()->attach($user->id);

        return $team;
    }
}
