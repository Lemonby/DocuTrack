<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kegiatan;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugRepairTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    /** @test */
    public function test_debug_419_error()
    {
        // ...
    }

    /** @test */
    public function test_debug_pdf_error()
    {
        // ...
    }
}
