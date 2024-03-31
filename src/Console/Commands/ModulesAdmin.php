<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Console\Commands;

use Illuminate\Console\Command;
use InterNACHI\Modular\Console\Commands\Admin\Inputs;
use InterNACHI\Modular\Console\Commands\Admin\MakeModuleConfig;
use InterNACHI\Modular\Console\Commands\Admin\Stringularity;
use InterNACHI\Modular\Support\ModuleRegistry;

/**
 *
 */
class ModulesAdmin extends Command
{
    private static array $taskSelector          = [1 => 'Create new Module', 9 => 'Irgendwas anderes'];
    private static array $taskMethodMap         = [1 => 'create_module', 9 => 'something_else'];
    public static string $flagNewComposerVendor = 'new Vendor';
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
        );
    }

    protected function task_create_module()
    {
        // collect a new Module Config
        $this->line('here we go in ' . __METHOD__);

        $existingVendor = Inputs::existingComposerVendors();

        if ($existingVendor === self::$flagNewComposerVendor) {
            $this->line('Right now, lets create a new Vendor for your Module ' . __METHOD__);
            $vendor_namespace_input = Inputs::composerVendor();
        }
        else {
            $this->line('Fine, use a Vendor that already exists:' . $existingVendor);
            $vendor_namespace_input = $existingVendor;
        }

        // from here we can convert a lot of things we need.
        $vendorStringularity = new Stringularity($vendor_namespace_input);

        $this->line('you typed:' . $vendor_namespace_input);
        $this->line('Converted to:' . $vendorStringularity->toName());

        $this->line('Namespace Segment to:' . $vendorStringularity->toClass());

        // your company in composer.json "name": "$vendor_namespace/laravel",

        $moduleName = Inputs::moduleName();

        $moduleStringularity = new Stringularity($moduleName);

        $newModuleConfig = new MakeModuleConfig(composer_vendor_name: $vendorStringularity,
                                                composer_module_name: $moduleStringularity);

        $this->line('you typed:' . $moduleName);

        $this->line('Converted for composer to:' . $moduleStringularity->toName());

        $this->line('Converted Classname Namespace:' . $moduleStringularity->toClass());
        // compile into

        $this->line('composer name will be "name":"' . $newModuleConfig->toComposerName());

        $this->line('Psr-4 Namespace will be: ' . $newModuleConfig->toNamespace());
        // at the end we have a configuration which we are using to create a new module

        $this->line('want to have vendore-prefixed module directory layout or the modulename as directory?');

        // create the vendor directory if nee
        dd($this->module_registry);
    }
}