<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use olckerstech\packager\src\traits\packager;

class PackagerExceptionMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:exception';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom Exception class';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:exception
            {name : Name of the Exception}
            {--package= : Fully qualified package name the Exception belongs to}
            {--render : Create the exception with an empty render method}
            {--report : Create the exception with an empty report method}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Exception';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Exceptions';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Exception: '.$this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Exception');
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
        if ($this->option('render')) {
            return $this->option('report')
                ? $this->resolveStubPath('/stubs/exception-render-report.stub')
                : $this->resolveStubPath('/stubs/exception-render.stub');
        }

        return $this->option('report')
            ? $this->resolveStubPath('/stubs/exception-report.stub')
            : $this->resolveStubPath('/stubs/exception.stub');
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($this->packageNameSpace.'\\Exceptions\\'.$rawName);
    }
}
