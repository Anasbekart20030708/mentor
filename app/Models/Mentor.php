<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    protected $fillable = [
        'user_id',
        'modules',
        'average_rating',
        'total_sessions',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    } public function feedback()
    {
        return $this->hasMany(Feedback::class, 'mentor_id', 'user_id');
    } public function sessions()
    {
        return $this->hasMany(Sessionn::class, 'mentor_id', 'user_id');
    }
}
