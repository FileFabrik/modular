<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Support\Helper;

use Illuminate\Support\Str;

/**
 * Changes Kebab Cases
 */
class CaseConverters
{

    /**
     * Test chaking kebab study
     *
     * @return void
     */
    public function typeShaking()
    {
    }

    public static function toComposerVendorName(string $name)
    {
        // todo does not start with -
        return Str::kebab($name);
    }

    public static function toComposerModuleName(string $name)
    {
        return Str::kebab($name);
    }

    public static function toNamespace(string $name)
    {
        return Str::studly($name);
    }

    public static function clear()
    {
        // todo later
        // strip all but not
        // double spaces
        // dots not allowed
        // double --

    }

    public static function composerName(string $vendor, string $module)
    {
        // todo verify lower cases
        // no space
        // no slash

        return $vendor . '/' . $module;
    }

    public static function composerNamespacing(array $segments)
    {
        return implode('\\', $segments);
    }


}
