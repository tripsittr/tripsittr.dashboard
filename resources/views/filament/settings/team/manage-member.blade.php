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
                    @php
                    $allPermissions = \Spatie\Permission\Models\Permission::orderBy('name')->pluck('name');
                    $memberPermissions = $member->permissions->pluck('name')->toArray();
                    // Group permissions by function/entity (split by last word)
                    $groupedPermissions = collect($allPermissions)->groupBy(function($perm) {
                    $parts = explode(' ', $perm);
                    return array_pop($parts);
                    });
                    @endphp
                    <x-filament::button type="button" color="primary" x-data x-on:click="$refs.permModal.showModal()">
                        Edit Permissions
                    </x-filament::button>
                    <dialog x-ref="permModal"
                        class="rounded-lg shadow-lg w-full max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4 md:pl-16 md:pr-10 md:pt-10 md:pb-10"
                        @close.window="$el.close()">
                        <form method="post" action="{{ route('team.member.roles', $member) }}"
                            class="space-y-4 p-6 md:pl-12 md:pr-6 md:pt-6 md:pb-6" @submit.prevent="$el.close()">
                            @csrf
                            @method('put')
                            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Edit
                                Permissions
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-80 overflow-y-auto">
                                @foreach($groupedPermissions as $entity => $perms)
                                <div class="pl-4 md:pl-8">
                                    <div class="font-semibold text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">
                                        {{ ucfirst($entity) }}</div>
                                    <div class="space-y-1">
                                        @foreach($perms as $permission)
                                        <label class="flex items-center gap-2 text-sm px-2 py-1">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                                @if(in_array($permission, $memberPermissions)) checked @endif
                                                class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                                            <span>{{ $permission }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <x-filament::button type="button" color="secondary" @click="$refs.permModal.close()">
                                    Close</x-filament::button>
                                <x-filament::button type="submit" color="primary">Save</x-filament::button>
                            </div>
                        </form>
                    </dialog>
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