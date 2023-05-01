<?php

namespace StManHori\LaravelMakeService;

use Illuminate\Support\ServiceProvider;

class LaravelMakeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands(MakeServiceCommand::class);
    }
}