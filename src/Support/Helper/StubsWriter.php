<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Support\Helper;

use Illuminate\Support\Str;

/**
 * Handling writing stubs
 */
class StubsWriter
{
    protected function writeStubs()
    {
        $this->title('Creating initial module files');

        // do not use right here. all stuff has to be configured before
        $tests_base = config('app-modules.tests_base', 'Tests\TestCase');

        $placeholders = [
            'StubBasePath'                   => $this->base_path,
            'StubModuleNamespace'            => $this->module_namespace,
            'StubComposerNamespace'          => $this->composer_namespace,
            'StubModuleNameSingular'         => Str::singular($this->module_name),
            'StubModuleNamePlural'           => Str::plural($this->module_name),
            'StubModuleName'                 => $this->module_name,
            'StubClassNamePrefix'            => $this->class_name_prefix,
            'StubComposerName'               => $this->composer_name,
            'StubMigrationPrefix'            => date('Y_m_d_His'),
            'StubFullyQualifiedTestCaseBase' => $tests_base,
            'StubTestCaseBase'               => class_basename($tests_base),
        ];

        $search  = array_keys($placeholders);
        $replace = array_values($placeholders);

        foreach ($this->getStubs() as $destination => $stub_file) {
            $contents    = file_get_contents($stub_file);
            $destination = str_replace($search, $replace, $destination);
            $filename    = "{$this->base_path}/{$destination}";

            $output = str_replace($search, $replace, $contents);

            if ($this->filesystem->exists($filename)) {
                $this->line(" - Skipping <info>{$destination}</info> (already exists)");
                continue;
            }

            $this->filesystem->ensureDirectoryExists($this->filesystem->dirname($filename));
            $this->filesystem->put($filename, $output);

            $this->line(" - Wrote to <info>{$destination}</info>");
        }

        $this->newLine();
    }
}