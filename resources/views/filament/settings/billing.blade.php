@php
/** @var \App\Models\User|null $user */
$user = auth()->user();
$team = $user?->currentTeam; // accessor provided on User
$subscription = null;
if($team && method_exists($team,'subscription')) {
$subscription = $team->subscription('default');
}
$plan = $team?->plan_slug;
$planConf = $plan ? (config("plans.plans.$plan") ?? null) : null;
$usedSeats = $team?->usedSeats() ?? 0;
$maxSeats = $team?->maxSeats() ?? 0;
$seatPct = $maxSeats > 0 ? min(100, round(($usedSeats / $maxSeats) * 100)) : 0;
// Payment methods (Stripe Cashier)
$paymentMethods = collect();
try { if($user && method_exists($user,'paymentMethods')) { $paymentMethods = collect($user->paymentMethods()); } } catch
(Throwable $e) {}
$defaultPm = null;
try { if($user && method_exists($user,'defaultPaymentMethod')) { $defaultPm = $user->defaultPaymentMethod(); } } catch
(Throwable $e) {}
// Upcoming invoice (if supported)
$upcoming = null;
try { if($user && method_exists($user,'upcomingInvoice')) { $upcoming = $user->upcomingInvoice(); } } catch (Throwable
$e) {}
@endphp

