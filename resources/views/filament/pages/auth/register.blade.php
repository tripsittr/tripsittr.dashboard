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

        <x-filament::section>
            <x-slot name="heading">Welcome</x-slot>
            <x-slot name="description">Tell us a bit about you and set up your account.</x-slot>

            <x-filament-panels::form id="form" wire:submit="register" class="space-y-6">
                {{ $this->form }}

                <x-filament-panels::form.actions :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()" />
            </x-filament-panels::form>
        </x-filament::section>

        <br>
        <hr class="bg-black" />
        <br>

        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            By creating an account, you agree to our
            <a href="https://tripsittr.com/terms" class="underline">Terms</a>
            and
            <a href="https://tripsittr.com/privacy" class="underline">Privacy Policy</a>.
        </div>
    </div>
</x-filament-panels::page.simple>