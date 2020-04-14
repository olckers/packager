<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\src\traits\commandParser;
use olckerstech\packager\src\traits\packager;

class PackagerFactoryMakeCommand extends Command
{
    use packager;
    use commandParser;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:factory';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:factory
        {name : Name of the Factory}
        {--package= : Fully qualified package name the Factory belongs to}
        {--model= : The name of the model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new factory for a model in a package';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'database\\factories';

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
        $this->info('Creating Factory: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Factory');
            return false;
        }

        $options = $this->parsePackageControllerOptions();

        if($this->createFolderIfNotExist(str_replace('\\', '/', $this->packageNameSpace.'/'.$this->packageNameSpace.'/'.$this->packageNameSpaceModifier))) {
            $result = $this->executeCommand('make:factory', $options);
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
            $from = $this->laravel->basePath() . '/database/factories/' . $name . '.php';
            $package_dir = base_path(str_replace('\\', '/', 'packages/' . $this->packageNameSpace . '/' . $this->packageNameSpaceModifier));
            $to = $package_dir . '/' . $name . '.php';
            $this->line('Moving created files to package...');
            $this->createFolderIfNotExist($package_dir);
          /*  if (!file_exists($package_dir)) {
                if (!mkdir($package_dir, 0777, true) && !is_dir($package_dir)) {
                    //throw new \RuntimeException(sprintf('Directory "%s" was not created', $package_dir));
                }
            }*/
            $this->line('Fixing use namespace...');
            $this->replaceModelNameSpace($from, str_replace('Factory', '', $name));
            copy($from, $to);
            $this->line('Deleting temporary file...');
            unlink($from);
            $this->line('Done');
        }
        return true;
    }

    /**
     * Replace the namespace for the controller
     * @param $factoryPath
     * @param $modelName
     */
    public function replaceModelNameSpace($factoryPath, $modelName)
    {
        $contents = file_get_contents($factoryPath);
        $contents = str_replace(
            'use App\\'.$modelName,
            'use ' . $this->packageNameSpace . '\\src\\Models\\'. $modelName,
            $contents
        );
        file_put_contents($factoryPath, $contents);
    }
}
