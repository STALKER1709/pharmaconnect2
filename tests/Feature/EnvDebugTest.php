<?php

namespace Tests\Feature;

use Tests\TestCase;

class EnvDebugTest extends TestCase
{
    public function test_env_debug(): void
    {
        dump([
            'env_effectif' => app()->environment(),
            'running_unit_tests' => app()->runningUnitTests(),
            'session' => config('session.driver'),
            'classe_TestCase' => static::class,
            'parent' => get_parent_class(static::class),
        ]);
        $this->assertTrue(true);
    }
}
