<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Traits\BlacklistedWordsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Album extends Model
{
    protected $fillable = [
        'title', 'release_date', 'band_id', 'artist_id', 'team_id', 'status', 'user_id', 'submitted_for_review_at', 'approved_at', 'released_at', 'approved_by', 'rejection_reason'
    ];

    protected $casts = [
        'release_date' => 'date',
        'submitted_for_review_at' => 'datetime',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    use HasFactory, SoftDeletes;
    use BlacklistedWordsTrait;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            if (Auth::check()) {
                $user = Auth::user();
                $tenant = $user->currentTeam; // Assuming tenancy is tied to teams

                if ($tenant) {
                    $album->team_id = $tenant->id; // Ensure 'team_id' matches the database column
                }
            }
        });
    }

    public function songs() { return $this->hasMany(Song::class); }

    public function approvals() { return $this->morphMany(Approval::class, 'approvable'); }

    public function latestApproval() { return $this->approvals()->latest()->first(); }

    public function submitForReview(int $userId): void
    {
        if (! in_array($this->status, ['draft','rejected'])) { return; }
        $this->status = 'in_review';
        $this->submitted_for_review_at = now();
        $this->save();
        $this->approvals()->create([
            'submitted_by' => $userId,
            'team_id' => $this->team_id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    public function approve(int $adminId): void
    {
        if ($this->status !== 'in_review') { return; }
        $approval = $this->latestApproval();
        if ($approval && $approval->status === 'pending') {
            $approval->status = 'approved';
            $approval->approved_by = $adminId;
            $approval->reviewed_at = now();
            $approval->save();
        }
    $releaseDate = $this->release_date instanceof \Illuminate\Support\Carbon ? $this->release_date : ($this->release_date ? \Illuminate\Support\Carbon::parse($this->release_date) : null);
    $this->status = $releaseDate && $releaseDate->isFuture() ? 'approved' : 'released';
        $this->approved_at = now();
        $this->approved_by = $adminId;
        if ($this->status === 'released') { $this->released_at = now(); }
        $this->save();
    }

    public function reject(int $adminId, string $reason = null): void
    {
        if ($this->status !== 'in_review') { return; }
        $approval = $this->latestApproval();
        if ($approval && $approval->status === 'pending') {
            $approval->status = 'rejected';
            $approval->approved_by = $adminId;
            $approval->reviewed_at = now();
            $approval->rejection_reason = $reason;
            $approval->save();
        }
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->save();
        if($this->user_id && $this->user){
            $this->user->notify(new \App\Notifications\AlbumRejected($this));
        }
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('team_id', $tenantId); // Ensure 'team_id' matches the database column
    }

    public function getBlacklistedFields(): array
    {
        return array_merge($this->fillable, ['name', 'description']);
    }
}
