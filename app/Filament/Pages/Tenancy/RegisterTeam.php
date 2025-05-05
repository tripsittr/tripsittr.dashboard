<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Pages\Page;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Auth;

class RegisterTeam extends RegisterTenant {
    public static function getLabel(): string {
        return 'Register team';
    }

    public function form(Form $form): Form {
        return $form
            ->schema([
                FileUpload::make('team_avatar')
                    ->image()
                    ->avatar(),
                TextInput::make('name')->required()
                    ->helperText('Artist, Band, or Company Name. (e.g. "The Beatles", "Sony Music")'),
                Select::make('type')
                    ->options([
                        'Solo Artist' => 'Solo Artist',
                        'Band' => 'Band',
                        'Management Agency' => 'Management Agency',
                        'Record Label' => 'Record Label',
                    ])
                    ->required(),
            ]);
    }

    protected function handleRegistration(array $data): Team {
        $team = Team::create($data);

        $team->users()->attach(Auth::user());

        return $team;
    }
}
