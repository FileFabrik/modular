<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Support;

/**
 * Prevents from using config('app-modules.livewire_location')
 */
class LivewireSupport
{

    /**
     * String segment where to output a created livewire component in the current module
     * older versions uses 'Http/Livewire'
     * newer versions have to use 'Livewire', means in the regular app/Livewire and in your Module app-modules/my-module/src/Livewire
     */
    public static function getLivewireLocation()
    {
        return config('app-modules.livewire_location');
    }

    /**
     * Without the Http
     * 'Http\\Livewire'
     */
    public static function getLivewireNamespace()
    {
        return config('app-modules.livewire_namespace');
    }
}