<x-filament-panels::page>
    <div class="space-y-10">
        {{-- Top Summary Stats --}}
        <div class="grid gap-4 mb-10 sm:grid-cols-2 lg:grid-cols-4">
            <div
                class="p-4 rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 flex flex-col gap-1">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Plan</p>
                <p class="text-sm font-semibold capitalize">{{ $plan ?? '—' }}</p>
                <p class="text-[11px] text-gray-400">{{ $planConf['tagline'] ?? '' }}</p>
            </div>
            <div
                class="p-4 rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 flex flex-col gap-1">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Seats</p>
                <p class="text-sm font-semibold">{{ $usedSeats }} / {{ $maxSeats }}</p>
                <div class="mt-1 h-1.5 rounded bg-gray-200 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full bg-primary" style="width: {{ $seatPct }}%"></div>
                </div>
            </div>
            <div
                class="p-4 rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 flex flex-col gap-1">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Status</p>
                @php $status = $subscription?->stripe_status; @endphp
                <p class="text-sm font-semibold capitalize">{{ $status ?? '—' }}</p>
                <span class="text-[11px] text-gray-400">@if($subscription && $subscription->asStripeSubscription())
                    Renews {{
                    \Illuminate\Support\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->toFormattedDateString()
                    }} @endif</span>
            </div>
            <div
                class="p-4 rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 flex flex-col gap-1">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Quantity</p>
                <p class="text-sm font-semibold">{{ $subscription?->quantity ?? '—' }}</p>
                <p class="text-[11px] text-gray-400">Stripe: <span class="font-mono">{{
                        Str::limit($team?->stripe_id,10,'…') ?: '—' }}</span></p>
            </div>
        </div><br>

        {{-- Main Grid --}}
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                {{-- Subscription Detail Card --}}
                <div
                    class="p-6 rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <h2 class="text-base font-semibold">Subscription</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Current plan usage and billing status</p>
                        </div>
                        @if($subscription)
                        <span
                            class="text-[11px] px-2 py-0.5 rounded bg-primary/10 text-primary font-medium">Active</span>
                        @else
                        <span
                            class="text-[11px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 font-medium">Pending</span>
                        @endif
                    </div><br>
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                        <div class="space-y-1">
                            <p class="text-gray-500">Plan</p>
                            <p class="font-medium capitalize text-sm">{{ $plan ?? '—' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-500">Seats Used</p>
                            <p class="font-medium text-sm">{{ $usedSeats }} / {{ $maxSeats }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-500">Stripe Customer</p>
                            <p class="font-mono text-[11px] break-all">{{ $team?->stripe_id ?? '—' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-500">Quantity</p>
                            <p class="font-medium text-sm">{{ $subscription?->quantity ?? '—' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-500">Renews</p>
                            <p class="font-medium text-sm">
                                @if($subscription && $subscription->asStripeSubscription())
                                {{
                                \Illuminate\Support\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->toFormattedDateString()
                                }}
                                @else — @endif
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-500">Status</p>
                            <p class="font-medium text-sm capitalize">{{ $subscription?->stripe_status ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <div
                            class="flex items-center justify-between mb-1 text-[11px] text-gray-500 uppercase tracking-wide">
                            <span>Seat Utilization</span>
                            <span>{{ $seatPct }}%</span>
                        </div>
                        <div class="h-2 w-full rounded bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full bg-primary transition-all" style="width: {{ $seatPct }}%"></div>
                        </div>
                        @if($seatPct >= 90)
                        <p class="mt-2 text-xs text-amber-600">You are nearing your seat limit.</p>
                        @endif

                    </div>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <x-filament::button size="sm" color="gray"
                            wire:click.prevent="$dispatch('open-modal',{id:'portal'})">Open Customer Portal
                        </x-filament::button>
                    </div>
                </div><br>

                {{-- Plan Features --}}
                <div
                    class="p-6 rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold">Plan Features</h3>
                        @if($plan)
                        <span class="px-2 py-0.5 rounded text-[11px] bg-primary/10 text-primary capitalize">{{ $plan
                            }}</span>
                        @endif
                    </div>
                    @if($planConf && !empty($planConf['features']))
                    <ul class="grid sm:grid-cols-2 gap-x-6 gap-y-2 text-xs">
                        @foreach($planConf['features'] as $feat)
                        <li class="flex items-start gap-2">
                            <x-heroicon-s-check class="w-4 h-4 text-primary" /> <span>{{ $feat }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-xs text-gray-500">No feature list available.</p>
                    @endif
                </div>
            </div>
            <div class="space-y-8">
                {{-- Upcoming Invoice --}}
                <div
                    class="p-6 rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-sm font-semibold mb-4">Upcoming Invoice</h3>
                    @if($upcoming)
                    <div class="text-xs space-y-2">
                        <div class="flex justify-between"><span class="text-gray-500">Amount</span><span
                                class="font-medium">${{ number_format($upcoming->total / 100, 2) }} {{
                                strtoupper($upcoming->currency) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Date</span><span
                                class="font-medium">{{
                                \Illuminate\Support\Carbon::createFromTimestamp($upcoming->period_end)->toFormattedDateString()
                                }}</span></div>
                        @if(isset($upcoming->lines) && $upcoming->lines->data)
                        <ul class="mt-2 space-y-1 text-[11px] list-disc list-inside">
                            @foreach($upcoming->lines->data as $line)
                            <li>{{ $line->description ?? 'Line Item' }} - ${{ number_format($line->amount / 100, 2) }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @else
                    <p class="text-xs text-gray-500">No upcoming invoice available.</p>
                    @endif
                </div><br>

                {{-- Help / Notes --}}
                <div
                    class="p-6 rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xs font-semibold mb-4">Billing Notes</h3>
                    <ul class="text-[11px] space-y-2 text-gray-500">
                        <li class="text-xs">Subscriptions are tied to the team (not individual users).</li>
                        <li class="text-xs">Use the customer portal to manage invoices & payment info.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <x-filament::modal id="portal" width="sm">
        <div class="space-y-4">
            <h3 class="text-sm font-semibold">Customer Portal</h3>
            <p class="text-xs text-gray-500">The portal will open in a new tab (if supported). If nothing opened, ensure
                Stripe is configured.</p>
            <x-filament::button color="primary" x-data @click="$dispatch('close-modal',{id:'portal'})">Close
            </x-filament::button>
        </div>
    </x-filament::modal>
</x-filament-panels::page>