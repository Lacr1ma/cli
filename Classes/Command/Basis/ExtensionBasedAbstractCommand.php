<?php
declare(strict_types = 1);

namespace LMS\Cli\Command\Basis;

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

use Symfony\Component\Finder\Finder;
use MCStreetguy\ComposerParser\Factory as Parser;
use TYPO3\CMS\Core\{Core\Environment, Utility\ExtensionManagementUtility};

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
abstract class ExtensionBasedAbstractCommand extends FileableAbstractCommand
{
    /**
     * @var string
     */
    protected $extensionName;

    /**
     * Ask about extension scope if it's not passed directly and save it
     */
    protected function askExtensionName(): void
    {
        if ($this->hasOption('extension') && $ext = $this->option('extension')) {
            $this->extensionName = $ext;
            return;
        }

        $this->extensionName = $this->choice(
            'Please select an extension',
            $this->getInstalledExtensions()
        );
    }

    /**
     * @return string
     */
    protected function getExtensionNamespace(): string
    {
        $file = Parser::parseComposerJson("{$this->getExtPath()}composer.json");

        return $file->getAutoload()->getPsr4()->current()['namespace'];
    }

    /**
     * @return string
     */
    protected function getExtPath(): string
    {
        return ExtensionManagementUtility::extPath($this->extensionName);
    }

    /**
     * @return string
     */
    protected function getExtKey(): string
    {
        $ext = str_replace('_', '', $this->extensionName);

        return "tx_{$ext}";
    }

    /**
     * @return array
     */
    private function getInstalledExtensions(): array
    {
        $extensions = $this->folders(Environment::getExtensionsPath(), '== 0');

        return array_map(function (\SplFileInfo $extensionFolder) {
            return $extensionFolder->getFilename();
        }, $extensions, range(1, count($extensions)));
    }

    /**
     * @param string $path
     * @param string $depth
     *
     * @return array
     */
    private function folders(string $path, string $depth = '> 5'): array
    {
        $files = (new Finder())
            ->in($path)
            ->directories()
            ->depth($depth)
            ->sortByModifiedTime();

        return iterator_to_array($files->getIterator());
    }
}
