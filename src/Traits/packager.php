<?php

namespace olckerstech\packager\Traits;

use Illuminate\Support\Str;

trait packager
{
    protected $packagerDirectory = false;

    protected $packagerVendor = false;

    protected $packagerPackage = false;

    protected $packageNameSpace = false;

    /**
     * Parse the package
     *
     * @return bool
     */
    public function parsePackage()
    {
        if (($this->option('package') !== null) && $this->checkProvidedPackageName($this->option('package'))) {
            if ($this->doesNotExistAndCantCreate()) {
                return false;
            }
            return true;
        }

        $this->packagerDirectory = $this->laravel->basePath(config('packager.packager_working_directory'));

        if (!$this->getVendor()) {
            return false;
        }

        if (!$this->getPackage($this->packagerVendor)) {
            return false;
        }

        $this->packageNameSpace = $this->packagerVendor . '\\' . $this->packagerPackage;

        if ($this->doesNotExistAndCantCreate()) {
            return false;
        }

        return true;
    }

    /**
     * Check if entity exists and if a new one can be created
     *
     * @return bool
     */
    public function doesNotExistAndCantCreate()
    {
        if (!$this->checkIfPackageExists() && !$this->checkForCanCreate()) {
            return true;
        }

        return false;
    }

    /**
     * Checks the physical drive for Vendor directories
     *
     * @return bool
     */
    public function getVendor()
    {
        $vendorList = $this->filterDirectories(array_values(scandir($this->packagerDirectory)));
        if (count($vendorList) === 1 && $vendorList[0] === '') {
            $this->error('No Vendors found. Try running: php artisan packager:create');
            return false;
        }
        $this->packagerVendor = $this->choice('Select Vendor:', $vendorList, 0, config('packager.command_settings.max_attempts'));

        return true;
    }

    /**
     * Checks the directory associated with the vendor for packages
     *
     * @param $vendor
     * @return bool
     */
    public function getPackage($vendor)
    {
        $packageList = $this->filterDirectories(array_values(scandir(($this->packagerDirectory . '/' . $vendor))));
        if (count($packageList) === 1 && $packageList[0] === '') {
            $this->error('The Vendor directory is empty. No Packages found. Try running: php artisan packager:create');
            return false;
        }
        $this->packagerPackage = $this->choice('Select Package:', $packageList, 0, config('packager.command_settings.max_attempts'));

        return true;
    }

    /**
     * Cleans the file directories
     *
     * @param $directories
     * @return array
     */
    public function filterDirectories($directories)
    {
        return explode(
            ' ',
            trim(str_replace(
                config('packager.directory_filter.replace'),
                config('packager.directory_filter.replace_with'),
                implode(' ', $directories)))
        );
    }

    /**
     * Check the provided package name for any illegal chars
     *
     * @param $package
     * @return bool
     */
    public function checkProvidedPackageName($package)
    {
        $package = str_replace('/', '\\', $package);

        $array = explode('\\', $package);

        if (count($array) !== 2) {
            return false;
        }

        if (preg_match('#[^a-zA-Z0-9]#', $array[0])) {
            $this->warn('Vendor name ' . $array[0] . ' contains illegal characters');
            return false;
        }

        $this->packagerVendor = $array[0];

        if (preg_match('#[^a-zA-Z0-9]#', $array[1])) {
            $this->warn('Package name ' . $array[1] . ' contains illegal characters');
            return false;
        }

        $this->packagerPackage = $array[1];

        $this->packageNameSpace = $this->packagerVendor . '\\' . $this->packagerPackage;

        return true;
    }

    /**
     *Checks if this is a new package request
     *
     * @return bool
     */
    public function checkForCanCreate()
    {
        return $this->getName() === 'packager:create';
    }

