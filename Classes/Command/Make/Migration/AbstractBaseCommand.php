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

use LMS\Facade\Assist\Str;

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
abstract class AbstractBaseCommand extends \LMS\Cli\Command\Basis\ExtensionBasedAbstractCommand
{
    /**
     * @param string $rawInputName
     *
     * @return string
     */
    protected function getTableName(string $rawInputName): string
    {
        $processedName = str_replace('_', '', Str::snake($rawInputName));

        return "{$this->getExtKey()}_domain_model_{$processedName}";
    }

    /**
     * @return string
     */
    protected function getMigrationFilePath(): string
    {
        return "{$this->getExtPath()}ext_tables.sql";
    }

    /**
     * @param string $table
     *
     * @return  bool
     */
    protected function migrationExistsFor(string $table): bool
    {
        return $this->fileContains($this->getMigrationFilePath(), $table);
    }

    /**
     * @param string $sql
     */
    protected function addNewMigration(string $sql): void
    {
        $this->files->append($this->getMigrationFilePath(), $sql);
    }
}
