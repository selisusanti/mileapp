<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoliMaster extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'koli_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id'];
    
}
