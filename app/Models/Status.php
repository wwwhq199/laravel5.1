<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //

    //也许批量赋值
    protected $fillable = ['content'];
    function user() {
        return $this->belongsTo(User::class);
    }
}
