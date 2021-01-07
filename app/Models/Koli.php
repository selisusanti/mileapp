<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Koli extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'koli';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','koli_id' ,
    'koli_code'         ,
    'koli_length'      ,
    'awb_url'           ,
    'koli_chargeable_weight'     ,
    'koli_width'       ,
    'koli_surcharge'   ,
    'koli_height'       ,
    'koli_description'  ,
    'koli_formula_id'  ,
    'koli_volume'       ,
    'connote_id'       ,];


    public function koli()
	{
		return $this->belongsTo(\App\Models\Connote::class, 'id');
    }

    
}
