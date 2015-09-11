<?php

namespace Ccs\Controllers;

abstract class Controller
{
    private $app;

    final public function __construct(\Ccs\Container $app) {
        $this->app = $app;
    }

    public function getAppInstance() {
        return $this->app;
    }

    protected function render($template) {
        $this->app['slim']->render($template);
    }
}