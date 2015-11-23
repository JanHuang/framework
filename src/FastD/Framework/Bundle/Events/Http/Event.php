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

use FastD\Config\Config;
use FastD\Database\Database;
use FastD\Database\Driver\Driver;
use FastD\Framework\Bundle\Events\ContainerAware;
use FastD\Http\Response;
use FastD\Http\Session\Session;
use FastD\Http\Session\SessionHandler;
use FastD\Http\Session\Storage\RedisStorage;
use FastD\Logger\Logger;
use FastD\Http\RedirectResponse;
use FastD\Http\Request;
use FastD\Routing\Router;
use FastD\Storage\StorageManager;

/**
 * Class BaseEvent
 *
 * @package FastD\Framework\Events
 */
class Event extends ContainerAware
{
    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var StorageManager
     */
    protected $storage;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Router
     */
    protected $routing;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Get custom defined helper obj.
     *
     * @param string $helper
     * @param array $parameters
     * @param bool $newInstance
     * @return mixed
     */
    public function get($helper, $parameters = array(), $newInstance = false)
    {
        return $this->container->get($helper, $parameters, $newInstance);
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

        $this->session = $this->getRequest()->getSessionHandle($handler);

        unset($storage, $handler);

        return $this->session;
    }

    /**
     * get database connection driver
     *
     * @param string $connection
     * @return Driver
     */
    public function getConnection($connection = null)
    {
        if (null === $this->database) {
            $this->database = $this->get('kernel.database', [$this->getParameters('database')]);
        }

        return $this->database->getConnection($connection);
    }

    /**
     * @param $vars
     */
    public function dump($vars)
    {
        return $this->container->get('kernel.debug')->dump($vars);
    }

    /**
     * @param $connection
     * @return \FastD\Storage\StorageInterface
     */
    public function getStorage($connection)
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
        if (null === $this->config) {
            $this->config = $this->get('kernel.config');
        }

        return $this->config->get($name);
    }

    /**
     * @param       $name
     * @param array $parameters
     * @param string$format
     * @return string
     */
    public function generateUrl($name, array $parameters = array(), $format = '')
    {
        $url = $this->getRouting()->generateUrl($name, $parameters, $format);
        if ('http' !== substr($url, 0, 4)) {
            $url = ('/' === ($path = $this->getRequest()->getBaseUrl()) ? '' : $path) . $url;
            $url = str_replace('//', '/', $url);
        }
        return $this->getRequest()->getSchemeAndHttpAndHost() . $url;
    }

    /**
     * @param      $name
     * @param null $host
     * @param null $path
     * @return string
     */
    public function asset($name, $host = null, $path = null)
    {
        if (null === $host) {
            try {
                $host = $this->getParameters('assets.host');
            } catch (\InvalidArgumentException $e) {
                $host = str_replace(['http:', 'https:'], '', $this->getRequest()->getSchemeAndHttpAndHost());
            }
        }

        if (null === $path) {
            try {
                $path = $this->getParameters('assets.path');
            } catch (\InvalidArgumentException $e) {
                $path = $this->getRequest()->getRootPath();

                if ('' != pathinfo($path, PATHINFO_EXTENSION)) {
                    $path = pathinfo($path, PATHINFO_DIRNAME);
                }
            }
        }

        return $host . str_replace('//', '/', $path . '/' . $name);
    }

    /**
     * @param     $url
     * @param int $statusCode
     * @param array $headers
     * @return \FastD\Http\RedirectResponse
     */
    public function redirect($url, $statusCode = 302, array $headers = [])
    {
        return new RedirectResponse($url, $statusCode, $headers);
    }

    /**
     * @param       $name
     * @param array $parameters
     * @return  mixed
     */
    public function forward($name, array $parameters = [])
    {
        $route = $this->getRouting()->getRoute($name);
        $callback = $route->getCallback();
        if (is_array($callback)) {
            $event = $callback[0];
            $handle = $callback[1];
        } else {
            list ($event, $handle) = explode('@', $callback);
        }

        $event = $this->container->set($name, $event)->get($name);

        if ($event instanceof Event) {
            $event->setContainer($this->container);
        }
        if (method_exists($event, '__initialize')) {
            $response = $this->container->getProvider()->callServiceMethod($event, '__initialize');
            if (null !== $response && $response instanceof Response) {
                return $response;
            }
        }
        return $response = $this->container->getProvider()->callServiceMethod($event, $handle, array_merge($route->getParameters(), $parameters));
    }

    public function render($template, array $parameters = array())
    {
        $paths = $this->getParameters('template.paths');
        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            $paths[] = dirname($bundle->getRootPath());
        }
        $options = [];
        if (!($isDebug = $this->container->get('kernel')->isDebug())) {
            $options = [
                'cache' => $this->getParameters('template.cache'),
                'debug' => $isDebug,
            ];
        }
        $self = $this;
        $this->template = $this->container->get('kernel.template', [$paths, $options]);
        $this->template->addGlobal('request', $this->getRequest());
        $this->template->addFunction(new \Twig_SimpleFunction('url', function ($name, array $parameters = [], $format = '') use ($self) {
            return $self->generateUrl($name, $parameters, $format);
        }));
        $this->template->addFunction(new \Twig_SimpleFunction('asset', function ($name, $host = null, $path = null) use ($self) {
            return $self->asset($name, $host, $path);
        }));
        unset($paths, $options);

        return $this->template->render($template, $parameters);
    }
}