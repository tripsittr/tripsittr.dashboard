<?php

namespace App\Filament\Resources\DirectMessageThreadResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\DirectMessage;
use Filament\Tables;

class DirectMessageList extends Widget
{
    protected static string $view = 'filament.resources.direct-message-thread-resource.widgets.direct-message-list';

    public $threadId;

    public function mount($threadId)
    {
        $this->threadId = $threadId;
    }

    public function getMessages()
    {
        return DirectMessage::where('thread_id', $this->threadId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
