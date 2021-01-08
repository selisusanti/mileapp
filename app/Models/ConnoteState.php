<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnoteState extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'payment_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','name'];


    public function payment()
	{
		return $this->hasOne(\App\Models\Connote::class, 'id');
    }

    
}