    /**
     * Checks if a package exists
     *
     * @return bool
     */
    public function checkIfPackageExists()
    {
        $filePath = str_replace('\\', '/', $this->packageNameSpace);

        if (file_exists(base_path('packages/' . $filePath))) {
            return true;
        }

        return false;
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
            $from = base_path(str_replace('\\', '/', $this->getDefaultNamespace('App') . '/' . $name . '.php'));
            $to = $this->str_replace_once('App', 'packages', $from);
            $to = $this->str_replace_once('/temp_packages', '', $to);
            $package_dir = $this->str_replace_once('/' . $name . '.php', '', $to);
            $this->line('Moving created files to package...');
            if (!file_exists($package_dir)) {
                if (!mkdir($package_dir, 0777, true) && !is_dir($package_dir)) {
                    //throw new \RuntimeException(sprintf('Directory "%s" was not created', $package_dir));
                }
            }
            copy($from, $to);
            $this->line('Deleting temporary file...');
            unlink($from);
            $this->line('Deleting temporary directory: ' . base_path('App/temp_packages'));
            $this->rrmdir(base_path('App/temp_packages'));
            $this->line('Done');
        }
        return true;
    }

    /**
     * String replace once function
     *
     * @param $str_pattern
     * @param $str_replacement
     * @param $string
     * @return string|string[]
     */
    public function str_replace_once($str_pattern, $str_replacement, $string)
    {

        if (strpos($string, $str_pattern) !== false) {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }

        return $string;
    }

    /**
     * Recursively delete temporary directory
     *
     * @param $src
     */
    public function rrmdir($src)
    {
        if (file_exists($src)) {
            $dir = opendir($src);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    $full = $src . '/' . $file;
                    if (is_dir($full)) {
                        $this->rrmdir($full);
                    } else {
                        unlink($full);
                    }
                }
            }
            closedir($dir);
            rmdir($src);
        }
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return $this->laravel->basePath('packages/olckerstech/packager/resources/' . trim($stub, '/'));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\temp_packages\\' . $this->packageNameSpace . '\\' . $this->packageNameSpaceModifier;
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel', 'DummyPackageNameSpace', 'DummyEntityName', 'DummyModelName', 'DummyBasePackage'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}', '{{ packageNameSpace }}', '{{ entityName }}', '{{ modelName }}', '{{ basePackage }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}', '{{packageNameSpace}}', '{{entityName}}', '{{modelName}}', '{{basePackage}}'],
        ];

        if($this->packagerPackage === null){
            $dummyEntityName = 'REPLACE';
        }else{
            $dummyEntityName = Str::ucfirst($this->packagerPackage);
        }

        $excludeSrcInNameSpaceModifier = $this->str_replace_once('src\\', '', $this->packageNameSpaceModifier);

        if($this->option('model') === null){
            $dummyModel = substr($name, strrpos($name, '\\') + 1 );//strstr($name, '\\');
            $dummyModel = str_replace(Str::ucfirst(Str::singular($excludeSrcInNameSpaceModifier)), '', $dummyModel);
        }else{
            $dummyModel = Str::ucfirst($this->option('model'));
        }

        $dummyNameSpace = $this->getNamespace($name);
        $dummyRootNameSpace = $this->rootNamespace();
        $nameSpacedDummyUserModel = $this->userProviderModel();
        $dummyPackageNameSpace = $this->packageNameSpace;
        $packageNameSpaceModifier = $this->packageNameSpace . '\\' . $excludeSrcInNameSpaceModifier;

/*
        dd([
            'name' => $name,
            'DummyNamespace' => $dummyNameSpace,
            'DummyRootNamespace' => $dummyRootNameSpace,
            'NamespacedDummyUserModel' => $nameSpacedDummyUserModel,
            'DummyPackageNameSpace' => $dummyPackageNameSpace,
            'DummyEntityName' => $dummyEntityName,
            'PackageNameSpaceModifier' => $packageNameSpaceModifier,
            'ClassNameSpace' => $excludeSrcInNameSpaceModifier,
            'ModelName' => $dummyModel
        ]);
*/
        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [
                    $dummyNameSpace,
                    $dummyRootNameSpace,
                    $nameSpacedDummyUserModel,
                    $packageNameSpaceModifier,
                    $dummyEntityName,
                    $dummyModel,
                    $dummyPackageNameSpace
                ],
                $stub
            );
        }

        return $this;
    }

}
