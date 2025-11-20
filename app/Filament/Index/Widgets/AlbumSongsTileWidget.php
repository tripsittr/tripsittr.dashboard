<?php
namespace App\Filament\Index\Widgets;

use Filament\Widgets\Widget;
use App\Models\Album;
use App\Models\Song;

class AlbumSongsTileWidget extends Widget
{
    protected static string $view = 'filament.widgets.album-songs-tile-widget';
    public Album $album;
    public int $gridSize = 3;
    public string $viewMode = 'grid';

    public function mount(Album $album)
    {
        $this->album = $album;
    }

    public function getSongsProperty()
    {
        return Song::where('album_id', $this->album->id)->get();
    }

    public static function canView(): bool
    {
        return true;
    }
}
