<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Console\Commands\Admin;

use InterNACHI\Modular\Console\Commands\ModulesAdmin;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Reusable Inputs
 */
class Inputs
{
    public static function existingComposerVendors()
    {
        // todo read vendors from app-modules
        // todo insert default vendor from app config
        return select('Use an existing Vendor?',
                      ['existing "Filefabrik"',
                       'existing "Other Namespace"',
                       '' . ModulesAdmin::$flagNewComposerVendor]);
    }

    /**
     * New Custom Vendor
     *
     * @return string
     */
    public static function composerVendor()
    {
        // your company in composer.json "name": "$vendor_namespace/laravel",
        return text(label      : 'Your vendor namespace in composer?',
                    placeholder: 'your-company',
                    default    : func_get_args()['cfgNamespace'] ?? '',
                    hint       : '// your company in composer.json "name": "$vendor_namespace/laravel"');
    }

    public static function moduleName()
    {
        // your company in composer.json "name": "$vendor_namespace/laravel",
        return text(label      : 'Name of your module?',
                    placeholder: 'My Amazing module',
                    default    : func_get_args()['cfgNamespace'] ?? '',
                    hint       : "/app-modules/my-amazing-module\n Namespace will be MyAmazingModule");
    }

}
