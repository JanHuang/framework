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

use FastD\Framework\Container\ContainerAware;
use FastD\Database\Database;
use FastD\Template\Template;
use FastD\Storage\StorageManager;
use FastD\Http\RedirectResponse;
use FastD\Http\Response;
use FastD\Http\JsonResponse;
use FastD\Http\XmlResponse;
use FastD\Http\Session\Storage\RedisStorage;
use FastD\Http\Session\Session;
use FastD\Http\Session\SessionHandler;


/**
 * Class Event
 *
 * @package FastD\Framework\Bundle\Events\Http
 */
class Controller extends ContainerAware implements ControllerInterface
{
    const SERVER_NAME = 'FastD';
    const SERVER_VERSION = '2.0';

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var StorageManager
     */
    protected $storage;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Template
     */
    protected $template;

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
     * @return Session
     */
    public function getSession()
    {
        if ($this->session instanceof Session) {
            return $this->session;
        }

        $config = $this->getParameters('session');

        $storage = new RedisStorage($config['host'], $config['port'], isset($config['auth']) ? $config['auth'] : null);

        $handler = new SessionHandler($storage);

        $this->session = $this->get('kernel.request')->getSessionHandle($handler);

        unset($storage, $handler);

        return $this->session;
    }

    /**
     * @param null  $connection
     * @param array $options
     * @return \FastD\Database\Driver\Driver
     */
    public function getConnection($connection = null, array $options = [])
    {
        if (null === $this->database) {
            $this->database = $this->get('kernel.database', [$this->getParameters('database')]);
        }

        return $this->database->getConnection($connection);
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
     * @param bool|false $flog
     * @return Response|string
     */
    public function render($view, array $parameters = array(), $flog = false)
    {
        if (null === $this->template) {
            $this->template = $this->get('kernel.dispatch')->dispatch('handle.tpl');
        }

        $content = $this->template->render($view, $parameters);

        if ($flog) {
            return $content;
        }

        return $this->responseHtml($content);
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
     * @return JsonResponse|Response|XmlResponse
     */
    public function response($data, $status = Response::HTTP_OK, array $headers = [])
    {
        switch ($this->get('kernel.request')->getFormat()) {
            case 'json':
                return $this->responseJson($data, $status, $headers);
            case 'xml':
                return $this->responseXml($data, $status, $headers);
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
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return XmlResponse
     */
    public function responseXml(array $data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new XmlResponse($data, $status, $headers);
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
}