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
        // Increase execution time and memory limit for video processing
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'nullable|file|mimes:mp4,mkv,avi,mov',
            'youtube_link' => 'nullable|url',
        ]);

        if (!$request->file && !$request->youtube_link) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please upload a file or provide a YouTube link.'
                ]);
            }
            return back()->withErrors(['error' => 'Please upload a file or provide a YouTube link.']);
        }

        $audioPath = null;
        try {
            // Handle file upload
            if ($request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                // Ensure videos directory exists
                $videosDir = storage_path('app/videos');
                if (!file_exists($videosDir)) {
                    mkdir($videosDir, 0755, true);
                }
                $originalName = $uploadedFile->getClientOriginalName();
                $extension = $uploadedFile->getClientOriginalExtension();
                if (empty($extension)) {
                    $extension = 'mp4';
                }
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $videoPath = $videosDir . '/' . $filename;
                $uploadedFile->move($videosDir, $filename);
                if (!file_exists($videoPath)) {
                    throw new \Exception('File was not saved properly at: ' . $videoPath);
                }
                $audioPath = $this->extractFromVideo($videoPath);
            }
            // Handle YouTube link
            if ($request->youtube_link) {
                // ...existing code...
                $audioPath = $this->downloadAudioFromYouTube($request->youtube_link);
            }
            if (!$audioPath || !file_exists($audioPath)) {
                throw new \Exception('Audio extraction failed - output file not created');
            }
            // AJAX: return JSON with download URL
            if ($request->ajax()) {
                // Make a public URL for the audio file
                $publicPath = str_replace(storage_path('app/public'), '', $audioPath);
                $downloadUrl = asset('storage' . $publicPath);
                return response()->json([
                    'success' => true,
                    'download_url' => $downloadUrl
                ]);
            }
            // Non-AJAX: return file download
            return response()->download($audioPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Audio extraction process failed', [
                'error' => $e->getMessage(),
                'request_data' => [
                    'has_file' => $request->hasFile('file'),
                    'youtube_link' => $request->youtube_link
                ]
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to extract audio: ' . $e->getMessage()
                ]);
            }
            return back()->withErrors(['error' => 'Failed to extract audio: ' . $e->getMessage()]);
        }
    }
    private function extractFromVideo($videoPath)
    {
        // Validate input file exists and is readable
        if (!file_exists($videoPath)) {
            throw new \Exception("Video file not found: $videoPath");
        }

        if (!is_readable($videoPath)) {
            throw new \Exception("Video file is not readable: $videoPath");
        }

        $fileSize = filesize($videoPath);
        if ($fileSize === 0) {
            throw new \Exception("Video file is empty: $videoPath");
        }

        Log::info('Processing video file', [
            'path' => $videoPath,
            'size' => $fileSize,
            'readable' => is_readable($videoPath)
        ]);

        // Ensure audio directory exists
        $audioDir = storage_path('app/public/audio');
        if (!file_exists($audioDir)) {
            mkdir($audioDir, 0755, true);
        }

        $audioPath = storage_path('app/public/audio/audio_' . time() . '.mp3');

        // Try to find ffmpeg and ffprobe binaries in common locations
        $possiblePaths = [
            'ffmpeg' => [
                '/usr/local/bin/ffmpeg',
                '/usr/bin/ffmpeg',
                '/opt/homebrew/bin/ffmpeg',
                '/usr/homebrew/bin/ffmpeg',
                'ffmpeg' // Try system PATH
            ],
            'ffprobe' => [
                '/usr/local/bin/ffprobe',
                '/usr/bin/ffprobe',
                '/opt/homebrew/bin/ffprobe',
                '/usr/homebrew/bin/ffprobe',
                'ffprobe' // Try system PATH
            ]
        ];

        $ffmpegPath = null;
        $ffprobePath = null;

        // Find working ffmpeg binary
        foreach ($possiblePaths['ffmpeg'] as $path) {
            if (file_exists($path) || $path === 'ffmpeg') {
                $output = [];
                $returnCode = 0;
                exec("$path -version 2>&1", $output, $returnCode);
                if ($returnCode === 0) {
                    $ffmpegPath = $path;
                    break;
                }
            }
        }

        // Find working ffprobe binary
        foreach ($possiblePaths['ffprobe'] as $path) {
            if (file_exists($path) || $path === 'ffprobe') {
                $output = [];
                $returnCode = 0;
                exec("$path -version 2>&1", $output, $returnCode);
                if ($returnCode === 0) {
                    $ffprobePath = $path;
                    break;
                }
            }
        }

        if (!$ffmpegPath || !$ffprobePath) {
            throw new \Exception('FFmpeg or FFprobe not found. Please install FFmpeg on your system.');
        }

        // Test ffprobe on the file first
        $probeOutput = [];
        $probeReturnCode = 0;
        exec("$ffprobePath \"$videoPath\" 2>&1", $probeOutput, $probeReturnCode);

        if ($probeReturnCode !== 0) {
            Log::error('FFprobe test failed', [
                'video_path' => $videoPath,
                'ffprobe_path' => $ffprobePath,
                'return_code' => $probeReturnCode,
                'output' => implode("\n", $probeOutput)
            ]);
            throw new \Exception('Video file appears to be corrupted or in an unsupported format. FFprobe output: ' . implode("\n", $probeOutput));
        }

        try {
            $ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries'  => $ffmpegPath,
                'ffprobe.binaries' => $ffprobePath,
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            $video = $ffmpeg->open($videoPath);

            // Create MP3 format with specific settings
            $format = new FFMpeg\Format\Audio\Mp3();
            $format->setAudioChannels(2);
            $format->setAudioKiloBitrate(128);

            $video->save($format, $audioPath);

            // Verify the output file was created
            if (!file_exists($audioPath) || filesize($audioPath) === 0) {
                throw new \Exception('Audio extraction completed but output file is missing or empty');
            }

            Log::info('Audio extraction successful', [
                'input' => $videoPath,
                'output' => $audioPath,
                'output_size' => filesize($audioPath)
            ]);

            return $audioPath;
        } catch (\Exception $e) {
            Log::error('FFmpeg extraction failed', [
                'error' => $e->getMessage(),
                'ffmpeg_path' => $ffmpegPath,
                'ffprobe_path' => $ffprobePath,
                'video_path' => $videoPath,
                'video_exists' => file_exists($videoPath),
                'video_size' => file_exists($videoPath) ? filesize($videoPath) : 'N/A'
            ]);
            throw new \Exception('Failed to extract audio from video: ' . $e->getMessage());
        }
    }


    private function downloadFromYouTube($youtubeLink)
    {
        Log::info('Starting YouTube download', ['url' => $youtubeLink]);

        // Ensure output directory exists
        $outputDir = storage_path('app/videos');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Try to find yt-dlp or youtube-dl binary
        $possibleBinaries = [
            '/usr/local/bin/yt-dlp',
            '/usr/bin/yt-dlp',
            'yt-dlp',
            '/usr/local/bin/youtube-dl',
            '/usr/bin/youtube-dl',
            'youtube-dl'
        ];

        $binary = null;
        foreach ($possibleBinaries as $path) {
            $output = [];
            $returnCode = 0;
            exec("$path --version 2>&1", $output, $returnCode);
            if ($returnCode === 0) {
                $binary = $path;
                Log::info('Found YouTube downloader', ['binary' => $binary]);
                break;
            }
        }

        if (!$binary) {
            throw new \Exception('yt-dlp or youtube-dl not found. Please install one of these tools.');
        }

        // Generate unique filename
        $timestamp = time();
        $outputTemplate = $outputDir . "/youtube_video_$timestamp.%(ext)s";

        // Download audio-only to reduce file size and processing time
        $command = "$binary -f 'bestaudio[ext=m4a]/bestaudio/best' --audio-quality 5 -o \"$outputTemplate\" \"$youtubeLink\"";

        Log::info('Executing YouTube download command', ['command' => $command]);

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('YouTube download failed', [
                'command' => $command,
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            throw new \Exception('Failed to download video from YouTube. Error: ' . implode("\n", $output));
        }

        // Find the downloaded file
        $downloadedFiles = glob($outputDir . "/youtube_video_$timestamp.*");

        if (empty($downloadedFiles)) {
            Log::error('No files found after download', [
                'search_pattern' => $outputDir . "/youtube_video_$timestamp.*",
                'directory_contents' => scandir($outputDir)
            ]);
            throw new \Exception('Video download completed but file not found');
        }

        $videoPath = $downloadedFiles[0];

        if (!file_exists($videoPath)) {
            throw new \Exception("Downloaded video file not found: $videoPath");
        }

        Log::info('YouTube download successful', [
            'video_path' => $videoPath,
            'file_size' => filesize($videoPath)
        ]);

        return $videoPath;
    }

    private function downloadAudioFromYouTube($youtubeLink)
    {
        Log::info('Starting direct YouTube audio download', ['url' => $youtubeLink]);

        // Ensure output directory exists
        $outputDir = storage_path('app/public/audio');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Try to find yt-dlp or youtube-dl binary
        $possibleBinaries = [
            '/usr/local/bin/yt-dlp',
            '/usr/bin/yt-dlp',
            'yt-dlp',
            '/usr/local/bin/youtube-dl',
            '/usr/bin/youtube-dl',
            'youtube-dl'
        ];

        $binary = null;
        foreach ($possibleBinaries as $path) {
            $output = [];
            $returnCode = 0;
            exec("$path --version 2>&1", $output, $returnCode);
            if ($returnCode === 0) {
                $binary = $path;
                break;
            }
        }

        if (!$binary) {
            throw new \Exception('yt-dlp or youtube-dl not found. Please install one of these tools.');
        }

        // Generate unique filename pattern (let yt-dlp choose the extension)
        $timestamp = time();
        $outputTemplate = $outputDir . "/youtube_audio_$timestamp.%(ext)s";

        // Download audio with flexible format - let yt-dlp choose the best available
        $command = "$binary -x --audio-format best --audio-quality 128K -o \"$outputTemplate\" \"$youtubeLink\" 2>&1";

        Log::info('Executing YouTube audio download', ['command' => $command]);

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        // Log the complete output regardless of return code
        Log::info('YouTube download command completed', [
            'command' => $command,
            'return_code' => $returnCode,
            'output' => implode("\n", $output)
        ]);

        // Find the downloaded audio file (could be any audio format)
        $downloadedFiles = glob($outputDir . "/youtube_audio_$timestamp.*");

        // Also try to find files that might have been downloaded with yt-dlp's own naming
        if (empty($downloadedFiles)) {
            // Check for any recently created files in the directory
            $allFiles = glob($outputDir . "/*");
            $recentFiles = [];
            $currentTime = time();

            foreach ($allFiles as $file) {
                $fileTime = filemtime($file);
                // If file was created in the last 60 seconds, consider it our download
                if (($currentTime - $fileTime) < 60) {
                    $recentFiles[] = $file;
                }
            }

            if (!empty($recentFiles)) {
                $downloadedFiles = $recentFiles;
                Log::info('Found recent files in audio directory', [
                    'files' => $downloadedFiles
                ]);
            }
        }

        if (empty($downloadedFiles)) {
            Log::error('No audio files found after download', [
                'search_pattern' => $outputDir . "/youtube_audio_$timestamp.*",
                'directory_contents' => scandir($outputDir),
                'return_code' => $returnCode,
                'command_output' => implode("\n", $output)
            ]);

            // If the command failed, throw the error
            if ($returnCode !== 0) {
                throw new \Exception('Failed to download audio from YouTube. Error: ' . implode("\n", $output));
            } else {
                throw new \Exception('Audio download completed but file not found');
            }
        }

        $downloadedAudioPath = $downloadedFiles[0];

        if (!file_exists($downloadedAudioPath)) {
            throw new \Exception("Downloaded audio file not found: $downloadedAudioPath");
        }

        Log::info('YouTube audio download successful', [
            'audio_path' => $downloadedAudioPath,
            'file_size' => filesize($downloadedAudioPath),
            'file_extension' => pathinfo($downloadedAudioPath, PATHINFO_EXTENSION)
        ]);

        // If the downloaded file is already MP3, return it directly
        $extension = strtolower(pathinfo($downloadedAudioPath, PATHINFO_EXTENSION));
        if ($extension === 'mp3') {
            return $downloadedAudioPath;
        }

        // If it's not MP3, convert it using FFmpeg
        Log::info('Converting downloaded audio to MP3', [
            'source_file' => $downloadedAudioPath,
            'source_extension' => $extension
        ]);

        $finalMp3Path = $outputDir . "/youtube_audio_$timestamp.mp3";
        $convertedPath = $this->convertToMp3($downloadedAudioPath, $finalMp3Path);

        // Clean up the original downloaded file
        if (file_exists($downloadedAudioPath) && $downloadedAudioPath !== $convertedPath) {
            unlink($downloadedAudioPath);
        }

        return $convertedPath;
    }

    private function convertToMp3($inputPath, $outputPath)
    {
        Log::info('Converting audio file to MP3', [
            'input' => $inputPath,
            'output' => $outputPath
        ]);

        // Find FFmpeg binary
        $possiblePaths = [
            '/usr/local/bin/ffmpeg',
            '/usr/bin/ffmpeg',
            '/opt/homebrew/bin/ffmpeg',
            '/usr/homebrew/bin/ffmpeg',
            'ffmpeg'
        ];

        $ffmpegPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $path === 'ffmpeg') {
                $output = [];
                $returnCode = 0;
                exec("$path -version 2>&1", $output, $returnCode);
                if ($returnCode === 0) {
                    $ffmpegPath = $path;
                    break;
                }
            }
        }

        if (!$ffmpegPath) {
            throw new \Exception('FFmpeg not found. Cannot convert audio to MP3.');
        }

        // Convert to MP3 using FFmpeg
        $command = "$ffmpegPath -i \"$inputPath\" -acodec libmp3lame -ab 128k \"$outputPath\" -y";

        Log::info('Executing audio conversion', ['command' => $command]);

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('Audio conversion failed', [
                'command' => $command,
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            throw new \Exception('Failed to convert audio to MP3. Error: ' . implode("\n", $output));
        }

        if (!file_exists($outputPath)) {
            throw new \Exception("Audio conversion completed but MP3 file not found: $outputPath");
        }

        Log::info('Audio conversion successful', [
            'mp3_path' => $outputPath,
            'file_size' => filesize($outputPath)
        ]);

        return $outputPath;
    }
}
