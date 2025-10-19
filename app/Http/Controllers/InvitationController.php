<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function show($token, InvitationService $service)
    {
        $invitation = Invitation::where('token',$token)->whereNull('accepted_at')->firstOrFail();
        $user = Auth::user();
        // If user logged in and email matches invitation, accept immediately if not already a member
        if($user && strcasecmp($user->email, $invitation->email) === 0){
            // If the user already belongs to the team, just mark accepted (if not) and redirect without seat/billing changes.
            if($user->teams()->where('teams.id', $invitation->team_id)->exists()) {
                if(! $invitation->accepted_at){
                    $invitation->update(['accepted_at' => now()]);
                }
                return redirect()->route('filament.admin.pages.dashboard')->with('success','Invitation accepted.');
            }
            // Otherwise leverage service to ensure seat & billing logic runs.
            try {
                $service->accept($invitation, $user);
            } catch(\RuntimeException $e){
                return redirect()->route('filament.admin.pages.dashboard')->with('error', $e->getMessage());
            }
            return redirect()->route('filament.admin.pages.dashboard')->with('success','Invitation accepted.');
        }
        // If logged in but email mismatch, show an error view or message
        if($user && strcasecmp($user->email, $invitation->email) !== 0){
            return view('invitations.accept', [
                'invitation' => $invitation,
                'emailMismatch' => true,
            ]);
        }
        return view('invitations.accept', compact('invitation'));
    }

    public function accept(Request $request, $token, InvitationService $service)
    {
        $invitation = Invitation::where('token',$token)->whereNull('accepted_at')->firstOrFail();
        $user = Auth::user();
        if(!$user){
            return redirect()->route('register', ['email' => $invitation->email, 'invitation' => $token]);
        }
        if(strcasecmp($user->email, $invitation->email) !== 0){
            return back()->withErrors(['email' => 'This invitation is for a different email address (expected '.$invitation->email.').']);
        }
        $service->accept($invitation, $user);
        return redirect()->route('filament.admin.pages.dashboard')->with('success','Invitation accepted.');
    }
}
