<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11
 * Time: 下午3:57
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Framework\Kernel;

use FastD\Config\Config;
use FastD\Container\Container;
use FastD\Framework\Dispatcher\Dispatcher;
use FastD\Http\Request;
use FastD\Http\Response;
use FastD\Framework\Bundle\Bundle;
use FastD\Routing\Router;

/**
 * Class AppKernel
 *
 * @package FastD\Framework\Kernel
 */
abstract class AppKernel extends Terminal
{
    /**
     * The FastD application version.
     *
     * @const string
     */
    const VERSION = '2.0.x';

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $rootPath;
    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Bundle[]
     */
    protected $bundles = array();

    /**
     * Constructor. Initialize framework environment.
     *
     * @param $env
     */
    public function __construct($env)
    {
        $this->environment = $env;

        $this->debug = in_array($env, ['dev', 'test']) ? true : false;
    }

    /**
     * Get custom bundles method.
     *
     * @return Bundle[]
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Get application running environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Bootstrap application. Loading cache,bundles,configuration,router and other.
     *
     * @return void
     */
    public function boot()
    {
        $this->initializeBundles();

        $this->initializeContainer();

        $this->container->singleton('kernel.dispatch')->dispatch('handle.error', [$this->isDebug(), null]);

        $config = $this->initializeConfigure();

        $routing = $this->initializeRouting();

        foreach ($this->bundles as $bundle) {
            $bundle->registerConfiguration($config, $this->environment);
            $bundle->registerRouting($routing, $this->environment);
        }
    }

    /**
     * Initialize application register bundles.
     *
     * @return void
     */
    public function initializeBundles()
    {
        $this->bundles = $this->registerBundles();
    }

    /**
     * Initialize application container.
     *
     * @return void
     */
    public function initializeContainer()
    {
        $this->container = new Container([
            'kernel.template'       => 'FastD\\Template\\Template',
            'kernel.logger'         => 'FastD\\Logger\\Logger',
            'kernel.database'       => 'FastD\\Database\\Database',
            'kernel.config'         => 'FastD\\Config\\Config',
            'kernel.storage'        => 'FastD\\Storage\\StorageManager',
        ]);

        $this->registerService($this->container);

        $this->container->set('kernel.container', $this->container);
        $this->container->set('kernel.dispatch', new Dispatcher($this->container));
        $this->container->set('kernel', $this);
    }

    /**
     * Initialize application configuration.
     *
     * @return Config
     */
    public function initializeConfigure()
    {
        $config = $this->container->get('kernel.config')->singleton();

        $config->setVariable([
            'root.path' => $this->getRootPath(),
            'env'       => $this->getEnvironment(),
            'debug'     => $this->isDebug(),
            'version'   => AppKernel::VERSION,
        ]);

        $this->registerConfigurationVariable($config);
        $this->registerConfiguration($config);

        return $config;
    }

    /**
     * Loaded application routing.
     *
     * Loaded register bundle routes configuration.
     *
     * @return Router
     */
    public function initializeRouting()
    {
        $router = \Routes::getRouter();

        $this->container->set('kernel.routing', $router);

        return $router;
    }

    /**
     * @return \FastD\Http\Request
     */
    public function createHttpRequestClient()
    {
        $request = Request::createRequestHandle();

        $this->container->set('kernel.request', $request);

        return $request;
    }

    /**
     * @return Response
     */
    public function createHttpRequestHandler()
    {
        $client = $this->createHttpRequestClient();

        return $this->container->singleton('kernel.dispatch')->dispatch('handle.http', [$client]);
    }

    /**
     * Get application work space directory.
     *
     * @return string
     */
    public function getRootPath()
    {
        if (null === $this->rootPath) {
            $this->rootPath = dirname((new \ReflectionClass($this))->getFileName());
        }

        return $this->rootPath;
    }
}