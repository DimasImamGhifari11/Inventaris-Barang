<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup authenticated user for testing
     */
    protected function actingAsAuthenticatedUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        return $user;
    }

    /**
     * Helper method untuk membuat request dengan autentikasi
     */
    protected function authenticatedJson($method, $uri, $data = [], $headers = [])
    {
        $this->actingAsAuthenticatedUser();
        return $this->json($method, $uri, $data, $headers);
    }

    /**
     * Helper method untuk POST request dengan autentikasi
     */
    protected function authenticatedPostJson($uri, $data = [], $headers = [])
    {
        return $this->authenticatedJson('POST', $uri, $data, $headers);
    }

    /**
     * Helper method untuk GET request dengan autentikasi
     */
    protected function authenticatedGetJson($uri, $headers = [])
    {
        return $this->authenticatedJson('GET', $uri, [], $headers);
    }

    /**
     * Helper method untuk PUT request dengan autentikasi
     */
    protected function authenticatedPutJson($uri, $data = [], $headers = [])
    {
        return $this->authenticatedJson('PUT', $uri, $data, $headers);
    }

    /**
     * Helper method untuk DELETE request dengan autentikasi
     */
    protected function authenticatedDeleteJson($uri, $data = [], $headers = [])
    {
        return $this->authenticatedJson('DELETE', $uri, $data, $headers);
    }
}
