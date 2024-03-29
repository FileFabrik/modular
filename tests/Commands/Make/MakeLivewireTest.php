<?php

namespace InterNACHI\Modular\Tests\Commands\Make;

use InterNACHI\Modular\Console\Commands\Make\MakeLivewire;
use InterNACHI\Modular\Tests\Commands\SupportStatics;
use InterNACHI\Modular\Tests\Concerns\TestsMakeCommands;
use InterNACHI\Modular\Tests\Concerns\WritesToAppFilesystem;
use InterNACHI\Modular\Tests\TestCase;
use Livewire\Livewire;
use Livewire\LivewireManager;
use Livewire\LivewireServiceProvider;

class MakeLivewireTest extends TestCase
{
    use WritesToAppFilesystem;
    use TestsMakeCommands;

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(Livewire::class)) {
            $this->markTestSkipped('Livewire is not installed.');
        }
    }

    public function test_it_overrides_the_default_commands(): void
    {
        $this->requiresLaravelVersion('10.0');

        $this->artisan('make:livewire', ['--help' => true])
             ->expectsOutputToContain('--module')
             ->assertExitCode(0)
        ;

        $this->artisan('livewire:make', ['--help' => true])
             ->expectsOutputToContain('--module')
             ->assertExitCode(0)
        ;
    }

    public function test_it_scaffolds_a_component_in_the_module_when_module_option_is_set(): void
    {
        $command = MakeLivewire::class;

        $arguments           = ['name' => SupportStatics::TestingLivewireComponentName];
        $expected_path       =
            'src/' . SupportStatics::getLivewireLocation() . '/' . SupportStatics::TestingLivewireComponentName . '.php';
        $expected_substrings = [
            'namespace Modules\TestModule\\' . SupportStatics::getLivewireNamespace(),
            'class ' . SupportStatics::TestingLivewireComponentName,
        ];

        $this->assertModuleCommandResults($command, $arguments, $expected_path, $expected_substrings);

        $expected_view_path = 'resources/views/livewire/test-livewire-component.blade.php';
        $this->assertModuleFile($expected_view_path);
    }

    public function test_it_scaffolds_a_component_with_nested_folders(): void
    {
        $command             = MakeLivewire::class;
        $arguments           = ['name' => 'test.my-component/' . SupportStatics::TestingLivewireComponentName];
        $expected_path       =
            'src/' . SupportStatics::getLivewireLocation() . '/Test/MyComponent/' . SupportStatics::TestingLivewireComponentName . '.php';
        $expected_substrings = [
            'namespace Modules\TestModule\\' . SupportStatics::getLivewireNamespace() . '\Test\MyComponent',
            'class ' . SupportStatics::TestingLivewireComponentName,
        ];

        $this->assertModuleCommandResults($command, $arguments, $expected_path, $expected_substrings);

        $expected_view_path = 'resources/views/livewire/test/my-component/test-livewire-component.blade.php';
        $this->assertModuleFile($expected_view_path);
    }

    public function test_it_scaffolds_a_component_in_the_app_when_module_option_is_missing(): void
    {
        $command             = MakeLivewire::class;
        $arguments           = ['name' => SupportStatics::TestingLivewireComponentName];
        $expected_path       =
            'app/' . SupportStatics::getLivewireLocation() . '/' . SupportStatics::TestingLivewireComponentName . '.php';
        $expected_substrings = [
            'namespace App\\' . SupportStatics::getLivewireNamespace(),
            'class ' . SupportStatics::TestingLivewireComponentName,
        ];

        $this->assertBaseCommandResults($command, $arguments, $expected_path, $expected_substrings);

        $expected_view_path = 'resources/views/livewire/test-livewire-component.blade.php';
        $this->assertBaseFile($expected_view_path);
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [LivewireServiceProvider::class]);
    }

    protected function getPackageAliases($app)
    {
        return array_merge(parent::getPackageAliases($app), ['Livewire' => LivewireManager::class]);
    }
}