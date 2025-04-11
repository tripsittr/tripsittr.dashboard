<?php

namespace App\Filament\Infolists\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\View;

class AudioEntry extends Entry {
    protected string $view = 'filament.components.infolists.audio-entry';

    protected string | Closure | null $audioUrl = null;
    protected array | Closure | null $waveformData = null;
    protected array | Closure | null $metadata = null;

    public function audioUrl(string | Closure | null $audioUrl): static {
        $this->audioUrl = $audioUrl;

        return $this;
    }

    public function waveformData(array | Closure | null $waveformData): static {
        $this->waveformData = $waveformData;

        return $this;
    }

    public function metadata(array | Closure | null $metadata): static {
        $this->metadata = $metadata;

        return $this;
    }

    public function getAudioUrl(): ?string {
        return $this->evaluate($this->audioUrl);
    }

    public function getWaveformData(): ?array {
        return $this->evaluate($this->waveformData);
    }

    public function getMetadata(): ?array {
        return $this->evaluate($this->metadata);
    }

    public function getActions(): array {
        return [
            Action::make('viewMetadata')
                ->label('View Metadata')
                ->modalHeading('Song Metadata')
                ->modalContent(View::make('filament.components.infolists.audio-metadata', ['metadata' => $this->getMetadata()]))
                ->modalWidth('lg'),
        ];
    }

    public function viewMetadata() {
        // Logic to handle viewing metadata
    }
}
