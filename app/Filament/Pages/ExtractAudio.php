<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Http\Request;
use FFMpeg;
use Illuminate\Support\Facades\Log;
use YoutubeDl\YoutubeDl;
use YoutubeDl\Exception\YoutubeDlException;
use YoutubeDl\Options;
use FFMpeg\FFProbe;
use PHPUnit\Runner\HookMethod;

class ExtractAudio extends Page
{
    protected static string $view = 'filament.pages.extract-audio';
    protected static ?string $navigationGroup = 'Extras';
    protected static ?string $navigationLabel = 'Extract Audio';


    public function extractAudio(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:mp4,mkv,avi,mov',
            'youtube_link' => 'nullable|url',
        ]);

        if (!$request->file && !$request->youtube_link) {
            return back()->withErrors(['error' => 'Please upload a file or provide a YouTube link.']);
        }

        $audioPath = null;

        // Handle file upload
        if ($request->file) {
            $videoPath = $request->file('file')->store('videos');
            $audioPath = $this->extractFromVideo(storage_path('app/' . $videoPath));
        }

        // Handle YouTube link
        if ($request->youtube_link) {
            $videoPath = $this->downloadFromYouTube($request->youtube_link);
            $audioPath = $this->extractFromVideo($videoPath);
        }

        return response()->download($audioPath)->deleteFileAfterSend(true);
    }

    private function extractFromVideo($videoPath)
    {
        $audioPath = storage_path('app/public/audio_' . time() . '.mp3');

        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/homebrew/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/homebrew/bin/ffprobe'
        ]);
        $video = $ffmpeg->open($videoPath);
        $video->save(new FFMpeg\Format\Audio\Mp3(), $audioPath);

        return $audioPath;
    }


    private function downloadFromYouTube($youtubeLink)
    {
        $outputPath = storage_path('app/public/audio/'); // Directory to save the audio files

        $yt = new YoutubeDl();

        try {
            $collection = $yt->download(
                Options::create()
                    ->downloadPath($outputPath) // Set the download path
                    ->extractAudio(true)        // Extract audio only
                    ->audioFormat('mp3')        // Save as MP3
                    ->audioQuality('0')         // Best audio quality
                    ->output('%(title)s.%(ext)s') // Output file pattern
                    ->url($youtubeLink)         // YouTube link
            );

            foreach ($collection->getVideos() as $video) {
                if ($video->getError() !== null) {
                    Log::error("Error downloading video: {$video->getError()}.");
                    throw new \Exception('Failed to download video from YouTube.');
                } else {
                    return $video->getFile(); // Return the path to the downloaded audio file
                }
            }
        } catch (YoutubeDlException $e) {
            Log::error('yt-dlp error: ' . $e->getMessage());
            throw new \Exception('Failed to download video from YouTube.');
        }
    }
}
