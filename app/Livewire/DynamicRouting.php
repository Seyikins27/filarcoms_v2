<?php

namespace App\Livewire;

use Livewire\Component;

class DynamicRouting extends Component
{
    public static $routes=[];
    public $middleware;
    public $method;

    public static function make()
    {
        return new static();
    }

    public static function setRoutes(array $routes)
    {
        static::$routes = $routes;

    }

    public function setMiddleware(null|string $middleware)
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function setMethod(null|string $method)
    {
        $this->method = $method;
        return $this;
    }

    public static function getRoutes()
    {
        return static::$routes;
    }
    public function render()
    {
        return view('livewire.dynamic-routing');
    }
}
