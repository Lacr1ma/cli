<?php
declare(strict_types = 1);

namespace LMS\Cli\Command\Make\Migration;

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

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
class MakeCommand extends AbstractBaseCommand
{
    /**
     * @var string
     */
    protected $signature = 'make:migration {name : The name of the migration.}
        {--extension= : Extension name where generation takes place.}';

    /**
     * @var string
     */
    protected $description = 'Create a new migration';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->askExtensionName();

        $table = $this->getTableName(
            $this->input->getArgument('name')
        );

        if ($this->migrationExistsFor($table)) {
            $this->warn("Migration for <{$table}> already defined. Skipping...");
            return;
        }

        $this->createMigrationFor($table);

        $this->info("Migration for <{$table}> created successfully.");
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->getExtPath() . 'Resources/Private/PHP/Stubs/Migration/create.stub';
    }

    /**
     * @param string $table
     *
     * @return string
     */
    protected function getFinalStub(string $table): string
    {
        $stub = file_get_contents($this->getStub());

        return str_replace('dummyTable', $table, $stub);
    }

    /**
     * @param string $table
     */
    protected function createMigrationFor(string $table): void
    {
        $this->addNewMigration(
            $this->getFinalStub($table)
        );
    }
}
