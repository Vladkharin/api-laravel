<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function render($request, Throwable $e)
    {

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Login Failed'
            ], 401);
        }
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Forbiden for you'
            ], 403);
        }
        if ($e instanceof ValidationException) {
            return response()->json([
                'success'=> false,
                'message' => $e->errors()
            ], 422);
        }
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Resource Not Found'
            ], 404);
        }
        return parent::render($request, $e); // TODO: Change the autogenerated stub
    }
}
