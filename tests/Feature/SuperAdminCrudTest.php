<?php

namespace Tests\Feature;

use App\Models\Iku;
use App\Models\Jurusan;
use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class SuperAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private string $token;

    private Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->superAdmin = User::create([
            'nama' => 'Super Admin Test',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);
        $this->superAdmin->assignRole('SuperAdmin');
        $this->token = $this->superAdmin->createToken('superadmin-token')->plainTextToken;

        $this->jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Informatika dan Komputer']);
    }

    /**
     * Memastikan SuperAdmin dapat melakukan operasi CRUD untuk mengelola akun user.
     */
    #[Test]
    #[TestDox('Memastikan SuperAdmin dapat melakukan operasi CRUD untuk mengelola akun user')]
    public function test_superadmin_user_crud_operations(): void
    {
        // 1. Create a user (POST /api/v1/superadmin/users)
        $createUserPayload = [
            'nama' => 'New Staff User',
            'email' => 'staff@example.com',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
            'role' => 'Admin',
            'nama_jurusan' => $this->jurusan->nama_jurusan,
        ];

        $createResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->postJson('/api/v1/superadmin/users', $createUserPayload);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $userId = $createResponse->json('data.id');
        $this->assertDatabaseHas('users', [
            'user_id' => $userId,
            'nama' => 'New Staff User',
            'email' => 'staff@example.com',
        ]);

        // 2. Read all users (GET /api/v1/superadmin/users)
        $listResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->getJson('/api/v1/superadmin/users');

        $listResponse->assertStatus(200)
            ->assertJsonPath('success', true);
        $this->assertNotEmpty($listResponse->json('data'));

        // 3. Read single user (GET /api/v1/superadmin/users/{id})
        $showResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->getJson("/api/v1/superadmin/users/{$userId}");

        $showResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'New Staff User');

        // 4. Update user (PUT /api/v1/superadmin/users/{id})
        $updatePayload = [
            'nama' => 'Updated Staff User',
            'status' => 'Tidak Aktif',
        ];

        $updateResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->putJson("/api/v1/superadmin/users/{$userId}", $updatePayload);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'user_id' => $userId,
            'nama' => 'Updated Staff User',
            'status' => 'Tidak Aktif',
        ]);

        // 5. Delete user (DELETE /api/v1/superadmin/users/{id})
        $deleteResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->deleteJson("/api/v1/superadmin/users/{$userId}");

        $deleteResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Soft deleted users are hidden by default, check deleted_at column
        $user = User::withTrashed()->find($userId);
        $this->assertNotNull($user->deleted_at);
    }

    /**
     * Memastikan SuperAdmin dapat melakukan operasi CRUD untuk mengelola IKU Master.
     */
    #[Test]
    #[TestDox('Memastikan SuperAdmin dapat melakukan operasi CRUD untuk mengelola IKU Master')]
    public function test_superadmin_iku_crud_operations(): void
    {
        // 1. Create IKU (POST /api/v1/superadmin/iku)
        $createIkuPayload = [
            'kode_iku' => 'IKU_TEST_01',
            'indikator_kinerja' => 'Persentase Lulusan Tepat Waktu',
            'deskripsi' => 'IKU deskripsi',
            'target' => '90%',
            'tahun' => 2026,
        ];

        $createResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->postJson('/api/v1/superadmin/iku', $createIkuPayload);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $ikuId = $createResponse->json('data.id');
        $this->assertDatabaseHas('ikus', [
            'id' => $ikuId,
            'kode_iku' => 'IKU_TEST_01',
            'indikator_kinerja' => 'Persentase Lulusan Tepat Waktu',
        ]);

        // 2. Read all IKUs (GET /api/v1/superadmin/iku)
        $listResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->getJson('/api/v1/superadmin/iku');

        $listResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. Read single IKU (GET /api/v1/superadmin/iku/{id})
        $showResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->getJson("/api/v1/superadmin/iku/{$ikuId}");

        $showResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_iku', 'IKU_TEST_01');

        // 4. Update IKU (PUT /api/v1/superadmin/iku/{id})
        $updatePayload = [
            'target' => '95%',
        ];

        $updateResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->putJson("/api/v1/superadmin/iku/{$ikuId}", $updatePayload);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('ikus', [
            'id' => $ikuId,
            'target' => '95%',
        ]);

        // 5. Delete IKU (DELETE /api/v1/superadmin/iku/{id})
        $deleteResponse = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->deleteJson("/api/v1/superadmin/iku/{$ikuId}");

        $deleteResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('ikus', [
            'id' => $ikuId,
        ]);
    }
}
