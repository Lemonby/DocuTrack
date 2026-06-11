<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('api/*') || $request->expectsJson() ? null : '/');

        // Pindahkan ValidatePostSize dari global middleware stack ke web & api groups.
        // Ini memastikan session sudah berjalan sebelum batasan ukuran post divalidasi,
        // sehingga kita bisa melakukan redirect back dengan validation errors secara aman.
        $middleware->remove(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
        $middleware->appendToGroup('web', \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
        $middleware->appendToGroup('api', \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tangani jika file/post data yang diunggah melebihi batas maksimal post_max_size server
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran data/file yang diunggah terlalu besar (melebihi batas server).',
                ], 413);
            }

            // Tentukan key error berdasarkan rute / path
            $errorKey = 'surat_pengantar';
            if ($request->is('*lpj*')) {
                $errorKey = 'bukti';
            }

            return redirect()->back()
                ->withInput($request->except(['surat_pengantar', 'bukti']))
                ->withErrors([
                    $errorKey => 'Ukuran file yang diunggah terlalu besar. Maksimal ukuran total unggahan adalah ' . ini_get('post_max_size') . '.',
                    'error' => 'Ukuran file yang diunggah terlalu besar. Maksimal ukuran total unggahan adalah ' . ini_get('post_max_size') . '.'
                ]);
        });

        // Force JSON for all API requests
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'success' => false,
                    'message' => "Data {$model} tidak ditemukan.",
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan.',
                ], 404);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Akses ditolak. Anda tidak memiliki izin.',
                ], 403);
            }
        });
    })->create();
