<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessionn extends Model
{
    protected $table = "sessionns";
    protected $fillable = [
        'help_request_id',
        'mentor_id',
        'mentee_id',
        'module',
        'scheduled_at',
        'type',
        'status',
        'mentor_notes',
    ];
    public function helpRequest()
    {
        return $this->belongsTo(HelpRequest::class);
    }
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
