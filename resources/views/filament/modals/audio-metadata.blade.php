@props(['record'])

@php
    $metadata = [
        'duration' => $record->duration,
        'bitrate' => $record->bitrate,
        'sample_rate' => $record->sample_rate,
        'codec' => $record->codec,
        'format' => $record->format,
        'channels' => $record->channels,
        'file_size' => $record->file_size,
        'file_extension' => $record->file_extension,
        'bit_depth' => $record->bit_depth,
        'compression_ratio' => $record->compression_ratio,
        'encoder' => $record->encoder,
        'channel_mode' => $record->channel_mode,
        'mode_extension' => $record->mode_extension,
        'audio_data_offset' => $record->audio_data_offset,
        'audio_data_length' => $record->audio_data_length,
        'track_number' => $record->track_number,
        'disc_number' => $record->disc_number,
        'mime_type' => $record->mime_type,
        'album_title' => $record->album_title,
        'year' => $record->year,
        'key_signature' => $record->key_signature,
        'publisher' => $record->publisher,
        'mood' => $record->mood,
        'bpm' => $record->bpm,
        'composer_notes' => $record->composer_notes,
        'genre_extended' => $record->genre_extended,
        'language' => $record->language,
        ];
@endphp

<div class="gap-3">
    @foreach ($metadata as $key => $value)
        @if ($value !== null)
            <div class="flex items-center mx-6 gap-3 mt-3">
                <strong>{{ $key }}:</strong> 
                <x-filament::badge style=" height: 20px; width: max-content;">
                    {{ $value }}
                </x-filament::badge>
            </div>
        @endif
    @endforeach
</div>

<!-- Metadata -->
<!-- `duration`, `bitrate`, `sample_rate`, `codec`, `format`, `channels``file_size`, `file_extension`, `bit_depth`, `compression_ratio`, `encoder`, `channel_mode`, `mode_extension`, `audio_data_offset`, `audio_data_length`, `mime_type`, `track_number`, `disc_number`, `album_title`, `year`, `bpm`, `mood`, `key_signature`, `publisher`, `copyright`, `composer_notes`, `genre_extended`, `language`, `album_artist`, `original_release_date`, `comment`, `lyrics`, `file_owner`, `encoded_by`, `performer_info`, `conductor`, `remixer`, `mix_artist`, `dj_mixer`, `author`, `grouping`, `subtitle` -->
