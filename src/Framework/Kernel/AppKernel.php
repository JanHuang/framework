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
use FastD\Database\Fdb;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Dispatcher\Dispatcher;
use FastD\Http\Request;
use FastD\Http\Response;
use FastD\Routing\Router;
use Monolog\Logger;

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
    const VERSION = '2.0.0';

    const ENV_PROD = 'prod';
    const ENV_TEST = 'test';
    const ENV_DEV = 'dev';

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
     * @var bool
     */
    protected $booted = false;

    /**
     * Constructor. Initialize framework environment.
     *
     * @param $env
     */
    public function __construct($env)
    {
        $this->environment = $env;

        $this->debug = in_array($env, [AppKernel::ENV_DEV, AppKernel::ENV_TEST]) ? true : false;
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
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Bootstrap application. Loading cache,bundles,configuration,router and other.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->booted) {

            $this->initializeContainer();
            $this->initializeBundles();

            $config = $this->initializeConfigure();
            $routing = $this->initializeRouting();

            foreach ($this->bundles as $bundle) {
                $bundle->registerConfiguration($config, $this->environment);
                $bundle->registerRouting($routing, $this->environment);
            }

            $this->booted = true;
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
            'kernel.database' => Fdb::class,
            'kernel.config' => Config::class,
            'kernel.logger' => Logger::class,
        ]);

        $this->registerService($this->container);

        $this->container->set('kernel.container', $this->container);
        $this->container->set('kernel.dispatch', new Dispatcher($this->container));
        $this->container->set('kernel', $this);

        $this->container->singleton('kernel.dispatch')->dispatch('handle.error', [$this->isDebug()]);
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
            'env' => $this->getEnvironment(),
            'debug' => $this->isDebug(),
            'version' => AppKernel::VERSION,
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

        if ($this->isDebug()) {
            $this->container->singleton('kernel.dispatch')->dispatch('handle.annotation.route');
        } else {
            include $this->getRootPath() . '/routes.cache';
        }

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