<?php

namespace Tests\Unit\Services\Factus;

use App\Models\AccessToken;
use App\Models\FactusConfiguration;
use App\Services\Factus\ApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class ApiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->createRequiredTables();
        $this->seedFactusConfiguration();
        $this->seedAccessToken();
        Cache::flush();
    }

    public function test_numbering_ranges_handles_nested_data_payload(): void
    {
        Http::fake([
            'https://api-sandbox.factus.com.co/v1/numbering-ranges*' => Http::response([
                'data' => [
                    'data' => [
                        [
                            'id' => 8,
                            'prefix' => 'SETP',
                            'from' => 990000000,
                            'to' => 995000000,
                        ],
                    ],
                    'pagination' => [
                        'total' => 1,
                    ],
                ],
            ], 200),
        ]);

        $ranges = ApiService::numberingRanges();

        $this->assertSame(1, count($ranges));
        $this->assertSame(8, $ranges[0]['id']);
        $this->assertSame('SETP', $ranges[0]['prefix']);
    }

    public function test_numbering_ranges_handles_flat_data_payload(): void
    {
        Http::fake([
            'https://api-sandbox.factus.com.co/v1/numbering-ranges*' => Http::response([
                'data' => [
                    [
                        'id' => 10,
                        'prefix' => 'ABC',
                        'from' => 1,
                        'to' => 100,
                    ],
                ],
            ], 200),
        ]);

        $ranges = ApiService::numberingRanges();

        $this->assertSame(1, count($ranges));
        $this->assertSame(10, $ranges[0]['id']);
        $this->assertSame('ABC', $ranges[0]['prefix']);
    }

    public function test_numbering_ranges_returns_empty_array_for_invalid_data_payload(): void
    {
        Http::fake([
            'https://api-sandbox.factus.com.co/v1/numbering-ranges*' => Http::response([
                'data' => 'unexpected-string',
            ], 200),
        ]);

        $ranges = ApiService::numberingRanges();

        $this->assertSame([], $ranges);
    }

    private function createRequiredTables(): void
    {
        if (! Schema::hasTable('factus_configurations')) {
            Schema::create('factus_configurations', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_api_enabled');
                $table->json('api')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('access_tokens')) {
            Schema::create('access_tokens', function (Blueprint $table) {
                $table->id();
                $table->text('access_token');
                $table->text('refresh_token');
                $table->dateTime('expires_at');
                $table->timestamps();
            });
        }

        FactusConfiguration::query()->delete();
        AccessToken::query()->delete();
    }

    private function seedFactusConfiguration(): void
    {
        FactusConfiguration::create([
            'is_api_enabled' => true,
            'api' => [
                'url' => 'https://api-sandbox.factus.com.co/',
                'client_id' => 'client-id',
                'client_secret' => 'client-secret',
                'email' => 'test@example.com',
                'password' => 'secret',
            ],
        ]);
    }

    private function seedAccessToken(): void
    {
        AccessToken::create([
            'access_token' => 'fake-token',
            'refresh_token' => 'fake-refresh',
            'expires_at' => now()->addHour(),
        ]);
    }
}
