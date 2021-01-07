<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // use SoftDeletes;

    protected $table = 'transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'transaction_code' ,
    'transaction_order'           ,
    'transaction_payment_type_name' ,
    'customer_id'                   ,
    'transaction_additional_field' ,
    'transaction_payment_type'      ,
    'location_id'           ,
    'connote_id'            ,
    'origin_data'           ,
    'destination_data'      ,
    'custom_field'          ,];

    public function destination_data()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'destination_data');
    }

    public function origin_data()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'origin_data');
    }

    public function connote()
	{
		return $this->belongsTo(\App\Models\Connote::class, 'connote_id')->with("koli");
    }
    
}
