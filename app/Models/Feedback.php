<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';
    protected $fillable = [
        'session_id',
        'mentor_id',
        'mentee_id',
        'rating',
        'comment',
        'problem_resolved',
    ];
    public function session()
    {
        return $this->belongsTo(Sessionn::class);
    }
    public function mentorUser()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }
}
