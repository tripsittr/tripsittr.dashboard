<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class EdgeEditor extends Field
{
    // Deprecated stub: the original EdgeEditor UI has been removed. This stub
    // remains to avoid runtime errors if any old compiled templates reference
    // the class. It intentionally renders an empty view.
    protected string $view = 'filament.forms.components.edge-editor-deprecated';

    protected function setUp(): void
    {
        parent::setUp();

        // Keep a safe default shape to match previous expectations.
        $this->default(fn () => [
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'left' => 0,
        ]);
    }
}
