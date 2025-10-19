@php
/** @var \App\Filament\Infolists\Components\AudioEntry $entry */
$record = $entry->getRecord();
$audioUrl = $record ? $entry->getAudioUrl() : null;
$isValid = $entry->isValidAudioUrl($audioUrl);
$config = $entry->getPlayerConfig();
$metadata = $entry->getMetadata();
$primaryColor = $config['primaryColor'] ?? 'indigo-500';
$bgColor = $config['backgroundColor'] ?? 'indigo-100 dark:indigo-900/30';
$modalId = 'asudio-meta-'.($record->id ?? uniqid());
@endphp


<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div x-data="audioPlayer({
			url: @js($audioUrl),
			autoplay: @js($config['autoplay']),
			defaultVolume: @js($config['defaultVolume']),
			defaultSpeed: @js($config['defaultSpeed']),
			metadata: @js($metadata),
		})" x-init="init()" class="space-y-3 ts-audio">
        @if(!$isValid)
        <div class="text-sm text-gray-500 dark:text-gray-400 italic">No valid audio file found.</div>
        @endif
        @if($isValid)
        <div class="flex items-center gap-3 w-full">
            <!-- Left: play/pause -->
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" @click="toggle()" aria-label="Toggle Play"
                    :aria-pressed="playing ? 'true' : 'false'" class="ts-btn-play rounded-full"
                    style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;line-height:0;padding:0;">
                    <svg x-show="!playing" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        aria-hidden="true" style="width:14px;height:14px;display:block;">
                        <path
                            d="M5.25 5.653c0-1.427 1.54-2.33 2.78-1.64l9.05 4.997c1.421.785 1.421 2.808 0 3.593l-9.05 4.997c-1.24.685-2.78-.213-2.78-1.64V5.653z" />
                    </svg>
                    <svg x-show="playing" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        aria-hidden="true" style="width:14px;height:14px;display:block;">
                        <path
                            d="M6.75 5.25a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H7.5a.75.75 0 01-.75-.75V5.25zM13.5 5.25a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H14.25a.75.75 0 01-.75-.75V5.25z" />
                    </svg>
                </button>

            </div>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Right: time, settings, metadata -->
            <div class="relative flex items-center gap-2 shrink-0 ml-auto">
                @if($config['showTimeDisplay'])
                <div class="text-xs font-mono tabular-nums text-gray-700 dark:text-gray-300">
                    <span x-text="formatTime(currentTime)"></span>
                    <span class="text-gray-400">/</span>
                    <span x-text="formatTime(duration)"></span>
                </div>
                @endif

                <!-- Settings popover: speed + volume -->
                <div class="relative" x-data="{ settingsOpen: false }">
                    <x-filament::icon-button icon="heroicon-o-cog-6-tooth" size="xs" color="gray"
                        x-on:click="settingsOpen = !settingsOpen" />
                    <div x-show="settingsOpen" x-transition @click.outside="settingsOpen=false"
                        class="absolute right-0 z-20 mt-2 w-56 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg p-3 space-y-3">
                        @if($config['showSpeedControl'])
                        <div class="space-y-1">
                            <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Speed
                            </div>
                            <select x-model.number="playbackRate" @change="applySpeed()"
                                class="w-full text-xs px-2 py-1 border rounded-md bg-white text-gray-900 border-gray-300 focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600">
                                <template x-for="rate in [0.5,0.75,1,1.25,1.5,2]" :key="rate">
                                    <option :value="rate" x-text="rate.toFixed(2)+'x'"></option>
                                </template>
                            </select>
                        </div>
                        @endif
                        @if($config['showVolumeControl'])
                        <div class="space-y-1">
                            <div class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Volume
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-speaker-x-mark class="w-4 h-4 text-gray-500 dark:text-gray-300" />
                                <input type="range" min="0" max="1" step="0.01" x-model.number="volume"
                                    @input="applyVolume()" class="flex-1"
                                    style="accent-color: var(--fi-color-primary-600, var(--fi-color-primary-500, #6366f1));" />
                                <x-heroicon-s-speaker-wave class="w-4 h-4 text-gray-500 dark:text-gray-300" />
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($metadata)
                <div>
                    <x-filament::button size="xs" color="gray"
                        x-on:click="$dispatch('open-modal',{id:'{{ $modalId }}'})">
                        Metadata</x-filament::button>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="relative w-full h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden"
            :class="{ 'cursor-pointer': isSeekable(), 'cursor-not-allowed opacity-60': !isSeekable() }"
            x-ref="progressBar" @click.prevent="isSeekable() && seekAtProgressEvent($event)"
            aria-label="Playback progress" role="progressbar" :aria-valuemin="0" :aria-valuemax="100"
            :aria-valuenow="progressPercent()">
            <div class="absolute inset-y-0 left-0"
                :style="{ width: progressPercent() + '%', background: progressColor() }"></div>
        </div>

        @if($config['showControls'] && $isValid)
        <audio x-ref="audio" :src="url" x-bind:autoplay="autoplay ? true : null" class="w-full mt-1 hidden"></audio>
        @endif

        @if($metadata)
        <x-filament::modal id="{{ $modalId }}" width="lg">
            <x-slot name="heading">Audio Metadata</x-slot>
            <div class="space-y-4">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
                    @foreach($metadata as $k => $v)
                    <div>
                        <dt class="font-medium text-gray-700 dark:text-gray-300">{{ Str::headline($k) }}</dt>
                        <dd class="mt-0.5 text-gray-600 dark:text-gray-400 break-all">{{ is_scalar($v) ? $v :
                            json_encode($v) }}</dd>
                    </div>
                    @endforeach
                </dl>
                <div class="flex justify-end">
                    <x-filament::button color="primary" x-data @click="$dispatch('close-modal',{id:'{{ $modalId }}'})">
                        Close</x-filament::button>
                </div>
            </div>
        </x-filament::modal>
        @endif
    </div>
