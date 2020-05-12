<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;
use olckerstech\packager\Traits\commandParser;
use olckerstech\packager\Traits\packager;

class PackagerRepositoryMakeCommand extends GeneratorCommand
{
    use packager;
    use commandParser;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:repository';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class for a package';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:repository
                    {name : Name of the Repository}
                    {--package= : Fully qualified package name the Repository belongs to}
                    {--model= : Model name}
                    {--trashed : Generate trashed model repository}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Repositories';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->info('Creating Repository: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Repository');
            return false;
        }

        parent::handle();

        /*
         * Call interface make command
         *
        $options = ['name' => $this->argument('name').'Interface'];
        $options += ['--package' => $this->getPackagerVendor().'/'.$this->getPackagerPackage()];

        if(!is_null($this->option('model'))){
            $options += ['--model' => $this->option('model')];
        }

        $this->executeCommand('packager:repository-interface', $options);
*/
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
        return $this->resolveStubPath('/stubs/repository.stub');
    }
}
