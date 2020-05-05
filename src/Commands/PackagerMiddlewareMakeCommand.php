<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\traits\commandParser;
use olckerstech\packager\traits\packager;

class PackagerMiddlewareMakeCommand extends Command
{
    use packager;
    use commandParser;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:middleware';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:middleware
        {name : Name of the Factory}
        {--package= : Fully qualified package name the Factory belongs to}
        {--model= : The name of the model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new middleware class in a package';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Middleware';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Middleware';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Creating Middleware: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Middleware');
            return false;
        }

        $options = $this->parsePackageControllerOptions();

        if($this->createFolderIfNotExist(str_replace('\\', '/', $this->packageNameSpace.'/'.$this->packageNameSpace.'/'.$this->packageNameSpaceModifier))) {
            $result = $this->executeCommand('make:middleware', $options);
        }else{
            return false;
        }

        if($result !== 0){
            return $result;
        }

        if (!$this->copyAndDelete($this->argument('name'))) {
            $this->error('FAILED. Could either not move and/or delete the created files');
            return false;
        }
        return true;
    }

    public function parsePackageControllerOptions()
    {
        $returnOptions = [];
        $returnOptions += $this->parseOption('name=' . $this->argument('name'));

        $options = $this->options();

        foreach ($options as $option => $value) {
            if (($option !== 'package') && $value) {
                if (is_string($value)) {
                    $returnOptions += $this->parseOption('--' . $option . '=' . $value);
                } else {
                    $returnOptions += $this->parseOption('--' . $option);
                }
            }
        }

        return $returnOptions;
    }

    public function executeCommand($command, $options)
    {
        //$return = false;
        try {
            if (config('packager.command_settings.silent')) {
                $return = $this->callSilent($command, $options);
            } else {
                $return = $this->call($command, $options);
            }
        } catch (\Exception $e) {
            return 'Failed: ' . $e->getMessage();
        }
        return $return;
    }

    /**
     * Copies the created files from the app directory to packages. Files in
     * app directory is deleted after successful copy.
     *
     * @return bool
     */
    public function copyAndDelete($name = false)
    {
        if ($name) {
            $from = $this->laravel->basePath() . '/App/Http/Middleware/' . $name . '.php';
            $package_dir = base_path(str_replace('\\', '/', 'packages/' . $this->packageNameSpace . '/' . $this->packageNameSpaceModifier));
            $to = $package_dir . '/' . $name . '.php';
            $this->line('Moving created files to package...');
            $this->createFolderIfNotExist($package_dir);
            copy($from, $to);
            $this->line('Deleting temporary file...');
            unlink($from);
            $this->line('Done');
        }
        return true;
    }

}
