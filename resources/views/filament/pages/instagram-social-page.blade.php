<x-filament-panels::page>
    <div class="instagram-feed">
        <h2>Instagram Feed</h2>
        <div class="posts">
            @foreach ($posts as $post)
                <div class="post">
                    <img src="{{ $post['media_url'] }}" alt="Instagram Post">
                    <p>{{ $post['caption'] ?? 'No caption available' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add some basic styling -->
    <style>
        .instagram-feed .posts {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }
        .instagram-feed .post {
            border: 1px solid #ddd;
            padding: 8px;
            max-width: 200px;
        }
        .instagram-feed .post img {
            max-width: 100%;
            height: auto;
        }
    </style>
</x-filament-panels::page>
