<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'mot_de_passe',
        'password',
        'password_confirmation',
        'current_password',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Gérer les exceptions 404
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return new JsonResponse([
                    'message' => 'Ressource non trouvée.'
                ], 404);
            }
        });

        // Gérer les exceptions de validation
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return new JsonResponse([
                    'message' => 'Les données fournies sont invalides.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Gérer les exceptions d'authentification
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return new JsonResponse([
                    'message' => 'Non authentifié.'
                ], 401);
            }
        });

        // Gérer les exceptions de méthode HTTP non autorisée
        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return new JsonResponse([
                    'message' => 'Méthode non autorisée.'
                ], 405);
            }
        });

        // Gérer les exceptions de modèle non trouvé
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return new JsonResponse([
                    'message' => 'Ressource non trouvée.'
                ], 404);
            }
        });

        // Gérer les exceptions de token CSRF
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            if (!$request->is('api/*')) {
                return redirect()->back()
                    ->withInput($request->except('password', 'mot_de_passe'))
                    ->with('error', 'La session a expiré. Veuillez réessayer.');
            }
        });
    }
}