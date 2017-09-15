<?php

namespace Lxh\Template;

use Lxh\View\Factory;

class Facade
{
    /**
     * 是否使用blade模板引擎
     *
     * @var bool
     */
    protected $useBladeEngine = false;

    /**
     * @var Factory
     */
    protected $blade;

    /**
     * @var View
     */
    protected $php;

    public function __construct()
    {
        $this->useBladeEngine = config('use-blade-engine');
    }

    public function share($key, $value = null)
    {
        if ($this->useBladeEngine)
            return $this->bladeFactory()->share($key, $value);

        return $this->phpView()->with($key, $value);
    }

    public function render($view, array $data)
    {
        if ($this->useBladeEngine)
            return $this->bladeFactory()->make($view, $data)->render();

        return $this->phpView()->render($view, $data);
    }

    public function exists($view)
    {
        if ($this->useBladeEngine)
            return $this->bladeFactory()->exists($view);

        return $this->phpView()->exist($view);
    }

    /**
     *
     * @return Factory
     */
    protected function bladeFactory()
    {
        return $this->blade ?: $this->blade = make('view.factory');
    }

    /**
     *
     * @return View
     */
    protected function phpView()
    {
        return $this->php ?: $this->php = make('view');
    }
}
