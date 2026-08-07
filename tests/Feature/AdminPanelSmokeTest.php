<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Tests\TestCase;

class AdminPanelSmokeTest extends TestCase
{
    public function test_large_animals_admin_pages_render(): void
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.database', 'vetdrugs');

        $user = User::query()->where('role', UserRole::Admin)->first()
            ?? User::factory()->create(['role' => UserRole::Admin]);

        $routes = [
            '/admin/large-animals/body-systems' => 200,
            '/admin/large-animals/anatomical-structures' => 200,
            '/admin/large-animals/findings' => 200,
            '/admin/large-animals/modifiers' => 200,
            '/admin/large-animals/clinical-signs' => 200,
            '/admin/large-animals/host-species' => 200,
            '/admin/large-animals/disease-classifications' => 200,
            '/admin/large-animals/microorganisms' => 200,
            '/admin/large-animals/medical-articles' => 200,
            '/admin/large-animals/synonyms' => 200,
            '/admin/large-animals/abbreviations' => 200,
            '/admin/large-animals/differential-syndromes' => 200,
            '/admin/large-animals/veterinary-projects' => 200,
            '/admin/knowledge-overview' => 200,
            '/admin/import-disease-knowledge' => 200,
            '/admin/diseases' => 200,
        ];

        foreach ($routes as $uri => $expected) {
            $response = $this->actingAs($user, 'admin')->get($uri);
            $this->assertSame(
                $expected,
                $response->getStatusCode(),
                'Expected '.$expected.' for '.$uri.' got '.$response->getStatusCode()
            );
        }

        $this->assertTrue(true);
    }
}
