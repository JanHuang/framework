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

use FastD\Framework\Dispatcher\Dispatcher;
use FastD\Framework\Bundle\Bundle;
use FastD\Container\Container;
use FastD\Container\Aware;
use FastD\Storage\Storage;
use FastD\Routing\Router;
use FastD\Http\Response;
use FastD\Config\Config;
use FastD\Database\Fdb;
use FastD\Http\Request;
use FastD\Debug\Debug;

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
    protected $bundles = [];

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
     * @param $name
     * @return Bundle|null
     */
    public function getBundle($name)
    {
        foreach ($this->getBundles() as $bundle) {
            if (false !== strpos($bundle->getName(), $name)) {
                return $bundle;
            }
        }

        return null;
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
     * Initialize application container.
     *
     * @return void
     */
    public function initializeContainer()
    {
        $this->container = new Container([
            'kernel.database'   => Fdb::class,
            'kernel.config'     => Config::class,
            'kernel.storage'    => Storage::class,
            'kernel.routing'    => '\\Routes::getRouter',
            'kernel.debug'      => Debug::enable($this->isDebug()),
        ]);

        $this->container->set('kernel.container', $this->container);
        $this->container->set('kernel.dispatch', new Dispatcher($this->container));
        $this->container->set('kernel', $this);

        $this->container->singleton('kernel.dispatch')->dispatch('handle.error');
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

        $this->bundles = $this->registerBundles();

        foreach ($this->bundles as $bundle) {
            $bundle->setContainer($this->getContainer());
            $bundle->registerRouting($routing, $this->getEnvironment());
            $bundle->registerConfiguration($config, $this->getEnvironment());
        }

        unset($config, $routing);
    }

    /**
     * Initialize application configuration.
     *
     * @return void
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

        if ($this->isDebug()) {
            $this->registerConfiguration($config);
            $debug = $this->getContainer()->singleton('kernel.debug');
            $debug->addConfig($debug->getBar(), $config);
            unset($debug);
        } else {
            $config->load($this->getRootPath() . '/config.cache');
        }
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
        if ($this->isDebug()) {
            return $this->container->singleton('kernel.dispatch')->dispatch('handle.annotation.route');
        }
        include $this->getRootPath() . '/routes.cache';
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


    /**
     * @param Container $container
     */
    abstract public function registerService(Container $container);
}