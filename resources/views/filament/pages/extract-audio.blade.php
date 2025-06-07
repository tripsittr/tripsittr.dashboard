<!-- filepath: /Users/tripsittr/Documents/GitHub/tripsittr.dashboard/resources/views/filament/pages/extract-audio.blade.php -->
<x-filament::page>
    <form method="POST" action="{{ route('extract-audio.process') }}" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">Upload Video File:</label>
            <input type="file" name="file" id="file" accept="video/mp4,video/mkv,video/avi">
        </div>

        <div>
            <label for="youtube_link">Or Enter YouTube Link:</label>
            <input type="url" name="youtube_link" id="youtube_link" placeholder="https://www.youtube.com/watch?v=example">
        </div>

        <div>
            <button type="submit">Extract Audio</button>
        </div>
    </form>
</x-filament::page>