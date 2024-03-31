<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Console\Commands\Admin;

use Illuminate\Support\Str;

/**
 *
 */
readonly class Stringularity
{

    /**
     * @param string $name
     */
    public function __construct(private string $name)
    {
    }

    /**
     * MyNamespaceSegment
     *
     * @return string
     */
    public function toClass(): string
    {
        return Str::studly($this->name);
    }

    /**
     * my-namespace-segment
     *
     * @return string
     */
    public function toName(): string
    {
        return Str::kebab($this->name);
    }

}