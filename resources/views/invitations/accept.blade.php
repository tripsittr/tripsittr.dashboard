@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md mt-16 p-6 bg-white shadow rounded">
    <h1 class="text-xl font-semibold mb-4">Accept Invitation</h1>
    <p class="mb-4">You've been invited to join team: <strong>{{ $invitation->team->name }}</strong>
        @if($invitation->role) as <strong>{{ $invitation->role }}</strong>@endif.</p>

    @if(!empty($emailMismatch))
    <div class="mb-4 p-3 rounded bg-amber-100 text-amber-800 text-sm space-y-2">
        {!! __( 'Invitation Email Mismatch', ['current' => '<strong>'.e(auth()->user()->email).'</strong>', 'expected'
        => '<strong>'.e($invitation->email).'</strong>'] ) !!}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:text-red-800 underline">{{
                __('Logout') }}</button>
        </form>
    </div>
    @endif

    @guest
    <p class="mb-4">Please register or login with <strong>{{ $invitation->email }}</strong> to accept this invitation.
    </p>
    <div class="flex gap-3">
        <a href="{{ route('login',['email'=>$invitation->email]) }}" class="text-blue-600 underline">Login</a>
        <a href="{{ route('register',['email'=>$invitation->email,'invitation'=>$invitation->token]) }}"
            class="text-blue-600 underline">Register</a>
    </div>
    @else
    @if(empty($emailMismatch))
    <form method="POST" action="{{ route('invitations.accept',$invitation->token) }}" class="space-y-4">
        @csrf
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Accept Invitation</button>
    </form>
    @endif
    @endguest
</div>
@endsection