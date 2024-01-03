<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeInterfaceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:interface {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make interface command';

    public function getStub() {
        return app_path().'/Console/Commands/Stubs/make-interface.stub';
    }

    /**
     * Execute the console command.
     */
    public function makeClass($name)
    {
        $makeInterface = parent::makeClass($name);
        return $makeInterface;
    }
}
