<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Console\Commands\Admin;

use InterNACHI\Modular\Support\Helper\CaseConverters;

/**
 * All creation configs for a new Module to transport it into stubs ...or somewhere
 * understand it as a kind of facade
 * todo make sure vendor name an module name are present
 */
class MakeModuleConfig
{

    private Stringularity $vendor;
    private Stringularity $module;

    /**
     * @var bool if true outputs the module as /app-modules/your-company/your-module otherwise /app-modules/your-module
     */
    private bool $flag_output_as_vendor_module = false;

    /**
     * @param string|Stringularity $composer_vendor_name
     * @param string|Stringularity $composer_module_name
     */
    public function __construct(string|Stringularity $composer_vendor_name,
                                string|Stringularity $composer_module_name)
    {
        $this->vendor = !is_string($composer_vendor_name) ?: new Stringularity($composer_vendor_name);
        $this->module = !is_string($composer_module_name) ?: new Stringularity($composer_module_name);
    }

    /**
     * compile the typical composer.json {"name":"your-company/your-module"}
     *
     * @return string
     */
    public function toComposerName(): string
    {
        return CaseConverters::composerName($this->vendor->toName(), $this->module->toName());
    }

    /**
     * MyVendor\\MyModule
     *
     * @return string
     */
    public function toNamespace(): string
    {
        return CaseConverters::composerNamespacing([$this->vendor->toClass(), $this->module->toClass()]);
    }
}