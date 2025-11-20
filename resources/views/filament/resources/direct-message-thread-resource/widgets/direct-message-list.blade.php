@php use Illuminate\Support\Facades\Auth; @endphp
<div class="space-y-4">
    @foreach ($this->getMessages() as $message)
    <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
        <div
            class="max-w-lg p-3 rounded-lg shadow {{ $message->sender_id === Auth::id() ? 'bg-blue-100 text-right' : 'bg-gray-100 text-left' }}">
            <div class="text-sm text-gray-700">{{ $message->body }}</div>
            @if ($message->attachment_path)
            <div class="mt-2">
                <a href="{{ $message->attachment_url }}" class="text-blue-600 underline" target="_blank">Download
                    Attachment</a>
            </div>
            @endif
            <div class="text-xs text-gray-400 mt-1">{{ $message->created_at->format('M d, Y H:i') }}</div>
        </div>
    </div>
    @endforeach
</div>