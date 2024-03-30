<?php declare(strict_types=1);
/**
 * PHP version 8.2
 *
 */
/** @copyright-header * */

namespace InterNACHI\Modular\Support\Helper;

use Illuminate\Support\Str;
use InterNACHI\Modular\Support\ModuleConfig;
use Livewire\Features\SupportConsoleCommands\Commands\ComponentParser;

/**
 * Override the original ComponentParser at some points to work properly with app-modules
 */
class LivewireComponentParser extends ComponentParser
{

    /**
     * @var string|null
     */
    private ?string $viewName = null;
    /**
     * @var ModuleConfig|null
     */
    private ?ModuleConfig $moduleConfig = null;

    /**
     * @param ModuleConfig $moduleConfig
     *
     * @return void
     */
    public function setModule(ModuleConfig $moduleConfig)
    {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @return string
     */
    public function viewName()
    {
        // refer to https://github.com/InterNACHI/modular/issues/85
        if (null === $this->moduleConfig) {
            return parent::viewName();
        }

        // otherwise make own component name
        // todo, has to be testet
        return $this->viewName ??= $this->moduleConfig->name . '::' .
            collect()
                ->filter()
                ->concat($this->directories)
                ->map([Str::class, 'kebab'])
                ->push($this->component)
                ->implode('.')
        ;
    }

    /**
     * @param string|null $viewName
     *
     * @return void
     */
    public function setViewName(?string $viewName): void
    {
        $this->viewName = $viewName;
    }

    /**
     * @return string|null
     */
    public function getViewName(): ?string
    {
        return $this->viewName;
    }

}