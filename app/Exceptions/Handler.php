<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
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
    public function render($request, Exception $exception)
    {
        $e = $this->prepareException($exception);

        if($e instanceof HttpResponseException) {
            return $e->getResponse();
        }
        if($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
        if ($e instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($e->getModel()));

            return $this->errorResponse("Does not exists any {$modelName} with the specified identificator", 404);
        }
        if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), 403);
        }
        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse("The specified URL cannot be found", 404);
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse("The specified method for the request is invalid", 405);
        }
        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
        if ($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1];
//
            if ($errorCode == 1451) {
                return $this->$this->errorResponse("can not remove this resource permanently. It is related with another resources", 409);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return !$request->expectsJson()
            ? $this->errorResponse("Unauthenticated", 401)
            : redirect()->guest(route('welcome'));
    }


    /**
     * @param ValidationException $e
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Http\JsonResponse|null|\Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return response()->json([
            'code' => '422',
            'description' => 'Ups... Ada error...Mohon periksa kembali form input anda.',
            'error' => $errors
        ], 422);
    }
}
