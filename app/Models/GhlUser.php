<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GhlUser extends Model
{
    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'assigned_to', 'ghl_user_id');
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'assigned_to', 'ghl_user_id');
    }

    public function wonOpportunities()
    {
        return $this->hasMany(Opportunity::class, 'assigned_to', 'ghl_user_id')->where('status', 'won');
    }
    public function OutboundMessage()
    {
        return $this->hasMany(Message::class,'assigned_to', 'ghl_user_id')->where('type' , 'OutboundMessage');
    }
    public function inboundMessage(){
     return $this->hasMany(Message::class, 'assigned_to', 'ghl_user_id')->where('type' , 'InboundMessage');
    }
       public function smsMessage(){
     return $this->hasMany(Message::class, 'assigned_to', 'ghl_user_id')->where('message_type' , 'SMS');
    }
     public function announcements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_ghl_user', 'ghl_user_id', 'announcement_id');
    }
    public function notes()
{
    return $this->hasManyThrough(
        Note::class,
        Contact::class,
        'assigned_to', // Foreign key on contacts table
        'contact_id',  // Foreign key on notes table
        'ghl_user_id', // Local key on ghl_users table
        'contact_id'   // Local key on contacts table
    );

    
}
}
