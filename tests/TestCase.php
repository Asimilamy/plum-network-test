<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Send the next request the way the SPA does, so Sanctum treats it as
     * stateful and starts a session for the `/api` routes.
     */
    protected function fromSpa(): static
    {
        return $this->withHeader('Referer', config('app.url'));
    }
}
