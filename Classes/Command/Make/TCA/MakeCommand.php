<?php
declare(strict_types = 1);

namespace LMS\Cli\Command\Make\TCA;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use LMS\Facade\Assist\Str;
use Symfony\Component\Console\Input\InputOption;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
class MakeCommand extends \LMS\Cli\Command\Basis\GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:tca';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new TCA';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TCA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->hasOption('extension') && $ext = $this->option('extension')) {
            $this->extensionName = $ext;
        } else {
            $this->extensionName = $this->choice('Please select the working extension', $this->installedExtensions());
        }

        $path = $this->getPath($this->getNameInput());

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, file_get_contents($this->getStub()));

        $this->info($this->type . ' created successfully.');
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     *
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($rawName));
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $ext = str_replace('_', '', $this->extensionName);

        $name = 'tx_' . $ext . '_domain_model_' . Str::lower($name);

        $base = ExtensionManagementUtility::extPath($this->extensionName) . 'Configuration/TCA/';

        return $base . $name . '.php';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $base = ExtensionManagementUtility::extPath('cli');

        return $base . 'Resources/Private/PHP/Stubs/TCA/plain.stub';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['extension', 'e', InputOption::VALUE_OPTIONAL, 'Extension name where generation takes place.'],

            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the model already exists.'],
        ];
    }
}
