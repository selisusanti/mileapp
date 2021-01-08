<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Jsonable;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Connote;
use App\Models\Koli;
use App\Models\KoliMaster;
use App\Models\Location;
use App\Service\Validate;
use App\Service\Response;
use App\Http\Helpers\Paginator;
use DB;
use Illuminate\Support\Facades\Validator;

use App\Exceptions\ApplicationException;

class TransactionController extends Controller
{

    public function index(Request $request){ 

        $transaction        = Transaction::with(["origin_data","connote","destination_data"])->get();
        $page               = $request->page ? $request->page : 1 ;
        $perPage            = $request->query('limit')?? 10;
        $all_transaction    = collect($transaction);
        $transaction_new    = new Paginator($all_transaction->forPage($page, $perPage), $all_transaction->count(), $perPage, $page, [
            'path' => url("api/package")
        ]); 
        return Response::success($transaction_new);
        
    }


    public function show($id)
    {
        $transaction    = Transaction::where('transaction.id',$id)
                          ->with(["origin_data","connote","destination_data"])->first();
        
        if(empty($transaction))
        {
            throw new ApplicationException("transaction.failure_select_transaction", ['id' =>  $id]);
        }

        return Response::success($transaction);
    }

    public function store(Request $request)
    {
        //transaksi_order
        $validator = Validator::make($request->all(), 
        [
            'origin_data'                   => 'required|integer',
            'destination_data'              => 'required|integer',
            'koli_id_array'                 => 'required|array',
            'customer_id'                   => 'required|integer',
            'transaction_amount'            => 'required|integer',
            'transaction_discount'          => 'required|integer',
            'transaction_additional_field'  => 'string|nullable',
            'transaction_payment_type'      => 'required|integer',

            'organization_id'               => 'required|integer',
            'transaction_cash_amount'       => 'required|integer',
            'transaction_cash_change'       => 'required|integer',

            'surcharge_amount'              => 'string|nullable',
            'actual_weight'                 => 'required|integer',
            'volume_weight'                 => 'required|integer',
            'chargeable_weight'             => 'required|integer',
            'connote_surcharge_amount'      => 'required|integer',
            'connote_sla_day'               => 'required|integer',
            'id_source_tariff'              => 'required|integer',
            'pod'                           => 'required|string|nullable',

            'connote_service'               => 'string|nullable',
            'connote_booking_code'          => 'string|nullable',
            'connote_order'                 => 'string|nullable',

            'transaction_payment_type_name' => 'string|nullable',
            'connote_service' => 'string|required',
        ]);

        if ($validator->fails()) {
            $response = array('response' => '', 'success'=>false);
            $response['response'] = $validator->messages();
            return $response;
        }
        $connote_total_package   =count($request->koli_id_array);
        $jum  = Transaction::orderBy("transaction.id", "desc")->first();
        $origin_detail  = Customer::where("id", $request->origin_data)->first();
        $code_origin    = $origin_detail['code'];
        $destination_detail     = Customer::where("id", $request->destination_data)->first();
        $code_destination       = $destination_detail['code'];
        $detail_customer        = Customer::where("id", $request->customer_id)->first();

        if($jum){
            $transaction_order = $jum['transaction_order'] + 1;
        }else{
            $transaction_order = 1;
        }
        $ldate = date('Ymd');
        $transaction_code   = $code_origin.$ldate.$transaction_order;
        $permitted_chars    = '0123456789abcdefghijklmnopqrstuvwxyz';
        $location_id        = substr(str_shuffle($permitted_chars), 0, 24);


        $connot_num  = connote::orderBy("connote.id", "desc")->first();
        if($connot_num){
            $connote_number = $connot_num['connote_number'] + 1;
        }else{
            $connote_number = 1;
        }

        $lddate2 = date('isdmYY');
        $connote_code = "AWB".$lddate2; 

        DB::beginTransaction();
        try {

            $connote_save           = Connote::create([
                    'connote_number'           => $connote_number,
                    'connote_service'          => $request->connote_service,
                    'connote_service_price'    => $request->transaction_amount,
                    'connote_amount'           => $request->transaction_amount,
                    'connote_code'             => $connote_code,
                    'connote_booking_code'     => $request->connote_booking_code,
                    'connote_order'            => $request->connote_order,
                    'connote_state_id'         => $request->transaction_payment_type,
                    'zone_code_from'           => $code_origin,
                    'zone_code_to'             => $code_destination,
                    'surcharge_amount'         => $request->surcharge_amount,
                    'actual_weight'            => $request->actual_weight,
                    'volume_weight'            => $request->volume_weight,
                    'chargeable_weight'        => $request->actual_weight,
                    'organization_id'          => $request->organization_id,
                    'location_id'              => $location_id,
                    'connote_total_package'    => $connote_total_package,
                    'connote_surcharge_amount' => $request->connote_surcharge_amount,
                    'connote_sla_day'          => $request->connote_sla_day,
                    'location_current'         => $detail_customer['id'],
                    'source_tariff_db'         => $request->id_source_tariff,
                    'id_source_tariff'         => $request->id_source_tariff,
                    'pod'                      => $request->pod,
                    'history'                  => $request->history,
                    'transaction_id'           => "null"
            ]);
            
            $id_connote = $connote_save['id'];

            for($i=0; $i<$connote_total_package-1; $i++)
            {

                $no = $i+1;
                $detail_koli    = KoliMaster::where("id", $request->koli_id_array[$i])->first();

                $koli           = Koli::create([
                        'koli_id'           => $detail_koli['koli_id'],
                        'koli_code'         => $connote_code.".".$no,
                        'koli_length'       => $detail_koli['koli_length'],
                        'awb_url'           => $detail_koli['awb_url'],
                        'koli_chargeable_weight'     => $detail_koli['koli_chargeable_weight'],
                        'koli_width'        => $detail_koli['koli_width'],
                        'koli_surcharge'    => $detail_koli['koli_surcharge'],
                        'koli_height'       => $detail_koli['koli_height'],
                        'koli_description'  => $detail_koli['koli_description'],
                        'koli_formula_id'   => $detail_koli['koli_formula_id'],
                        'koli_volume'       => $detail_koli['koli_volume'],
                        'connote_id'        => $id_connote,
                ]);

            }
            

            $transaction = Transaction::create([
                'transaction_code'            => $transaction_code,
                'transaction_order'           => $transaction_order,
                'transaction_payment_type_name' => $request->transaction_payment_type_name,
                'customer_id'                   => $request->customer_id,
                'transaction_additional_field'  => $request->transaction_additional_field,
                'transaction_payment_type'      => $request->transaction_payment_type,
                'location_id'           => $location_id,
                'connote_id'            => $id_connote,
                'origin_data'           => $request->origin_data,
                'destination_data'      => $request->destination_data,
                'custom_field'          => $request->custom_field,
            ]);

            $connote_save->update([
                'transaction_id'            => $transaction['id']
            ]);

            $transaction3    = Transaction::where("transaction.id", $transaction['id'])
                                ->with(["origin_data","connote","destination_data"])->get();
            DB::commit();
            return Response::success($transaction3);   
        } catch (Exception $e) {
            DB::rollBack();
            throw new ApplicationException("transaction.failure_insert_transaction");
        }
    }

