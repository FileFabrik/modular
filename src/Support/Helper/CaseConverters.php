<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Support\Helper;

use UnexpectedValueException;

/**
 * Changes Kebab Cases
 */
class CaseConverters
{

    public static function composerName(string $vendor, string $module)
    {
        // todo verify lower cases
        // no space
        // no slash

        return $vendor . '/' . $module;
    }

    /**
     * @param array $segments
     *
     * @return string
     */
    public static function composerNamespacing(array $segments): string
    {
        return implode('\\', $segments);
    }

    /**
     *
     * @param string $composerName
     *
     * @return array
     */
    public static function fromComposerName(string $composerName): array
    {
        $ex = self::splitComposerName($composerName);

        return (count($ex) === 2) ? $ex :
            throw new UnexpectedValueException('Composer-Name does not looks valid:' . $composerName);
    }

    public static function splitComposerName(string $composerName): array
    {
        return explode('/', $composerName);
    }

}