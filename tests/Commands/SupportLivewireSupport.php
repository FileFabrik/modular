<?php

declare(strict_types=1);
/**
 * PHP version 8.2
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Tests\Commands;

use InterNACHI\Modular\Support\LivewireSupport;

/**
 * Override if need
 */
class SupportLivewireSupport extends LivewireSupport
{
	/**
	 * Livewire Test-Component Name in tests to prevent from "handwritten" stuff inside tests
	 * do not use strings in tests ...
	 */
	public const TestingLivewireComponentName = 'TestLivewireComponent';
}
