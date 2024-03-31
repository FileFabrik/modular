<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Tests\Console\Commands;

/**
 * @coversDefaultClass \InterNACHI\Modular\Console\Commands\ModulesAdmin;
 **/
class ModulesAdminTest extends \InterNACHI\Modular\Tests\TestCase
{
    /** @test */
    public function it_can_reload_caches()
    {
        $this->artisan('modules:admin');
    }
}