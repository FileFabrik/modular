<?php

namespace InterNACHI\Modular\Console\Commands\Make;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InterNACHI\Modular\Support\Statics;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;
use Livewire\Livewire;

if (class_exists(MakeCommand::class)) {
    class MakeLivewire extends MakeCommand
    {
        use Modularize;

        public function getAliases(): array
        {
            return ['make:livewire', 'livewire:make'];
        }

        public function handle()
        {
            if ($module = $this->module()) {
                Config::set('livewire.class_namespace', $module->qualify(Statics::getLivewireNamespace()));
                Config::set('livewire.view_path', $module->path('resources/views/livewire'));

                $app = $this->getLaravel();

                $defaultManifestPath = $app['livewire']->isRunningServerless()
                    ? '/tmp/storage/bootstrap/cache/livewire-components.php'
                    : $app->bootstrapPath('cache/livewire-components.php');
                /*
                 * https://github.com/InterNACHI/modular/pull/60/files
                                $componentsFinder = new LivewireComponentsFinder(
                                    new Filesystem(),
                                    Config::get('livewire.manifest_path') ?? $defaultManifestPath,
                                    $module->path('src/' . Statics::getLivewireLocation()),
                                );

                                $app->instance(LivewireComponentsFinder::class, $componentsFinder);*/
                if (class_exists("Livewire\LivewireComponentsFinder")) {
                    $componentsFinder = new \Livewire\LivewireComponentsFinder(
                        new Filesystem(),
                        Config::get('livewire.manifest_path') ?? $defaultManifestPath,
                        $module->path('src/' . Statics::getLivewireLocation()),
                    );

                    $app->instance(\Livewire\LivewireComponentsFinder::class, $componentsFinder);
                }
            }

            parent::handle();
        }

        protected function createClass($force = false, $inline = false)
        {
            if ($module = $this->module()) {
                $name = Str::of($this->argument('name'))
                           ->split('/[.\/(\\\\)]+/')
                           ->map([Str::class, 'studly'])
                           ->join(DIRECTORY_SEPARATOR)
                ;

                $classPath = $module->path('src/' . Statics::getLivewireLocation() . '/' . $name . '.php');

                if (File::exists($classPath) && !$force) {
                    $this->line("<options=bold,reverse;fg=red> WHOOPS-IE-TOOTLES </> 😳 \n");
                    $this->line("<fg=red;options=bold>Class already exists:</> {$this->parser->relativeClassPath()}");

                    return false;
                }

                $this->ensureDirectoryExists($classPath);

                File::put($classPath, $this->parser->classContents($inline));

                $component_name = Str::of($name)
                                     ->explode('/')
                                     ->filter()
                                     ->map([Str::class, 'kebab'])
                                     ->implode('.')
                ;

                $fully_qualified_component = Str::of($this->argument('name'))
                                                ->prepend(Statics::getLivewireNamespace() . '/')
                                                ->split('/[.\/(\\\\)]+/')
                                                ->map([Str::class, 'studly'])
                                                ->join('\\')
                ;

                Livewire::component("{$module->name}::{$component_name}", $module->qualify($fully_qualified_component));

                return $classPath;
            }

            return parent::createClass($force, $inline);
        }
    }
}