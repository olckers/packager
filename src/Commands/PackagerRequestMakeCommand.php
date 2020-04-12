<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;
use olckerstech\packager\src\traits\packager;

class PackagerRequestMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:request
            {name : Name of the Resource}
            {--package= : Fully qualified package name the Resource belongs to}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Requests';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Request: '.$this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Request');
            return false;
        }

        parent::handle();

        if(!$this->copyAndDelete($this->argument('name'))){
            $this->error('FAILED. Could either not move and/or delete the created files');
            return false;
        }
        return true;

    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/request.stub');
    }
}
