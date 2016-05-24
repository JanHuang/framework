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
use FastD\Container\Aware;
use FastD\Container\Container;
use FastD\Database\Fdb;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Dispatcher\Dispatcher;
use FastD\Http\Request;
use FastD\Http\Response;
use FastD\Routing\Router;
use FastD\Debug\Debug;
use FastD\Storage\Storage;

/**
 * Class AppKernel
 *
 * @package FastD\Framework\Kernel
 */
abstract class AppKernel extends Terminal
{
    use Aware;

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
     * @var Bundle[]
     */
    protected $bundles = array();

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @var null|string
     */
    protected $active_bundle = null;

    /**
     * Constructor. Initialize framework environment.
     *
     * @param $env
     */
    public function __construct($env)
    {
        $this->environment = $env;

        $this->debug = in_array($env, [AppKernel::ENV_DEV, AppKernel::ENV_TEST]) ? true : false;

        Debug::enable($this->isDebug());
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
     * @return null|string
     */
    public function getActiveBundle()
    {
        return $this->bundles[$this->active_bundle];
    }

    /**
     * @param null|string $active_bundle
     * @return $this
     */
    public function setActiveBundle($active_bundle)
    {
        $this->active_bundle = $active_bundle;

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
            $this->initializeRouting();
            $this->initializeConfigure();

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
        $config = $this->getContainer()->singleton('kernel.config');
        $routing = $this->getContainer()->singleton('kernel.routing');

        foreach ($this->registerBundles() as $bundle) {
            $this->bundles[$bundle->getNamespace()] = $bundle->setContainer($this->getContainer());
            if ($this->isDebug()) {
                $bundle->registerRouting($routing, $this->getEnvironment());
                $bundle->registerConfiguration($config, $this->getEnvironment());
            }
        }

        unset($bundles, $config, $routing);
    }

    /**
     * Initialize application container.
     *
     * @return void
     */
    public function initializeContainer()
    {
        $this->container = new Container([
            'kernel.database'   => Fdb::class,
            'kernel.config'     => Config::class,
            'kernel.routing'    => '\\Routes::getRouter',
            'kernel.storage'    => Storage::class
        ]);

        $this->registerService($this->container);

        $this->container->set('kernel.container', $this->container);
        $this->container->set('kernel.dispatch', new Dispatcher($this->container));
        $this->container->set('kernel', $this);

        $this->container->singleton('kernel.dispatch')->dispatch('handle.error');
    }

    /**
     * Initialize application configuration.
     *
     * @return Config
     */
    public function initializeConfigure()
    {
        $config = $this->container->singleton('kernel.config');

        $config->setVariable([
            'root.path' => $this->getRootPath(),
            'env'       => $this->getEnvironment(),
            'debug'     => $this->isDebug(),
            'version'   => AppKernel::VERSION,
        ]);

        $this->registerConfigurationVariable($config);

        if (!$this->isDebug() && file_exists($this->getRootPath() . '/config.cache')) {
            $config->load($this->getRootPath() . '/config.cache');
            return $config;
        }

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
        if (!$this->isDebug() && file_exists($this->getRootPath() . '/routes.cache')) {
            return include $this->getRootPath() . '/routes.cache';
        }

        return $this->container->singleton('kernel.dispatch')->dispatch('handle.annotation.route');
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