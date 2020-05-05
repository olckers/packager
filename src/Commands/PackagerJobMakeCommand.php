<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use olckerstech\packager\traits\packager;

class PackagerJobMakeCommand extends GeneratorCommand
{
    use packager;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:job
            {name : Name of the Job}
            {--package= : Fully qualified package name the Job belongs to}
            {--sync : Indicates that Job should be synchronous}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Jobs';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Job: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Job');
            return false;
        }

        parent::handle();

        if (!$this->copyAndDelete($this->argument('name'))) {
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
        return $this->option('sync')
            ? $this->resolveStubPath('/stubs/job.stub')
            : $this->resolveStubPath('/stubs/job.queued.stub');
    }

}
