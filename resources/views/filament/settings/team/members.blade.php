@php
$user = auth()->user();
// Prefer the Filament selected tenant (active team) explicitly; fall back to accessor if missing
try {
$tenant = \Filament\Facades\Filament::getTenant();
} catch (\Throwable $e) { $tenant = null; }
$team = ($tenant instanceof \App\Models\Team) ? $tenant : ($user?->current_team);
if($team){
app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
}
$canManage = $user && $team ? $user->hasAnyRole(['Admin','Manager']) : false;
$members = $team ? $team->users()->with('roles')->orderBy('users.name')->get() : collect();
$invitations = $team ? \App\Models\Invitation::where('team_id',$team->id)->latest()->get() : collect();
$activeInvites = $invitations->filter(fn($i) => $i->isActive());
$usedSeats = $team?->usedSeats() ?? 0;
$maxSeats = $team?->maxSeats() ?? 0;
$seatPct = $maxSeats > 0 ? min(100, round(($usedSeats / $maxSeats) * 100)) : 0;
@endphp
<x-filament-panels::page>
    @if(!$canManage)
    <div class="p-4 text-sm text-red-600 bg-red-50 rounded">You do not have permission to manage team members.</div>
    @else
    <div class="space-y-6">
        <!-- Header (no invite action here anymore) -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold leading-tight">Team Members</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage seats & invitations for <strong>{{
                        $team->name }}</strong></p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 flex flex-col gap-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Seats Used</div>
                <div class="flex items-end justify-between">
                    <div class="text-2xl font-semibold">{{ $usedSeats }}<span
                            class="text-base font-normal text-gray-500"> / {{ $maxSeats }}</span></div>
                    <div class="text-xs px-2 py-0.5 rounded bg-primary/10 text-primary">{{ $seatPct }}%</div>
                </div>
                <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded overflow-hidden">
                    <div class="h-full bg-primary/60" style="width: {{ $seatPct }}%"></div>
                </div>
            </div>
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 flex flex-col gap-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Active Invitations</div>
                <div class="text-2xl font-semibold">{{ $activeInvites->count() }}</div>
                <div class="text-xs text-gray-500">{{ $invitations->count() }} total</div>
            </div>
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 flex flex-col gap-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Members</div>
                <div class="text-2xl font-semibold">{{ $members->count() }}</div>
                <div class="text-xs text-gray-500">Including you</div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Members Table -->
            <div class="lg:col-span-2 space-y-4">
                <div
                    class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
                    <table class="min-w-full text-sm table-fixed">
                        <colgroup>
                            <col style="width:20%">
                            <col style="width:25%">
                            <col style="width:25%">
                            <col style="width:15%">
                            <col style="width:15%">
                        </colgroup>
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr class="text-xs text-gray-600 dark:text-gray-300">
                                <th class="px-3 py-2 text-left font-medium">Name</th>
                                <th class="px-3 py-2 text-left font-medium">Email</th>
                                <th class="px-3 py-2 text-left font-medium">Role</th>
                                <th class="px-3 py-2 text-left font-medium">Joined</th>
                                <th class="px-3 py-2 text-left font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members as $member)
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100 truncate">{{
                                    $member->name }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300 truncate">{{ $member->email }}
                                </td>
                                <td class="px-3 py-2">
                                    @php $primaryRole = $member->roles->first()?->name; @endphp
                                    @if($primaryRole)
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded bg-primary/10 text-primary">{{
                                        $primaryRole }}</span>
                                    @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{
                                    $member->created_at?->diffForHumans() }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-wrap gap-2">
                                        <x-filament::button tag="a" size="xs" color="gray"
                                            href="{{ \App\Filament\Artists\Clusters\Settings\Pages\ManageMember::getUrl(['member' => $member]) }}">
                                            Manage</x-filament::button>
                                        @if($member->id !== $user->id)
                                        <x-filament::button size="xs" color="danger" x-data
                                            @click="$dispatch('open-modal', { id: 'remove-member-{{ $member->id }}' })">
                                            Remove</x-filament::button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-gray-500">No members found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Invitations Panel -->
            <div class="space-y-4">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-sm">Invitations</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage pending & past invites</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <x-filament::badge color="gray">{{ $invitations->count() }}</x-filament::badge>
                            <x-filament::button size="xs" color="primary" x-data
                                @click="$dispatch('open-modal', { id: 'invite-member' })">
                                Invite Member
                            </x-filament::button>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3">
                    @if($invitations->isEmpty())
                    <div class="px-3 py-6 text-center text-sm text-gray-500">No invitations found.</div>
                    @else
                    <ul class="grid gap-3 sm:grid-cols-1 md:grid-cols-1">
                        @foreach($invitations as $inv)
                        @php
                        $status = 'Pending';
                        if($inv->accepted_at) $status = 'Accepted';
                        elseif($inv->revoked_at) $status = 'Revoked';
                        elseif($inv->expires_at && now()->greaterThan($inv->expires_at)) $status = 'Expired';
                        $badgeColors = [
                        'Accepted' => 'bg-green-100 text-green-700 dark:bg-green-600/20 dark:text-green-400',
                        'Revoked' => 'bg-red-100 text-red-700 dark:bg-red-600/20 dark:text-red-400',
                        'Expired' => 'bg-gray-100 text-gray-700 dark:bg-gray-600/30 dark:text-gray-300',
                        'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-600/20 dark:text-amber-400',
                        ];
                        @endphp
                        <li
                            class="rounded border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-950/30 px-3 py-3 flex flex-col gap-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-medium text-sm text-gray-900 dark:text-gray-100 truncate">{{
                                        $inv->email }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex gap-2 items-center">
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs rounded {{ $badgeColors[$status] ?? 'bg-gray-100 text-gray-600' }}">{{
                                            $status }}</span>
                                        @if($inv->role)
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs rounded bg-primary/10 text-primary">{{
                                            $inv->role }}</span>
                                        @endif
                                        @if($inv->expires_at)
                                        <span class="text-[10px] text-gray-400">exp {{ $inv->expires_at->diffForHumans()
                                            }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 justify-end">
                                    @if(! $inv->accepted_at && ! $inv->revoked_at)
                                    <form method="post" action="{{ route('team.invitation.resend',$inv) }}"
                                        class="inline">
                                        @csrf
                                        <x-filament::button size="xs" color="gray">Resend</x-filament::button>
                                    </form>
                                    <form method="post" action="{{ route('team.invitation.revoke',$inv) }}"
                                        class="inline" onsubmit="return confirm('Revoke this invitation?')">
                                        @csrf
                                        <x-filament::button size="xs" color="danger">Revoke</x-filament::button>
                                    </form>
                                    @else
                                    <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">Sent {{
                                $inv->created_at?->diffForHumans() }}</div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <x-filament::modal id="invite-member" width="md">
        <form method="post" action="{{ route('team.invite') }}" class="space-y-4">
            @csrf
            @if($team)
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            @endif
            <x-filament::input.wrapper>
                <label class="fi-input-label text-xs font-medium text-gray-700 dark:text-gray-300">Email</label>
                <x-filament::input type="email" name="email" required />
            </x-filament::input.wrapper>
            <x-filament::input.wrapper>
                <label class="fi-input-label text-xs font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select name="role"
                    class="fi-input block w-full rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600"
                    required>
                    <option value="Member">Member</option>
                    <option value="Manager">Manager</option>
                    <option value="Admin">Admin</option>
                </select>
            </x-filament::input.wrapper>
            <div class="text-right">
                <x-filament::button type="submit" color="primary">Send Invite</x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    @foreach($members as $member)
    @if($member->id !== $user->id)
    <x-filament::modal id="remove-member-{{ $member->id }}" width="sm">
        <form method="post" action="{{ route('team.member.remove', $member) }}" class="space-y-4">
            @csrf
            @method('delete')
            <p class="text-sm">Remove <strong>{{ $member->name }}</strong> from the team?</p>
            <div class="flex justify-end gap-2">
                <x-filament::button type="button" color="gray" x-data
                    @click="$dispatch('close-modal', { id: 'remove-member-{{ $member->id }}' })">Cancel
                </x-filament::button>
                <x-filament::button type="submit" color="danger">Remove</x-filament::button>
            </div>
        </form>
    </x-filament::modal>
    @endif
    @endforeach
</x-filament-panels::page>