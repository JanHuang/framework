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

namespace FastD\Framework\Bundle\Events\Http;

use FastD\Framework\Container\ContainerAware;
use FastD\Framework\Bundle\Events\EventInterface;
use FastD\Framework\Extensions\Preset;
use FastD\Http\Session\Storage\RedisStorage;
use FastD\Http\Session\Session;
use FastD\Http\Session\SessionHandler;
use FastD\Database\Database;
use FastD\Http\RedirectResponse;
use FastD\Storage\StorageManager;
use FastD\Http\Response;
use FastD\Http\JsonResponse;
use FastD\Http\XmlResponse;
use FastD\Template\Template;

/**
 * Class Event
 *
 * @package FastD\Framework\Bundle\Events\Http
 */
class Event extends ContainerAware implements EventInterface
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
    public function get($name, array $parameters = array(), $flag = false)
    {
        if (!$flag) {
            return $this->container->singleton($name, $parameters);
        }

        return $this->container->instance($name, $parameters);
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
        $url = $this->get('kernel.routing')->generateUrl($name, $parameters, $format);
        if ('http' !== substr($url, 0, 4)) {
            $url = ('/' === ($path = $this->get('kernel.request')->getBaseUrl()) ? '' : $path) . $url;
            $url = str_replace('//', '/', $url);
        }

        return $this->get('kernel.request')->getSchemeAndHttpAndHost() . $url;
    }

    /**
     * @param      $name
     * @return string
     */
    public function asset($name, $verion = null)
    {

    }

    /**
     * @param       $name
     * @param array $parameters
     * @return  Response
     */
    public function forward($name, array $parameters = [])
    {

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
            $extensions = [new Preset()];
            $paths = [
                $this->get('kernel')->getRootPath() . '/views',
                $this->get('kernel')->getRootPath() . '/../src'
            ];
            $bundles = $this->getContainer()->singleton('kernel')->getBundles();
            foreach ($bundles as $bundle) {
                $paths[] = dirname($bundle->getRootPath());
                $extensions = array_merge($extensions, $bundle->registerExtensions());
            }

            $options = [];
            if (!($isDebug = $this->container->singleton('kernel')->isDebug())) {
                $options = [
                    'cache' => $this->get('kernel')->getRootPath() . '/storage/templates',
                    'debug' => $isDebug,
                ];
            }

            $this->template = $this->container->singleton('kernel.template', [$paths, $options]);
            foreach ($extensions as $extension) {
                $extension->setContainer($this->getContainer());
                $this->template->addExtension($extension);
            }
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