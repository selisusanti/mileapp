<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use League\OAuth2\Server\Exception\OAuthServerException;
// use App\Services\Response;
use App\Service\Response;
use App\Http\Helpers\GlobalHelper;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // return parent::render($request, $exception);
        $error_code = $e->getCode();
        $error_message = $e->getMessage();

        if ($e instanceof ApplicationException)
        {
            $http_status_code = $e->getStatusCode();
        }
        elseif ($e instanceof HttpResponseException)
        {
            $error_code = $e->getStatusCode();
            $http_status_code = $e->getStatusCode();
        }
        elseif ($e instanceof HttpException)
        {
            $error_code = $e->getStatusCode();
            $http_status_code = $e->getStatusCode();
        }
        else
        {
            $resource = GlobalHelper::getResourceError('errors.application_error');
            if ($e instanceof ValidationException) {
                $resource['message'] = $e->validator->errors()->first();
                $resource['code'] = 4200;
                $resource['status_code'] = 422;
            }
            elseif ($e instanceof QueryException)
                $resource = GlobalHelper::getResourceError('errors.database_error');
            
            $error_message = $resource['message'];
            $error_code = $resource['code'];
            $http_status_code = $resource['status_code'];
        }

        // return Response::error($error_message, $error_code, $http_status_code, $e);
        return Response::error($error_message, $error_code, $http_status_code);

    }
}
