<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Date;
use App\Attachment;

class Permit extends Model
{
    protected $table = 'permits';

    /**
     * Get all of the dates for the Permit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dates()
    {
        return $this->hasMany(Date::class, 'permit_id');
    }
    

    /**
     * Get all of the attachments for the Permit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'permit_id');
    }
}
