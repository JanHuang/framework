<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Framework\Kernel;

use FastD\Event\Event;
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

class AppKernel extends Terminal implements AppKernelInterface
{
    use Aware;

    /**
     * The FastD application version.
     *
     * @const string
     */
    const VERSION = '2.1.0';

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
     * App constructor.
     *
     * @param array $bootstrap
     */
    public function __construct(array $bootstrap)
    {
        $this->rootPath = $bootstrap['root.path'] ?? '.';

        $this->environment = $bootstrap['env'] ?? 'dev';

        $this->debug = in_array((string) $this->environment, ['dev', 'test']) ? true : false;

        $this->bundles = $bootstrap['bundles'] ?? [];
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
     * Bootstrap application.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (!$this->booted) {

            $this->initializeContainer();
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
            'kernel.event'      => Event::class,
        ]);

        $this->container->set('kernel.container', $this->container);
        $this->container->set('kernel', $this);
    }

    /**
     * Initialize application configuration.
     *
     * @return void
     */
    public function initializeConfigure()
    {
        $config = $this->container->singleton('kernel.config');

        $config->load($this->getRootPath() . '/config.cache');
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
            $this->container->singleton('kernel.event');
        } else {
            include $this->getRootPath() . '/routes.cache';
        }
    }

    /**
     * @return Response
     */
    public function createHttpRequestHandler()
    {
        $client = Request::createRequestHandle();

        $this->container->set('kernel.request', $client);

        return $this->handleHttpRequest($client);
    }

    /**
     * Get application work space directory.
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }
}