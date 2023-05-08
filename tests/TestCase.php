<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockAppPathFunction();
    }

    protected function mockAppPathFunction(): void
    {
        $appPath = realpath(__DIR__ . '/../') . '/app';

        eval(<<<EOT
        function app_path(\$path = '')
        {
            return '{$appPath}' . (\$path ? DIRECTORY_SEPARATOR . \$path : \$path);
        }
        EOT
        );
    }
}
