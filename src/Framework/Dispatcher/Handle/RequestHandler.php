<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/19
 * Time: 下午11:39
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Debug\Exceptions\HttpException;
use FastD\Framework\Bundle\Controllers\ControllerInterface;
use FastD\Framework\Dispatcher\Dispatch;
use FastD\Http\Response;
use FastD\Routing\Route;

/**
 * Framework kernel boot http handle.
 * Handle every time user request.
 *
 * Class RequestHandler
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class RequestHandler extends Dispatch
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.http';
    }

    /**
     * @param Route $route
     * @return mixed
     * @throws \Exception
     */
    public function handleRoute(Route $route)
    {
        $callback = $route->getCallback();

        list($controller, $action) = explode('@', $callback);
        $controller = str_replace(':', '\\', $controller);
        $bundle = trim(substr($controller, 0, strpos($controller, 'Controller')), '\\');
        $this->getContainer()->singleton('kernel')->setActiveBundle($bundle); unset($bundle);

        $service = $this->container->set('request_callback', $controller)->get('request_callback');
        if (($instance = $service->singleton()) instanceof ControllerInterface) {
            $instance->setContainer($this->container);
            $instance->currentAction = $action;
        }

        try {
            try {
                $response = $service->__initialize();
                if ($response instanceof Response) {
                    return $response;
                }
            } catch (\Exception $e) {}
            $response = call_user_func_array([$service, $action], $route->getParameters());
            $instance->statusCode = $response->getStatusCode() === 200 ? 1 : 0;
            if (1 !== $instance->statusCode) {
                $instance->code = $response->getStatusCode();
                $instance->msg = $response->getContent();
            }

            unset($instance);
            return $response;
        } catch (HttpException $e) {
            $response = $e->getContent();
            if ($response instanceof Response) {
                return $response;
            }
            return new Response($e->getContent(), $e->getStatusCode(), $e->getHeaders());
        } catch (\Exception $e) {
            if ($this->getContainer()->get('kernel')->isDebug()) {
                return new Response($e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
            }

            return new Response('server interval error.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        $route = $this->getContainer()->singleton('kernel.routing')->match($parameters[0]->getMethod(), $parameters[0]->getPathInfo());

        return $this->handleRoute($route);
    }
}