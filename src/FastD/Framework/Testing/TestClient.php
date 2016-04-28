<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/26
 * Time: 下午4:04
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Tests;

use FastD\Container\Container;
use FastD\Http\Request;
use FastD\Http\Response;

/**
 * Class TestClient
 *
 * @package FastD\Framework\Tests
 */
class TestClient
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * TestClient constructor.
     * @param Container $container
     * @param Request|null $request
     */
    public function __construct(Container $container, Request $request = null)
    {
        $this->container = $container;

        if (null !== $request) {
            $this->request = $request;
        }
    }

    /**
     * @param       $method
     * @param       $path
     * @param array $parameters
     * @return Response
     */
    public function testResponse($method, $path, array $parameters = [])
    {
        $this->request->server->set('REQUEST_METHOD', $method);
        $this->request->server->set('PATH_INFO', $path);

        return $this->container->singleton('kernel.dispatch')->dispatch('handle.http', [$this->request]);
    }

    public function testService($name, array $parameters = null)
    {

    }

    public function testRepository($name, array $parameters = null)
    {

    }
}