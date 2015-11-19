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
use FastD\Framework\Dispatch\Dispatcher;
use FastD\Http\Request;
use FastD\Framework\Bundle;

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
     * @var Container
     */
    protected $container;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var Bundle[]
     */
    protected $bundles = array();

    /**
     * Constructor. Initialize framework components.
     *
     * @param $env
     */
    public function __construct($env)
    {
        $this->environment = $env;

        $this->debug = 'prod' === $this->environment ? false : true;
    }

    /**
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

        $this->initializeConfigure();

        $this->initializeRouting();
    }

    /**
     * Initialize application register bundles.
     */
    public function initializeBundles()
    {
        $this->registerBundles();
    }

    /**
     * Initialize application container.
     *
     * @return void
     */
    public function initializeContainer()
    {
        $this->container = new Container(array_merge([
            'kernel.template'   => 'FastD\\Template\\Template',
            'kernel.logger'     => 'FastD\\Logger\\Logger',
            'kernel.database'   => 'FastD\\Database\\Database',
            'kernel.config'     => 'FastD\\Config\\Config',
            'kernel.storage'    => 'FastD\\Storage\\StorageManager',
            'kernel.request'    => 'FastD\\Http\\Request::createRequestHandle',
            'kernel.http.handler' => 'FastD\\Framework\\Handle\\HttpHandler',
        ], (null === ($services = $this->registerService()) ? [] : $services)));

        unset($services);

        $this->container->set('kernel', $this);
    }

    /**
     * Initialize application configuration.
     *
     * @return Config
     */
    public function initializeConfigure()
    {
        $config = $this->container->get('kernel.config');

        $config->setVariable([
            'root.path' => $this->getRootPath(),
            'env'       => $this->getEnvironment(),
            'debug'     => $this->isDebug(),
            'version'   => AppKernel::VERSION,
        ]);

        $cache = $this->getRootPath() . '/config.php.cache';

        if (file_exists($cache)) {
            $config->set(include $cache);
            return $config;
        }

        $config->load($this->getRootPath() . '/config/global.php');

        $this->registerConfiguration($config);

        foreach ($this->getBundles() as $bundle) {
            $file = $bundle->getRootPath() . '/Resources/config/config.php';
            if (file_exists($file)) {
                $config->load($file);
            }
        }

        return $config;
    }

    /**
     * Loaded application routing.
     *
     * Loaded register bundle routes configuration.
     */
    public function initializeRouting()
    {
        $router = \Routes::getRouter();

        $this->container->set('kernel.routing', $router);
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

    public function createHttpRequestHandler()
    {
        $client = $this->createHttpRequestClient();

        return $this->container->get('kernel.http.handler')->handle($client);
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