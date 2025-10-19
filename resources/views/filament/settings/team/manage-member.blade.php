@php
$user = auth()->user();
$team = $user?->currentTeam;
if($team){ app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id); }
$member = $this->member;
$currentRole = $member->roles->first()?->name;
$directPerms = $member->permissions->pluck('name')->sort()->values()->implode(', ');
@endphp
<x-filament-panels::page>
    <div class="max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Manage Member</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure role & permissions for <strong>{{
                        $member->name }}</strong></p>
            </div>
            <x-filament::button tag="a"
                :href="route('filament.admin.settings.pages.team-members', ['tenant' => $team?->id])" color="gray">Back
            </x-filament::button>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">Member</div>
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</div>
                <div class="text-xs text-gray-500 mt-1 truncate">{{ $member->email }}</div>
                <div class="mt-3 text-xs text-gray-400">Joined {{ $member->created_at?->diffForHumans() }}</div>
            </div>
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">Current Role</div>
                <div class="flex flex-wrap gap-1">
                    @foreach($member->roles as $r)
                    <span class="inline-flex px-2 py-0.5 text-xs rounded bg-primary/10 text-primary">{{ $r->name
                        }}</span>
                    @endforeach
                </div>
                <div class="mt-4 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Direct
                    Permissions</div>
                <div class="flex flex-wrap gap-1">
                    @forelse($member->permissions as $perm)
                    <span
                        class="inline-flex px-2 py-0.5 text-xs rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{
                        $perm->name }}</span>
                    @empty
                    <span class="text-xs text-gray-400">None</span>
                    @endforelse
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('team.member.roles', $member) }}" class="space-y-8">
            @csrf
            @method('put')
            <div class="grid gap-6 md:grid-cols-2">
                <fieldset class="space-y-3">
                    <legend class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Role</legend>
                    @foreach(['Member','Manager','Admin'] as $role)
                    <label class="flex items-center gap-2 text-sm py-1">
                        <input type="radio" name="role" value="{{ $role }}" @checked($currentRole===$role)
                            class="text-primary focus:ring-primary border-gray-300 dark:border-gray-600" />
                        <span>{{ $role }}</span>
                    </label>
                    @endforeach
                </fieldset>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Extra
                        Permissions</label>
                    <input type="text" name="permissions" value="{{ $directPerms }}"
                        placeholder="upload-media, manage-store"
                        class="fi-input block w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600 text-sm" />
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Comma separated list. New permissions are
                        created automatically for this team.</p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">Changes apply immediately.</div>
                <x-filament::button type="submit" color="primary">Save Changes</x-filament::button>
            </div>
        </form>

        @if(auth()->id() !== $member->id)
        <form method="post" action="{{ route('team.member.remove', $member) }}"
            onsubmit="return confirm('Remove this member from the team?')"
            class="pt-4 border-t border-gray-200 dark:border-gray-700">
            @csrf
            @method('delete')
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-red-600 dark:text-red-400">Danger Zone</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Remove this member from the team.</p>
                </div>
                <x-filament::button type="submit" color="danger" size="sm">Remove Member</x-filament::button>
            </div>
        </form>
        @endif
    </div>
</x-filament-panels::page>