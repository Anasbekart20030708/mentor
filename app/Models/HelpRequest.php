<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    protected $fillable = [
        'mentee_id',
        'mentor_id',
        'module',
        'description',
        'proposed_date',
        'type',
        'status',
    ];
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }  public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }  public function session()
    {
        return $this->hasOne(Sessionn::class);
    }
}
