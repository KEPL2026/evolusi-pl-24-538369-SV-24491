<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Vite;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()->instance(Vite::class, new class
        {
            public function __invoke(...$entrypoints): string
            {
                return '';
            }
        });
    }
}
