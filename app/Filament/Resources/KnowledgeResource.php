<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Knowledge as ClustersKnowledge;
use App\Filament\Resources\KnowledgeResource\Pages;
use App\Filament\Resources\KnowledgeResource\RelationManagers;
use App\Models\Knowledge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\MarkdownEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Support\HtmlString;

class KnowledgeResource extends Resource {
    protected static ?string $model = Knowledge::class;

    protected static bool $isScopedToTenant = false;
    protected static ?string $cluster = ClustersKnowledge::class;

    protected static ?string $navigationIcon = 'fas-book';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'faq' => 'FAQ',
                        'article' => 'Article',
                        'blogpost' => 'Blog Post',
                        'documentation' => 'Documentation',
                        'guide' => 'Guide',
                        'news' => 'News',
                        'book' => 'Book',
                        'course' => 'Course',
                        'case_study' => 'Case Study',
                        'research_paper' => 'Research Paper',
                        'report' => 'Report',
                        'template' => 'Template',
                        'checklist' => 'Checklist',
                        'tool' => 'Tool',
                        'resource' => 'Resource',
                        'other' => 'Other',

                    ]),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'Draft' => 'Draft',
                        'Published' => 'Published',
                        'Archived' => 'Archived',
                    ])
                    ->default('Draft'),
                Forms\Components\TextInput::make('author')
                    ->default(fn($record) => $record->author ?? Auth::user()->name)
                    ->maxLength(255),
                Forms\Components\TextInput::make('source')
                    ->maxLength(255),
                Forms\Components\TextInput::make('source_url')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->sortable()
                        ->grow()
                        ->description('Author')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('slug')
                        ->sortable()
                        ->hidden(),
                    Tables\Columns\TextColumn::make('type')
                        ->label('Record Type')
                        ->sortable()
                        ->hidden()
                        ->grow()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('status')
                        ->hidden()
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('author')
                        ->label('Author')
                        ->description('Updated At')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('source')
                        ->hidden()
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('source_url')
                        ->hidden()
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->hidden()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable(),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                TextEntry::make('content')
                    ->markdown()
                    ->label('')
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->badge()
                    ->label('Type'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Published' => 'success',
                        'Archived' => 'warning',
                    })
                    ->label('Status'),
                TextEntry::make('author')
                    ->badge()
                    ->label('Author'),
                TextEntry::make('source')
                    ->badge()
                    ->label('Source'),
                TextEntry::make('source_url')
                    ->badge()
                    ->label('Source URL'),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListKnowledge::route('/'),
            'create' => Pages\CreateKnowledge::route('/create'),
            'view' => Pages\ViewKnowledge::route('/{record}'),
            'edit' => Pages\EditKnowledge::route('/{record}/edit'),
        ];
    }
}
