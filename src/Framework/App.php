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

namespace FastD\Framework;

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
use FastD\Framework\Kernel\AppKernelInterface;
use FastD\Framework\Kernel\Terminal;

/**
 * Class AppKernel
 *
 * @package FastD\Framework\Kernel
 */
class App extends Terminal implements AppKernelInterface
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
        $this->rootPath = $bootstrap['root.path'];

        $this->environment = $bootstrap['env'] ?? 'prod';

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
            'kernel.event'      => Event::class,
        ]);

        $this->container->set('kernel.container', $this->container);
        $this->container->set('kernel', $this);
    }

    /**
     * Initialize application register bundles.
     *
     * @return void
     */
    public function initializeBundles()
    {
        foreach ($this->bundles as $bundle) {
            $bundle->setContainer($this->getContainer());
        }
    }

    /**
     * Initialize application configuration.
     *
     * @return void
     */
    public function initializeConfigure()
    {
        $config = $this->container->singleton('kernel.config');

        if ($this->isDebug()) {
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

        $event = $this->container->singleton('kernel.event');

        $event->on('handle.http', function (Request $request) {
            $request->getPathInfo();
        });

        return $event->trigger('handle.http', [$client]);
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

    /**
     * Run framework into bootstrap file.
     *
     * @param $bootstrap
     * @return mixed
     */
    public static function run($bootstrap)
    {
        $app = new static($bootstrap);

        $app->bootstrap();

        $response = $app->createHttpRequestHandler();

        $response->send();

        $app->shutdown($app);
    }
}