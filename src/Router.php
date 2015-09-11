<?php

namespace Ccs;

class Router
{
    private $controllers = [];
    private $default;

    public function __construct($slim) {

        $slim->get('/', function () use ($slim) {
            if(!isset($this->controllers[$this->default])) {
                throw new \RuntimeException('No default route has been registered in src/Container.php.');
            }
            $this->call($this->controllers[$this->default]);
        });

        $slim->map('/:class', function ($class) use ($slim) {
            if(!isset($this->controllers[$class])) {
                throw new \RuntimeException('This controller does not exist: ' . $class);
            }
            $this->call($this->controllers[$class]);
        })->via('GET', 'POST');

        $slim->map('/:class/:function', function ($class, $function) use ($slim) {
            if(!isset($this->controllers[$class])) {
                throw new \RuntimeException('This function: ' . $function . ' does not exist for the controller: ' . $class);
            }
            $this->call($this->controllers[$class], $function);
        })->via('GET', 'POST');
    }

    public function register($class, \Ccs\Controllers\Controller $controller) {
        $this->controllers[$class] = $controller;
    }

    public function defaultRoute($class) {
        $this->default = $class;
    }

    public function call($controller, $function = 'index') {
        $controller->$function();
    }
}