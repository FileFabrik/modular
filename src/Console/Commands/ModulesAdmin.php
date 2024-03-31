<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Console\Commands;

use Illuminate\Console\Command;
use InterNACHI\Modular\Console\Commands\Admin\Inputs;
use InterNACHI\Modular\Support\Helper\CaseConverters;
use InterNACHI\Modular\Support\ModuleRegistry;

/**
 *
 */
class ModulesAdmin extends Command
{
    private static $taskSelector          = [1 => 'Create new Module', 9 => 'Irgendwas anderes'];
    private static $taskMethodMap         = [1 => 'create_module', 9 => 'something_else'];
    public static  $flagNewComposerVendor = 'new Vendor';
    /**
     * @var string
     */
    protected $signature = 'modules:admin';

    /**
     * @var string
     */
    protected $description = 'administrate modules';

    public function __construct(private readonly ModuleRegistry $module_registry)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line('handle something');

        $taskName = $this->tasks();

        $this->line('you select ' . $taskName);
        $this->handleSelectedTask($taskName);
    }

    private function handleSelectedTask(string $selectedTask)
    {
        $selectedKey = array_search($selectedTask, self::$taskSelector, true);

        if (!$selectedKey || !($method = self::$taskMethodMap[$selectedKey] ?? null)) {
            // this error method not found
            $this->error('Task not callable: ' . $selectedTask);

            return false;
        }

        $this->{'task_' . $method}();
    }

    // options

    protected function tasks()
    {
        return $this->choice(
            'What do you want to do?',
            self::$taskSelector,
            1,
            1,
            $allowMultipleSelections = false,
        );
    }

    protected function task_create_module()
    {
        $this->line('here we go in ' . __METHOD__);

        $existingVendor = Inputs::existingComposerVendors();

        if ($existingVendor === self::$flagNewComposerVendor) {
            $this->line('Right now, lets create a new Vendor for your Module ' . __METHOD__);
            $vendor_namespace_input = Inputs::composerVendor();
        }
        else {
            $this->line('Fine, use a Vendor that already exists:' . $existingVendor);
            $vendor_namespace_input = strtolower($existingVendor);
        }

        $composer_vendor_name      = CaseConverters::toComposerVendorName($vendor_namespace_input);
        $composer_vendor_namespace = CaseConverters::toNamespace($vendor_namespace_input);

        $this->line('you typed:' . $vendor_namespace_input);
        $this->line('Converted to:' . $composer_vendor_namespace);

        $this->line('Namespace Segment to:' . $composer_vendor_name);

        // your company in composer.json "name": "$vendor_namespace/laravel",

        $moduleName = Inputs::moduleName();

        $this->line('you typed:' . $moduleName);
        $composerModuleName = CaseConverters::toComposerModuleName($moduleName);
        $this->line('Converted for composer to:' . $composerModuleName);

        $module_namespace = CaseConverters::toNamespace($moduleName);
        $this->line('Converted Classname Namespace:' . $module_namespace);
        // compile into

        $this->line('composer name will be "name":"' . CaseConverters::composerName($composer_vendor_name,
                                                                                    $composerModuleName) . '"');

        $this->line('Psr-4 Namespace will be: ' . CaseConverters::composerNamespacing([$composer_vendor_namespace,
                                                                                       $module_namespace]));
        // at the end we have a configuration which we are using to create a new module

        $this->line('want to have vendore-prefixed module directory layout or the modulename as directory?');


        // create the vendor directory if nee
        dd($this->module_registry);
    }
}
