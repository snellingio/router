<?php
declare (strict_types=1);

namespace Snelling;

class Router
{

    private static $tokens = [
        ':string' => '([a-zA-Z]+)',
        ':number' => '([0-9]+)',
        ':alpha'  => '([a-zA-Z0-9-_]+)',
    ];

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->server = $_SERVER;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        $path = '/';
        if (!empty($this->server['PATH_INFO'])) {
            $path = $this->server['PATH_INFO'];
        } elseif (!empty($this->server['ORIG_PATH_INFO']) && '/index.php' !== $this->server['ORIG_PATH_INFO']) {
            $path = $this->server['ORIG_PATH_INFO'];
        } else {
            if (!empty($this->server['REQUEST_URI'])) {
                $path = (strpos($this->server['REQUEST_URI'], '?') > 0) ? strstr($this->server['REQUEST_URI'], '?', true) : $this->server['REQUEST_URI'];
            }
        }
        if (strlen($path) > 1) {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    /**
     * @param array $routes
     * @return bool
     */
    public function serve(array $routes): bool
    {
        $method     = strtolower($this->server['REQUEST_METHOD']) ?? 'get';
        $path       = $this->path();
        $discovered = null;
        $matches    = [];
        if (isset($routes[$path])) {
            $discovered = $routes[$path];
        } elseif ($routes) {
            foreach ($routes as $pattern => $name) {
                $pattern = strtr($pattern, self::$tokens);
                if (preg_match('#^/?' . $pattern . '/?$#', $path, $match)) {
                    $discovered = $name;
                    $matches    = $match;
                    break;
                }
            }
        }
        $handler = null;
        if ($discovered) {
            if (is_string($discovered)) {
                $handler = new $discovered();
            } elseif (is_callable($discovered)) {
                $handler = $discovered();
            }
        }
        if ($handler && method_exists($handler, $method)) {
            call_user_func_array([$handler, $method], $matches);

            return true;
        }

        return false;
    }
}
