<?php

namespace App\Models;
use App\Models\Team;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'team_id','email','role','token','accepted_at','invited_by','expires_at','revoked_at'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public static function booted()
    {
        static::creating(function($invitation){
            if(empty($invitation->token)) {
                $invitation->token = Str::uuid()->toString();
            }
            // safety: ensure invited_by set if not
            if(empty($invitation->invited_by) && \Illuminate\Support\Facades\Auth::id()) {
                $invitation->invited_by = \Illuminate\Support\Facades\Auth::id();
            }
        });

        static::created(function($invitation){
            \App\Services\LogActivity::record('invitation.created','Invitation',$invitation->id,[
                'email' => $invitation->email,
                'role' => $invitation->role,
            ], $invitation->team_id);
        });
    }

    public function scopeForTeamEmail($query, int $teamId, string $email)
    {
        return $query->where('team_id',$teamId)->where('email',$email);
    }

    public function isAccepted(): bool
    {
        return ! is_null($this->accepted_at);
    }

    public function markRevoked(): void
    {
        // simple revoke: delete (could add revoked_at column later)
        if(! $this->revoked_at) {
            $this->update(['revoked_at' => now()]);
            \App\Services\LogActivity::record('invitation.revoked','Invitation',$this->id,[ 'email'=>$this->email ], $this->team_id);
        }
    }

    public function isActive(): bool
    {
        if($this->revoked_at) return false;
        if($this->accepted_at) return false;
        if($this->expires_at && now()->greaterThan($this->expires_at)) return false;
        return true;
    }

    public function team(): BelongsTo { return $this->belongsTo(Team::class); }
    public function inviter(): BelongsTo { return $this->belongsTo(User::class,'invited_by'); }
}
