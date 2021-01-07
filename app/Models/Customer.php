<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name' ,
    'code'           ,
    'address' ,
    'email'                   ,
    'phone' ,
    'address_detail'      ,
    'zip_code'           ,
    'organization_id'            ,
    'created_at'           ,
    'updated_at'      ,];

    public function destination_data()
	{
		return $this->hasOne('App\Models\Transaction', 'destination_data', 'id');
	}
    public function origin_data()
	{
		return $this->hasOne('App\Models\Transaction', 'origin_data', 'id');
	}
}
