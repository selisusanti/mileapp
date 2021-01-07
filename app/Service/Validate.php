<?php

namespace App\Service;

use Illuminate\Validation\ValidationException;
use App\Service\Response;
use Validator;

class Validate
{   
    public static function request($request = [], $format = [])
    {     
        $validator = Validator::make($request,$format);

        if ($validator->fails())
            throw new ValidationException($validator);
        return TRUE;
    }
}