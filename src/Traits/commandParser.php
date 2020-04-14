<?php

namespace olckerstech\packager\src\traits;

use Illuminate\Support\Str;

trait commandParser
{

    public function parseAndExecuteCommand($command, $skips)
    {
        $collection = collect(explode(' ', $command));
        if ($collection->count() !== 0) {

            $options_size = $collection->count() - 1;

            $build_command = $collection[0];
            $options_array = [];

            $test_skip = str_replace(':', '', strstr($build_command, ':'));

            if (in_array($test_skip, $skips, true)) {
                return 'Skipped';
            }

            if ($options_size > 0) {
                $i = 0;
                foreach ($collection as $item) {
                    if ($i !== 0) {
                        $options_array += $this->parseOption($item);
                    }
                    $i++;
                }
            }

            //dd($options_array);

            $return = $this->executeCommand($build_command, $options_array);

        } else {
            $return = 'Parse Error';
        }
        return $return;
    }

    public function executeCommand($command, $options)
    {
        try {
            if (config('packager.command_settings.silent')) {
                $this->callSilent($command, $options);
            } else {
                $this->call($command, $options);
            }
        } catch (\Exception $e) {
            return 'Failed: ' . $e->getMessage();
        }
        return 'Success';
    }

    public function parseOption($option)
    {
        $return = [];

        if (strpos($option, '=') !== false) {
            $parse = explode('=', $option);
            $return[$parse[0]] = $parse[1];
        } else {
            $return[$option] = true;
        }
        return $return;
    }

    public function parsePlaceholders($item, $name)
    {
        /*
         * All placeholders relate to the provided $name
         *
         * %name% - replace name as is
         * %plural% - replace name with plural version of name
         * %singular% - replace name with singular version of name
         * %uppercase% - replace name with uppercase version of name
         * %lowercase% - replace name with lowercase version of name
         * %upperfirst% - replace name with first letter of name in uppercase
         * %plurallowercase% - replace name with plural and all lowercase version of name
         * %pluraluppercase% - replace name with plural and all uppercase version of name
         * %pluralupperfirst% - replace name with plural and first letter of name in uppercase
         */

        $replace = [
            '%name%',
            '%plural%',
            '%singular%',
            '%uppercase%',
            '%lowercase%',
            '%upperfirst%',
            '%plurallowercase%',
            '%pluraluppercase%',
            '%pluralupperfirst%',
            '%packager%',
        ];

        $replace_with = [
            $name,
            Str::plural($name),
            Str::singular($name),
            Str::upper($name),
            Str::lower($name),
            Str::ucfirst($name),
            Str::plural(Str::lower($name)),
            Str::plural(Str::upper($name)),
            Str::plural(Str::ucfirst($name)),
            $this->getPackagerVendor() . '/' . $this->getPackagerPackage(),
        ];

        if (is_array($item)) {
            $return_array = [];

            foreach ($item as $value) {
                $return_array[] = str_replace($replace, $replace_with, $value);
            }

            $return = $return_array;
        } else {
            $return = str_replace($replace, $replace_with, $item);
        }

        return $return;
    }

    public function getPackagerVendor()
    {
        if (isset($this->packagerVendor)) {
            return $this->packagerVendor;
        }
        return 'unknown';
    }

    public function getPackagerPackage()
    {
        if (isset($this->packagerPackage)) {
            return $this->packagerPackage;
        }
        return 'unknown';
    }

    public function parseMessages($messages, $name)
    {
        if (isset($messages)) {
            if (is_array($messages)) {
                foreach ($messages as $message) {
                    $this->displayMessage($this->parsePlaceholders($message, $name));
                }
            } else {
                $this->displayMessage($this->parsePlaceholders($messages, $name));
            }
        }
    }

    public function displayMessage($message)
    {
        /*
         * Message types
         *
         * line - default line style message
         * comment - comment style message
         * info - info style message
         * error - error style message
         * warn - warning style message
         */

        $type = strstr($message, ':', true);
        $message = str_replace($type . ': ', '', $message);

        switch ($type) {
            case 'line':
                $this->line($message);
                break;
            case 'comment':
                $this->comment($message);
                break;
            case 'info':
                $this->info($message);
                break;
            case 'error':
                $this->error($message);
                break;
            case 'warn':
                $this->warn($message);
                break;
        }

    }

    /**
     * Checks if a folder path exists. If not, creates the path
     *
     * @param $path
     * @return bool
     */
    public function createFolderIfNotExist($path)
    {
        if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            //throw new \RuntimeException(sprintf('Directory "%s" was not created', $package_dir));
            $this->error('The directory: '.$path.' could not be created');
            return false;
        }

        return true;
    }

}
