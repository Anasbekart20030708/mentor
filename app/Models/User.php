<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'is_mentor',
        'bio',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_mentor' => 'boolean',
        ];
    }

    // Relationships
    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }

    public function helpRequestsAsMentee()
    {
        return $this->hasMany(HelpRequest::class, 'mentee_id');
    }

    public function helpRequestsAsMentor()
    {
        return $this->hasMany(HelpRequest::class, 'mentor_id');
    }

    public function sessionsAsMentor()
    {
        return $this->hasMany(Sessionn::class, 'mentor_id');
    }

    public function sessionsAsMentee()
    {
        return $this->hasMany(Sessionn::class, 'mentee_id');
    }

    public function feedbackGiven()
    {
        return $this->hasMany(Feedback::class, 'mentee_id');
    }

    public function feedbackReceived()
    {
        return $this->hasMany(Feedback::class, 'mentor_id');
    }
}