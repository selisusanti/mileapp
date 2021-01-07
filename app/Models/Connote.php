<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connote extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'connote';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','connote_number','connote_service',
    'connote_service_price'   ,
    'connote_amount'           ,
    'connote_code'            ,
    'connote_booking_code'    ,
    'connote_order'            ,
    'connote_state_id'         ,

    'zone_code_from'          ,
    'zone_code_to'            ,
    'surcharge_amount'        ,
    'actual_weight'            ,
    'volume_weight'           ,
    'chargeable_weight'       ,
    'organization_id'         ,
    'location_id'             ,
    'connote_total_package'    ,
    'connote_surcharge_amount',
    'connote_sla_day'         ,
    'location_current'       ,
    'source_tariff_db'        ,
    'id_source_tariff'        ,
    'pod'                      ,
    'history'                 ,];


    public function connote()
	{
		return $this->hasOne('App\Models\Transaction', 'connote_id', 'id');
    }
    

	public function koli()
	{
        	return $this->hasMany('App\Models\Koli', 'connote_id', 'id');
	}

}