</x-dynamic-component>

<script>
    document.addEventListener('alpine:init', () => {
		window.audioPlayer = (cfg) => ({
            url: cfg.url,
            autoplay: cfg.autoplay,
            defaultVolume: cfg.defaultVolume ?? 1,
            defaultSpeed: cfg.defaultSpeed ?? 1,
            metadata: cfg.metadata || null,
            showMeta: false,
            playing: false,
            duration: 0,
            currentTime: 0,
            volume: cfg.defaultVolume ?? 1,
            playbackRate: cfg.defaultSpeed ?? 1,
			init() {
                const audio = this.$refs.audio;
                if (!audio) return;
                audio.addEventListener('loadedmetadata', () => {
                    this.duration = audio.duration || 0;
                });
                let _lastUpdate = 0;
                audio.addEventListener('timeupdate', () => {
                    this.currentTime = audio.currentTime;
                    const now = performance.now();
                    if (now - _lastUpdate > 100) { // throttle UI updates
                        _lastUpdate = now;
                    }
                });
                audio.addEventListener('ended', () => { this.playing = false; });
                audio.volume = this.volume;
                audio.playbackRate = this.playbackRate;
                if (this.autoplay) this.toggle();
            },
			toggle() {
				const audio = this.$refs.audio; if (!audio) return;
				if (audio.paused) { audio.play(); this.playing = true; }
				else { audio.pause(); this.playing = false; }
			},
			applyVolume() { const a = this.$refs.audio; if (a) a.volume = this.volume; },
			applySpeed() { const a = this.$refs.audio; if (a) a.playbackRate = this.playbackRate; },
			formatTime(t) { if (!t) return '0:00'; const m = Math.floor(t/60); const s = Math.floor(t%60).toString().padStart(2,'0'); return `${m}:${s}`; },
            progressRatio() { return (this.duration > 0) ? Math.max(0, Math.min(1, this.currentTime / this.duration)) : 0; },
            progressPercent() { return Math.round(this.progressRatio() * 100); },
            progressColor() {
                // TripSittr brand red to match play button
                return '#c65c5d';
            },
            isSeekable() { return !!this.$refs.audio && this.duration > 0; },
            seekAtProgressEvent(evt) {
                const bar = this.$refs.progressBar; if (!bar) return;
                const rect = bar.getBoundingClientRect();
                const x = evt.clientX - rect.left;
                const ratio = Math.max(0, Math.min(1, x / rect.width));
                const a = this.$refs.audio; if (!a || !this.duration) return;
                a.currentTime = ratio * this.duration;
                this.currentTime = a.currentTime;
            }
		})
	})
</script>

<style>
    .ts-btn-play {
        /* Compact filled pill with brand red */
        background: #c65c5d;
        color: #000;
        /* icons black in light mode */
        border: 0;
        transition: background-color .15s ease;
    }

    /* Dark mode: keep white icons for contrast */
    .dark .ts-btn-play {
        color: #fff;
    }

    .ts-btn-play:hover {
        background: #b14f50;
        /* slightly darker */
    }

    .ts-btn-play:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(198, 92, 93, .25);
    }
</style>