<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostTooLargeExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Register dynamic test routes directly in the application's router
        $this->app['router']->post('/test-web-upload', function () {
            return 'ok';
        })->middleware('web');

        $this->app['router']->post('/test-api-upload', function () {
            return 'ok';
        })->middleware('api');
    }

    #[Test]
    public function it_redirects_back_with_validation_errors_when_web_post_too_large(): void
    {
        // Bind ValidatePostSize to mock that always throws PostTooLargeException
        $this->app->bind(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, function () {
            return new class {
                public function handle($request, \Closure $next) {
                    throw new \Illuminate\Http\Exceptions\PostTooLargeException;
                }
            };
        });

        // Act: Simulasikan request POST dari web ke route uji
        $response = $this->from('/previous-page')
            ->post('/test-web-upload');

        // Assert: Harus diredirect kembali ke '/previous-page'
        $response->assertRedirect('/previous-page');

        // Assert: Harus ada pesan error di session untuk 'surat_pengantar' dan 'error'
        $response->assertSessionHasErrors(['surat_pengantar', 'error']);
        
        $errors = session('errors')->getBag('default');
        $this->assertStringContainsString('Ukuran file yang diunggah terlalu besar', $errors->first('surat_pengantar'));
    }

    #[Test]
    public function it_returns_json_error_when_api_post_too_large(): void
    {
        // Bind ValidatePostSize to mock that always throws PostTooLargeException
        $this->app->bind(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, function () {
            return new class {
                public function handle($request, \Closure $next) {
                    throw new \Illuminate\Http\Exceptions\PostTooLargeException;
                }
            };
        });

        // Act: Simulasikan request POST API ke route uji
        $response = $this->postJson('/test-api-upload');

        // Assert: Status 413 Payload Too Large
        $response->assertStatus(413);
        $response->assertJson([
            'success' => false,
            'message' => 'Ukuran data/file yang diunggah terlalu besar (melebihi batas server).',
        ]);
    }
}
