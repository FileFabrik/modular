<?php

namespace InterNACHI\Modular\Console\Commands\Make;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InterNACHI\Modular\Support\Helper\LivewireComponentParser;
use InterNACHI\Modular\Support\LivewireSupport;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;
use Livewire\Livewire;

if (class_exists(MakeCommand::class)) {
	/**
	 * @property LivewireComponentParser $parser
	 */
	class MakeLivewire extends MakeCommand
	{
		use Modularize;

		protected $parser;

		public function getAliases(): array
		{
			return ['make:livewire', 'livewire:make'];
		}

		/**
		 * ugly copied the Livewire MakeCommand handle method to override the viewName()
		 */
		protected function parentHandle()
		{
			$this->parser = new LivewireComponentParser(
				config('livewire.class_namespace'),
				config('livewire.view_path'),
				$this->argument('name'),
				$this->option('stub'),
			);

			// important the your-module-name::livewire.klicker
			$this->parser->setModule($this->module());

			if (! $this->isClassNameValid($name = $this->parser->className())) {
				$this->line("<options=bold,reverse;fg=red> WHOOPS! </> 😳 \n");
				$this->line("<fg=red;options=bold>Class is invalid:</> {$name}");

				return;
			}

			if ($this->isReservedClassName($name)) {
				$this->line("<options=bold,reverse;fg=red> WHOOPS! </> 😳 \n");
				$this->line("<fg=red;options=bold>Class is reserved:</> {$name}");

				return;
			}

			$force = $this->option('force');
			$inline = $this->option('inline');
			$test = $this->option('test') || $this->option('pest');
			$testType = $this->option('pest') ? 'pest' : 'phpunit';

			$showWelcomeMessage = $this->isFirstTimeMakingAComponent();

			$class = $this->createClass($force, $inline);
			$view = $this->createView($force, $inline);

			if ($test) {
				$test = $this->createTest($force, $testType);
			}

			if ($class || $view) {
				$this->line("<options=bold,reverse;fg=green> COMPONENT CREATED </> 🤙\n");
				$class && $this->line("<options=bold;fg=green>CLASS:</> {$this->parser->relativeClassPath()}");

				if (! $inline) {
					$view && $this->line("<options=bold;fg=green>VIEW:</>  {$this->parser->relativeViewPath()}");
				}

				if ($test) {
					$test && $this->line("<options=bold;fg=green>TEST:</>  {$this->parser->relativeTestPath()}");
				}

				if ($showWelcomeMessage && ! app()->runningUnitTests()) {
					$this->writeWelcomeMessage();
				}
			}
		}

		public function handle()
		{
			if ($module = $this->module()) {
				Config::set('livewire.class_namespace', $module->qualify(LivewireSupport::getLivewireNamespace()));
				Config::set('livewire.view_path', $module->path('resources/views/livewire'));
				$this->parentHandle();
			} else {
				parent::handle();
			}
		}

		protected function createClass($force = false, $inline = false)
		{
			if ($module = $this->module()) {
				// todo move out to make it testable
				$name = Str::of($this->argument('name'))
						   ->split('/[.\/(\\\\)]+/')
						   ->map([Str::class, 'studly'])
						   ->join(DIRECTORY_SEPARATOR);

				$classPath = $module->path('src/'.LivewireSupport::getLivewireLocation().'/'.$name.'.php');

				if (File::exists($classPath) && ! $force) {
					$this->line("<options=bold,reverse;fg=red> WHOOPS-IE-TOOTLES </> 😳 \n");
					$this->line("<fg=red;options=bold>Class already exists:</> {$this->parser->relativeClassPath()}");

					return false;
				}

				$this->ensureDirectoryExists($classPath);
				// todo move out to make it testable
				$component_name = Str::of($name)
									 ->explode('/')
									 ->filter()
									 ->map([Str::class, 'kebab'])
									 ->implode('.');

				// todo parser for viewName and parser for tag-name which is testable
				$this->parser->setViewName("{$module->name}::livewire.{$component_name}");

				$fContent = $this->parser->classContents($inline);
				File::put($classPath, $fContent);

				// todo move out to make it testable
				$fully_qualified_component = Str::of($this->argument('name'))
												->prepend(LivewireSupport::getLivewireNamespace().'/')
												->split('/[.\/(\\\\)]+/')
												->map([Str::class, 'studly'])
												->join('\\');

				Livewire::component($this->parser->getViewName(), $module->qualify($fully_qualified_component));

				$tagName = "livewire:{$module->name}::{$component_name}";

				$this->line("<options=bold;fg=green>TAG:</>  <{$tagName}/>");

				return $classPath;
			}

			return parent::createClass($force, $inline);
		}
	}
}
