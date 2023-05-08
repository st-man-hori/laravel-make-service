<?php

namespace Tests\Unit;

use StManHori\LaravelMakeService\MakeServiceCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Container\Container;
use Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Mockery;

class MakeServiceCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testHandle()
    {
        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('isDirectory')->twice()->andReturn(false);
        $filesystem->shouldReceive('makeDirectory')->twice()->andReturn(true);
        $filesystem->shouldReceive('get')->twice()->andReturn('DummyNamespace', 'DummyNamespace');
        $filesystem->shouldReceive('put')->twice()->withArgs(function ($path, $content) {
            $this->assertStringContainsString('App\Services', $content);
            return true;
        });

        $command = new MakeServiceCommand($filesystem);
        $app = new Container();
        $command->setLaravel($app);

        $input = new ArrayInput(['name' => 'TestService']);
        $output = new BufferedOutput();
        $command->run($input, $output);

        $outputText = $output->fetch();
        $this->assertStringContainsString('Service created successfully.', $outputText);
        $this->assertStringContainsString('Register the service in AppServiceProvider using:', $outputText);
        $this->assertStringContainsString('\App\Services\TestServiceInterface', $outputText);
        $this->assertStringContainsString('\App\Services\TestService', $outputText);
    }
}
