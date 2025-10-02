<x-filament::page>
    <div>
        <!-- Page Header -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    @svg('heroicon-o-musical-note', 'w-6 h-6 text-primary-600')
                    Audio Extractor
                </div>
            </x-slot>
            <h3 class="text-sm font-semibold text-gray-300 dark:text-white">Extract audio from video files or
                YouTube links and download as MP3</h3>
        </x-filament::section>
        <br>
        <!-- Main Form -->
        <x-filament::section>
            <form method="POST" action="{{ route('extract-audio.process') }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                <!-- File Upload Section -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-cloud-arrow-up', 'w-5 h-5 text-primary-600')
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Video File</h3>
                    </div>
                    <div class="fi-fo-field-wrp">
                        <div class="grid gap-y-2">
                            <label for="file"
                                class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">Video
                                File</label>
                            <div class="fi-fo-file-input relative">
                                <input type="file" name="file" id="file"
                                    accept="video/mp4,video/mkv,video/avi,video/mov"
                                    class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] file:me-4 file:rounded-lg file:border-0 file:bg-gray-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-950 file:hover:bg-gray-100 dark:file:bg-white/5 dark:file:text-white dark:file:hover:bg-white/10" />
                            </div>
                            <p class="fi-fo-field-wrp-hint text-sm text-gray-500 dark:text-gray-400">Supported
                                formats: MP4, MKV, AVI, MOV (Max: 100MB)</p>
                        </div>
                    </div>
                </div>
                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500 dark:bg-gray-900 dark:text-gray-400">OR</span>
                    </div>
                </div>
                <!-- YouTube Link Section -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-link', 'w-5 h-5 text-red-600')
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">YouTube Link</h3>
                    </div>
                    <div class="fi-fo-field-wrp">
                        <div class="grid gap-y-2">
                            <label for="youtube_link"
                                class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">YouTube
                                URL</label>
                            <div
                                class="fi-input-wrp flex rounded-lg shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus-within:ring-2 focus-within:ring-primary-600 dark:ring-white/20 dark:focus-within:ring-primary-500">
                                <div class="flex items-center ps-3">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                </div>
                                <input type="url" name="youtube_link" id="youtube_link"
                                    placeholder="https://www.youtube.com/watch?v=example"
                                    class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)]" />
                            </div>
                            <p class="fi-fo-field-wrp-hint text-sm text-gray-500 dark:text-gray-400">Paste a YouTube
                                video URL to extract its audio</p>
                        </div>
                    </div>
                </div>
                <!-- Error Display -->
                @if ($errors->any())
                <div
                    class="fi-banner fi-color-danger rounded-xl bg-danger-50 p-4 ring-1 ring-danger-600/10 dark:bg-danger-400/10 dark:ring-danger-400/30">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            @svg('heroicon-o-exclamation-triangle', 'fi-banner-icon h-5 w-5 text-danger-600
                            dark:text-danger-400')
                        </div>4
                        <div>
                            <h3 class="fi-banner-heading text-sm font-medium text-danger-700 dark:text-danger-300">
                                Please fix the following errors:</h3>
                            <div class="mt-2">
                                <ul
                                    class="list-disc list-inside space-y-1 text-sm text-danger-600 dark:text-danger-400">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <!-- Submit Button -->
                <div class="flex justify-end items-center gap-3">
                    <!-- Loading Spinner (Hidden by default) -->
                    <div id="loading-spinner" class="hidden flex items-center gap-2 text-primary-600">
                        <div class="animate-spin">
                            @svg('heroicon-o-arrow-path', 'w-5 h-5')
                        </div>
                        <span id="loading-text" class="text-sm font-medium">Processing...</span>
                    </div>
                    <x-filament::button type="submit" size="lg" icon="heroicon-o-arrow-down-tray" id="submit-btn">
                        Extract Audio</x-filament::button>
                </div>
            </form>
        </x-filament::section>
        <br>
        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="fi-section-content-ctn bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @svg('heroicon-o-cloud-arrow-up', 'h-6 w-6 text-primary-600 dark:text-primary-400')
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">File Upload</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload video files directly from
                            your device. Supported formats include MP4, MKV, AVI, and MOV with high-quality audio
                            extraction.</p>
                    </div>
                </div>
            </div>
            <div
                class="fi-section-content-ctn bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @svg('heroicon-o-globe-alt', 'h-6 w-6 text-primary-600 dark:text-primary-400')
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">YouTube Integration</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Extract audio directly from YouTube
                            videos by pasting the video URL. Fast processing with automatic format detection.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingSpinner = document.getElementById('loading-spinner');
        const submitBtn = document.getElementById('submit-btn');
        const form = document.querySelector('form');
        const loadingText = document.getElementById('loading-text');
        // Container for dynamic messages/download
        let resultContainer = document.getElementById('ajax-result');
        if (!resultContainer) {
            resultContainer = document.createElement('div');
            resultContainer.id = 'ajax-result';
            resultContainer.className = 'mt-4';
            form.parentNode.insertBefore(resultContainer, form.nextSibling);
        }
        // Always hide spinner and enable button on page load
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (submitBtn) submitBtn.disabled = false;
        function showLoadingSpinner() {
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = true;
        }
        function hideLoadingSpinner() {
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = false;
        }
        function updateLoadingText(hasFile, hasYouTubeLink) {
            if (loadingText) {
                if (hasYouTubeLink && !hasFile) {
                    loadingText.textContent = 'Downloading from YouTube...';
                } else if (hasFile) {
                    loadingText.textContent = 'Processing video file...';
                } else {
                    loadingText.textContent = 'Processing...';
                }
            }
        }
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                resultContainer.innerHTML = '';
                const hasFile = document.getElementById('file')?.files?.length > 0;
                const hasYouTubeLink = document.getElementById('youtube_link')?.value?.trim() !== '';
                if (!hasFile && !hasYouTubeLink) return;
                showLoadingSpinner();
                updateLoadingText(hasFile, hasYouTubeLink);
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingSpinner();
                    if (data.success && data.download_url) {
                        resultContainer.innerHTML = `<div class='fi-banner fi-color-success rounded-xl bg-success-50 p-4 ring-1 ring-success-600/10 dark:bg-success-400/10 dark:ring-success-400/30'><h3 class='fi-banner-heading text-sm font-medium text-success-700 dark:text-success-300'>Audio extracted! <a href='${data.download_url}' class='underline text-primary-600' download>Download MP3</a></h3></div>`;
                    } else if (data.error) {
                        resultContainer.innerHTML = `<div class='fi-banner fi-color-danger rounded-xl bg-danger-50 p-4 ring-1 ring-danger-600/10 dark:bg-danger-400/10 dark:ring-danger-400/30'><h3 class='fi-banner-heading text-sm font-medium text-danger-700 dark:text-danger-300'>${data.error}</h3></div>`;
                    } else {
                        resultContainer.innerHTML = `<div class='fi-banner fi-color-danger rounded-xl bg-danger-50 p-4 ring-1 ring-danger-600/10 dark:bg-danger-400/10 dark:ring-danger-400/30'><h3 class='fi-banner-heading text-sm font-medium text-danger-700 dark:text-danger-300'>Unknown error occurred.</h3></div>`;
                    }
                })
                .catch(error => {
                    hideLoadingSpinner();
                    resultContainer.innerHTML = `<div class='fi-banner fi-color-danger rounded-xl bg-danger-50 p-4 ring-1 ring-danger-600/10 dark:bg-danger-400/10 dark:ring-danger-400/30'><h3 class='fi-banner-heading text-sm font-medium text-danger-700 dark:text-danger-300'>Error: ${error.message}</h3></div>`;
                });
            });
        }
    });
</script>