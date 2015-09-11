<?php

namespace Ccs;

class Container extends \Pimple\Container
{
    public function __construct(\Slim\Slim $slim) {
        $this['slim'] = $slim;

        $router = new Router($slim);

        $router->register('index', new Controllers\Index($this));

        $router->register('csv', new Controllers\Csv($this));
        $router->register('csv/upload', new Controllers\Csv($this));

        $router->defaultRoute('index');
    }
}