    public function edit(Request $request, $id)
    {
        $transaction        = Transaction::where('id',$id)->first();
  
        if(empty($transaction)){
            throw new ApplicationException("transaction.failure_select_transaction", ['id' =>  $id]);
        }

        //transaksi_order
        $validator = Validator::make($request->all(), 
        [
            'origin_data'                   => 'required|integer',
            'destination_data'              => 'required|integer',
            'koli_id_array'                 => 'required|array',
            'customer_id'                   => 'required|integer',
            'transaction_amount'            => 'required|integer',
            'transaction_discount'          => 'required|integer',
            'transaction_additional_field'  => 'string|nullable',
            'transaction_payment_type'      => 'required|integer',

            'organization_id'               => 'required|integer',
            'transaction_cash_amount'       => 'required|integer',
            'transaction_cash_change'       => 'required|integer',

            'surcharge_amount'              => 'string|nullable',
            'actual_weight'                 => 'required|integer',
            'volume_weight'                 => 'required|integer',
            'chargeable_weight'             => 'required|integer',
            'connote_surcharge_amount'      => 'required|integer',
            'connote_sla_day'               => 'required|integer',
            'id_source_tariff'              => 'required|integer',
            'pod'                           => 'required|string|nullable',

            'connote_service'               => 'string|nullable',
            'connote_booking_code'          => 'string|nullable',
            'connote_order'                 => 'string|nullable',

            'transaction_payment_type_name' => 'string|nullable',
            'connote_service' => 'string|required',
        ]);

        if ($validator->fails()) {
            $response = array('response' => '', 'success'=>false);
            $response['response'] = $validator->messages();
            return $response;
        }


        DB::beginTransaction();
        try {

            $id_connote              = $transaction['connote_id'];
            $connote_total_package   = count($request->koli_id_array);
            $origin_detail           = Customer::where("id", $request->origin_data)->first();
            $code_origin             = $origin_detail['code'];
            $destination_detail      = Customer::where("id", $request->destination_data)->first();
            $code_destination        = $destination_detail['code'];
            $detail_customer         = Customer::where("id", $request->customer_id)->first();
            $lddate2                 = date('isdmYY');
            $connote_code            = "AWB".$lddate2; 



            //UPDATE TRANSAKSI

            $detail_connote    = Connote::where("id", $id_connote)->first();
            $connote_update    = $detail_connote->update([
                'connote_service'          => $request->connote_service,
                'connote_service_price'    => $request->transaction_amount,
                'connote_amount'           => $request->transaction_amount,
                'connote_booking_code'     => $request->connote_booking_code,
                'connote_order'            => $request->connote_order,
                'connote_state_id'         => $request->transaction_payment_type,
                'zone_code_from'           => $code_origin,
                'zone_code_to'             => $code_destination,
                'surcharge_amount'         => $request->surcharge_amount,
                'actual_weight'            => $request->actual_weight,
                'volume_weight'            => $request->volume_weight,
                'chargeable_weight'        => $request->actual_weight,
                'organization_id'          => $request->organization_id,
                'connote_total_package'    => $connote_total_package,
                'connote_surcharge_amount' => $request->connote_surcharge_amount,
                'connote_sla_day'          => $request->connote_sla_day,
                'location_current'         => $detail_customer['id'],
                'source_tariff_db'         => $request->id_source_tariff,
                'id_source_tariff'         => $request->id_source_tariff,
                'pod'                      => $request->pod,
                'history'                  => $request->history
            ]); 

            //UPDATE KOLI TAPI HAPUS DULU YANG SEBELUMNYA
            Koli::where('connote_id',$id_connote)->delete();

            for($i=0; $i<$connote_total_package-1; $i++)
            {

                $no = $i+1;
                $detail_koli    = KoliMaster::where("id", $request->koli_id_array[$i])->first();
                $koli           = Koli::create([
                        'koli_id'           => $detail_koli['koli_id'],
                        'koli_code'         => $connote_code.".".$no,
                        'koli_length'       => $detail_koli['koli_length'],
                        'awb_url'           => $detail_koli['awb_url'],
                        'koli_chargeable_weight'     => $detail_koli['koli_chargeable_weight'],
                        'koli_width'        => $detail_koli['koli_width'],
                        'koli_surcharge'    => $detail_koli['koli_surcharge'],
                        'koli_height'       => $detail_koli['koli_height'],
                        'koli_description'  => $detail_koli['koli_description'],
                        'koli_formula_id'   => $detail_koli['koli_formula_id'],
                        'koli_volume'       => $detail_koli['koli_volume'],
                        'connote_id'        => $id_connote,
                ]);

            }

            //UPDATE TRANSAKSI
            $transaction2             = $transaction->update([
                'transaction_payment_type_name' => $request->transaction_payment_type_name,
                'customer_id'                   => $request->customer_id,
                'transaction_additional_field'  => $request->transaction_additional_field,
                'transaction_payment_type'      => $request->transaction_payment_type,
                'origin_data'                   => $request->origin_data,
                'destination_data'              => $request->destination_data,
                'custom_field'                  => $request->custom_field,
            ]); 

            $transaction3    = Transaction::where("transaction.id", $id)
                                ->with(["origin_data","connote","destination_data"])->get();
            DB::commit();
            return Response::success($transaction3);   
        } catch (Exception $e) {
            DB::rollBack();
            throw new ApplicationException("transaction.failure_update_transaction");
        }

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), 
        [
            'amount'            => 'required|integer',
            'discount'          => 'integer',
          
        ]);

        if ($validator->fails()) {
            $response = array('response' => '', 'success'=>false);
            $response['response'] = $validator->messages();
            return $response;
        }else{
            
            DB::beginTransaction();
            try {
                $transaction = Transaction::where('id',$id)->first();

                $transaction  = $transaction->update([
                    'amount'            => $request->amount,
                    'discount'          => $request->discount,
                ]); 

                DB::commit();
                return Response::success($transaction);   
            } catch (Exception $e) {
                DB::rollBack();
                throw new ApplicationException("transaction.failure_update_transaction", ['id' => $id]);
            }
        } 
    }

    public function delete($id)
    {    
        $transaction = Transaction::where('id',$id)->first();

        if($transaction){
            $id_connote     = $transaction['connote_id'];

            //delete dulu koli dan connote
            Koli::where('connote_id',$id_connote)->delete();
            Connote::where('id',$id_connote)->delete();
            $transaction = $transaction->delete();
        }

        return Response::success(['id' => $id]);

    }

}