<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\CanSpanColumns;
use Filament\Forms\Components\Concerns\HasChildComponents;
use Closure;

class Stack extends Component
{
    use CanSpanColumns;
    use HasChildComponents; // use Filament's native child component handling

    protected string $view = 'forms.components.stack';

    /**
     * @param array<Component>|Closure $schema
     */
    final public function __construct(array|Closure $schema = [])
    {
        $this->schema($schema);
    }

    // Provide API parity: allow Stack::make([...]) like other layout components
    public static function make(array|Closure $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();
        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpan('full');
    }
}
