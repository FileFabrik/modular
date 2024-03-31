<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Console\Commands\Admin;

use InterNACHI\Modular\Support\Helper\CaseConverters;

/**
 * Tooling Class
 */
class Module
{

    public static function fromInput(string $vendorName, string $moduleName)
    {
        // validate both, make lower-case
    }

    /**
     * Split Composer-Name into Vendor-Name and Module-Name and get back validated and usable object
     *
     * @param string $composerName
     *
     * @return MakeModuleConfig
     */
    public static function fromComposerName(string $composerName): MakeModuleConfig
    {
        return (new MakeModuleConfig(...CaseConverters::fromComposerName($composerName)));
    }
}