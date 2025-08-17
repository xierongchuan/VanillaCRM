<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SessionStartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // -------------- ВАЖНО --------------
        // Делать кеш array чтобы throttle не летел в Redis/DB и не зависал.
        config(['cache.default' => 'array']);
        Cache::flush();
        // ------------------------------------

        $this->admin = User::factory()->create([
            'login'    => 'admin',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        $this->user = User::factory()->create([
            'login'    => 'user',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_in_with_correct_credentials()
    {
        $response = $this->postJson('/api/session', [
            'login'    => 'admin',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_with_incorrect_credentials()
    {
        $response = $this->postJson('/api/session', [
            'login'    => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Неверные данные']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_for_non_admin_users()
    {
        $response = $this->postJson('/api/session', [
            'login'    => 'user',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Доступ запрещён']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_blocks_after_throttle_limit()
    {
        // Первые пять запросов — успешный
        foreach (range(1, 5) as $i) {
            $this->postJson('/api/session', [
                'login'    => 'admin',
                'password' => 'password123',
            ])->assertStatus(200);
        }

        // Шестрой запрос — должен вернуть 429
        $response = $this->postJson('/api/session', [
            'login'    => 'admin',
            'password' => 'password123',
        ]);

        $response->assertStatus(429)
                 ->assertJson(['message' => 'Too Many Attempts.']);
    }
}
