<?php

return [
    'failure_update_transaction' => [
        'code'          => 4100,
        'status_code'   => 401,
        'message'       => "Failed Insert"],
    'failure_insert_transaction' => [
        'code'          => 4101,
        'status_code'   => 403,
        'message'       => "Failed Update "],
    'failure_delete_transaction' => [
        'code'          => 4200,
        'status_code'   => 422,
        'message'       => "Failed Delete"],
    'failure_select_transaction' => [
        'code'          => 4200,
        'status_code'   => 422,
        'message'       => "Id :id not found."],
    'failure_update_origin_transaction' => [
        'code'          => 4200,
        'status_code'   => 422,
        'message'       => "Update Origin Data Failed"],

];
