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

use FastD\Annotation\AnnotationExtractor;
use FastD\Finder\Finder;
use FastD\Framework\Dispatcher\Dispatch;

/**
 * 注释处理调度任务
 *
 * Class AnnotationHandle
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class AnnotationHandle extends Dispatch
{
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
        $finder = new Finder();

        $bundles = $this->getContainer()->singleton('kernel')->getBundles();
        foreach ($bundles as $bundle) {
            $baseNamespace = $bundle->getNamespace() . '\\Events\\Http\\';
            $path = $bundle->getRootPath() . '/Events/Http';
            $files = $finder->in($path)->files();
            foreach ($files as $file) {
                $className = $baseNamespace . pathinfo($file->getFileName(), PATHINFO_FILENAME);
                $extractor = AnnotationExtractor::getExtractor($className);
                $methods = [];
                foreach ($extractor->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if (false === strpos($method->getName(), 'Action')) {
                        continue;
                    }
                    $methods[] = $method->getName();
                }

                $routesAnnotation = [];
                foreach ($methods as $method) {
                    $annotation = $extractor->getMethodAnnotation($method);
                    $routeAnnotation = $extractor->getParameters($annotation, 'Route');
                    if (empty($routeAnnotation)) {
                        continue;
                    }
                    $routeAnnotation['class'] = $className;
                    $routeAnnotation['action'] = $method;
                    $routesAnnotation[] = $routeAnnotation;
                }

                $routes = function () use ($routesAnnotation) {
                    foreach ($routesAnnotation as $routeAnnotation) {
                        $args = [];
                        if (!isset($routeAnnotation['method'])) {
                            $methodName = 'any';
                        } else {
                            if (is_array($routeAnnotation['method'])) {
                                $methodName = 'match';
                                $args[] = $routeAnnotation['method'];
                            } else {
                                $methodName = $routeAnnotation['method'];
                            }
                        }

                        if (isset($routeAnnotation[0])) {
                            if (!isset($routeAnnotation['name'])) {
                                $args[] = $routeAnnotation[0];
                            } else {
                                $args[] = [
                                    $routeAnnotation[0],
                                    'name' => $routeAnnotation['name']
                                ];
                            }
                        }

                        $args[] = $routeAnnotation['class'] . '@' . $routeAnnotation['action'];

                        call_user_func_array("\\Routes::{$methodName}", $args);
                    }
                };

                $with = $extractor->getParameters($extractor->getClassAnnotation(), 'Route');

                if (!empty($with)) {
                    \Routes::with($with[0], $routes);
                } else {
                    $routes();
                }

                unset($extractor);
            }
        }
    }
}