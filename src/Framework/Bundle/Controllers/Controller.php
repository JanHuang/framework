<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 上午11:18
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Framework\Bundle\Controllers;

use FastD\Container\ContainerAware;
use FastD\Debug\Exceptions\Http\HttpException;
use FastD\Database\DriverInterface;
use FastD\Framework\Kernel\AppKernel;
use FastD\Storage\StorageInterface;
use FastD\Storage\StorageManager;
use FastD\Http\RedirectResponse;
use FastD\Http\Response;
use FastD\Http\JsonResponse;
use FastD\Http\Session\Session;
use FastD\Http\Session\Storage\SessionStorageInterface;

/**
 * Class Controller
 *
 * @package FastD\Framework\Bundle\Controllers
 */
class Controller extends ContainerAware implements ControllerInterface
{
    const SERVER_VERSION = AppKernel::VERSION;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var StorageManager
     */
    protected $storage;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Get custom defined helper obj.
     *
     * @param string $name
     * @param array $parameters
     * @param bool  $flag
     * @return mixed
     */
    public function get($name, array $parameters = [], $flag = false)
    {
        return $flag ? $this->container->instance($name, $parameters) : $this->container->singleton($name, $parameters);
    }

    /**
     * @param StorageInterface|null $storageInterface
     * @return Session
     */
    public function getSession(StorageInterface $storageInterface)
    {
        if ($this->session instanceof Session) {
            return $this->session;
        }

        $this->session = $this->container->singleton('kernel.request')->getSessionHandle();

        return $this->session;
    }

    /**
     * @param null  $connection
     * @param array $options
     * @return DriverInterface
     */
    public function getDriver($connection = null, array $options = [])
    {
        if (null === $this->driver) {
            $this->driver = $this->get('kernel.database', [$this->getParameters('database')]);
        }

        return $this->driver->getDriver($connection);
    }

    /**
     * @param       $connection
     * @param array $options
     * @return \FastD\Storage\StorageInterface
     */
    public function getStorage($connection, array $options = [])
    {
        if (null === $this->storage) {
            $this->storage = $this->get('kernel.storage', [$this->getParameters('storage')]);
        }

        return $this->storage->getConnection($connection);
    }

    /**
     * Get custom config parameters.
     *
     * @param string $name
     * @return mixed
     */
    public function getParameters($name = null)
    {
        return $this->get('kernel.config')->get($name);
    }

    /**
     * @param       $name
     * @param array $parameters
     * @param string$format
     * @return string
     */
    public function generateUrl($name, array $parameters = array(), $format = '')
    {
        return $this->get('kernel.dispatch')->dispatch('handle.url', [$name, $parameters, $format]);
    }

    /**
     * @param               $name
     * @param   string|int  $version
     * @return string
     */
    public function asset($name, $version = null)
    {
        return $this->get('kernel.dispatch')->dispatch('handle.asset', [$name, $version]);
    }

    /**
     * @param       $name
     * @param array $parameters
     * @return  Response
     */
    public function forward($name, array $parameters = [])
    {
        return $this->get('kernel.dispatch')->dispatch('handle.forward', [$name, $parameters]);
    }

    /**
     * Render template to html or return content.
     *
     * @param            $view
     * @param array      $parameters
     * @param bool|false $flag
     * @return Response|string
     */
    public function render($view, array $parameters = array(), $flag = false)
    {
        $content = $this->get('kernel.dispatch')->dispatch('handle.tpl')->render($view, $parameters);

        return $flag ? $content : $this->responseHtml($content);
    }

    /**
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function response($data, $status = Response::HTTP_OK, array $headers = [])
    {
        switch ($this->get('kernel.request')->getFormat()) {
            case 'json':
                return $this->responseJson($data, $status, $headers);
            case 'php':
            case 'jsp':
            case 'asp':
            case 'text':
            case 'html':
            default:
                return $this->responseHtml($data, $status, $headers);
        }
    }

    /**
     * Redirect url.
     *
     * @param       $url
     * @param array $parameters
     * @param int   $statusCode
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, array $parameters = [], $statusCode = 302, array $headers = [])
    {
        return new RedirectResponse($url, $statusCode, $headers);
    }

    /**
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function responseHtml($data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new Response($data, $status, $headers);
    }

    /**
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return JsonResponse
     */
    public function responseJson(array $data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param int    $statusCode
     * @param string $content
     * @param array  $headers
     * @throws \Exception
     */
    public function throwException($content = "Forbidden", $statusCode = Response::HTTP_FORBIDDEN, array $headers = [])
    {
        throw new HttpException($content, $statusCode, $headers);
    }
}