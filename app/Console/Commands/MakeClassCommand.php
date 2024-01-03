<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeClassCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:class {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create new class';

    public function getStub() {
        return app_path() . '/Console/Commands/Stubs/make-class.stub';
    }

    /**
     * Execute the console command.
     */
    public function makeClass($name)
    {
        $buildClass = parent::buildClass($name);
        return $buildClass;
    }
}
