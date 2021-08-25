<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Permit;

class Attachment extends Model
{
    protected $table = 'attachments';

    /**
     * Get the permit that owns the Date
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permit()
    {
        return $this->belongsTo(Permit::class, 'permit_id');
    }
    
    public function getFileAttribute($value)
    {
        return url($value);;
    }
}
