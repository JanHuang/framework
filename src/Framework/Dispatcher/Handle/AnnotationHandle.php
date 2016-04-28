<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/27
 * Time: 下午12:18
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Annotation\Annotation;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Dispatcher\Dispatch;
use Symfony\Component\Finder\Finder;


/**
 * 注释处理调度任务
 *
 * Class AnnotationHandle
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class AnnotationHandle extends Dispatch
{
    protected $routes = [];

    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.annotation.route';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        $bundles = $this->getContainer()->singleton('kernel')->getBundles();

        foreach ($bundles as $bundle) {
            $this->routes = array_merge($this->routes, $this->scanRoutes($bundle));
        }

        foreach ($this->routes as $prefix => $routes) {
            $closure = function () use ($routes) {
                foreach ($routes as $route) {
                    call_user_func_array("\\Routes::{$route['method']}", $route['parameters']);
                }
            };

            \Routes::group($prefix, $closure);
        }
    }

    /**
     * @param Bundle $bundle
     * @return array
     */
    protected function scanRoutes(Bundle $bundle)
    {
        $path = $bundle->getRootPath() . '/Controllers';

        if (!is_dir($path)) {
            return [];
        }

        $baseNamespace = $bundle->getNamespace() . '\\Controllers\\';
        $finder = new Finder();
        $files = $finder->name('*.php')->in($path)->files();

        $routes = [];

        foreach ($files as $file) {
            $className = $baseNamespace . pathinfo($file->getFileName(), PATHINFO_FILENAME);
            if (!class_exists($className)) {
                continue;
            }

            $annotation = new Annotation($className, null, 'Action');

            foreach ($annotation->getMethods() as $method) {
                if (false === ($route = $method->getParameters('Route'))) {
                    continue;
                }

                if (!isset($route['name'])) {
                    $route['name'] = $route[0];
                }

                $parameters = [
                    $route['name'],
                    $route[0],
                    $method->getParent()->getName() . '@' . $method->getName(),
                    isset($route['defaults']) ? $route['defaults'] : [],
                    isset($route['requirements']) ? $route['requirements'] : [],
                ];

                $parent = $method->getParent()->getParameters('Route');

                $method = false === $method->getParameters('Method') ? 'get' : $method->getParameters('Method')[0];

                if (!empty($parent) && isset($parent[0])) {
                    $routes[$parent[0]][] = [
                        'method' => strtolower($method),
                        'parameters' => $parameters
                    ];
                } else {
                    $routes['/'][] = [
                        'method' => $method,
                        'parameters' => $parameters
                    ];
                }

                unset($route, $method, $parameters, $parent);
            }
        }

        return $routes;
    }
}