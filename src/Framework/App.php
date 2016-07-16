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

use FastD\Framework\Bundle\Commands\AssetInstallCommand;
use FastD\Standard\Commands\BundleGeneratorCommand;
use FastD\Standard\Commands\ConfigCacheCommand;
use FastD\Standard\Commands\FdbDataSetCommand;
use FastD\Standard\Commands\RouteCacheCommand;
use FastD\Standard\Commands\FdbReflexCommand;
use FastD\Standard\Commands\FdbSchemaCommand;
use FastD\Standard\Commands\RouteDumpCommand;
use FastD\Standard\Commands\SwooleCommand;
use FastD\Standard\Commands\ProdCommand;
use FastD\Framework\Kernel\AppKernel;
use FastD\Console\Command\Command;

/**
 * Class AppKernel
 *
 * @package FastD\Framework\Kernel
 */
class App extends AppKernel
{
    /**
     * Run framework into bootstrap file.
     *
     * @param $bootstrap
     * @return void
     */
    public static function run($bootstrap)
    {
        $app = new static($bootstrap);

        $app->bootstrap();

        $response = $app->createHttpRequestHandler();

        $response->send();

        $app->shutdown($app);
    }

    /**
     * @return Command[]
     */
    public function getDefaultCommands()
    {
        return [
            new AssetInstallCommand(),
            new RouteCacheCommand(),
            new RouteDumpCommand(),
            new FdbReflexCommand(),
            new FdbDataSetCommand(),
            new FdbSchemaCommand(),
            new BundleGeneratorCommand(),
            new SwooleCommand(),
            new ConfigCacheCommand(),
            new ProdCommand(),
        ];
    }
}