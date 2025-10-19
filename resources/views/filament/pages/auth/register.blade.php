@php
// You can customize layout classes and brand here
$brandLogo = asset('/storage/Tripsittr Logo.png');
@endphp

<x-filament-panels::page.simple>
    <x-slot name="heading">
        Create your Tripsittr account
    </x-slot>

    @if (filament()->hasLogin())
    <x-slot name="subheading">
        Already have an account?
        {{ $this->loginAction }}
    </x-slot>
    @endif

    <div class="mx-auto w-full max-w-2xl">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Welcome</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tell us a bit about you and set up your
                    account.</p>
            </div>

            <x-filament-panels::form id="form" wire:submit="register" class="space-y-6">
                {{ $this->form }}

                <x-filament-panels::form.actions :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()" />
            </x-filament-panels::form>
        </div>

        <div class="my-8 h-px w-full bg-gray-200 dark:bg-gray-800"></div>

        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            By creating an account, you agree to our
            <a href="https://tripsittr.com/terms" class="underline">Terms</a>
            and
            <a href="https://tripsittr.com/privacy" class="underline">Privacy Policy</a>.
        </div>
    </div>
</x-filament-panels::page.simple>