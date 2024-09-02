<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, \Exception $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Vous n\'avez pas les droits pour effectuer cette action.'], 403);
        }

        abort(403, 'Vous n\'avez pas les droits pour effectuer cette action.');
        return redirect()->guest('login');


    }
}
