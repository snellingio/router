<?php

include __DIR__ . '/../vendor/autoload.php';

$router = new Snelling\Router();

$router->serve(
    [
        '/'        => Index::class,
        '/:string' => Path::class,
    ]
);

class Index
{
    public function get()
    {
        echo 'Hello World!';
    }
}

class Path
{
    public function get()
    {
        $router = new Snelling\Router();
        $path   = $router->path();
        echo 'Path: <pre><code>' . $path . '</code></pre>';
    }
}
