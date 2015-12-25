<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/8/2
 * Time: 上午12:13
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Command\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
use FastD\Console\IO\OutputInterface;

class RouteCacheCommand extends Command
{
    public function getName()
    {
        return 'route:cache';
    }

    public function configure(){}

    public function execute(Input $input, Output $output)
    {
        $kernel = $this->getApplication()->getKernel();
        $kernel->boot();
        $routing = $kernel->getContainer()->singleton('kernel.routing');
        $caching = $kernel->getRootPath() . '/route.cache';
        // Init caching file.
        file_put_contents($caching, '<?php' . PHP_EOL);
        foreach ($routing as $route) {
            $methods = '[\'' . implode('\', \'', $route->getMethods()) . '\']';
            $name = '' == $route->getName() ? '' : "'name' => '{$route->getName()}'";
            $path = "['{$route->getPath()}', {$name}]";
            $default = array() === $route->getDefaults() ? '[]' : '[]';
            $requirements = array() === $route->getRequirements() ? '[]' : '[]';
            $routeCaching = "Routes::match({$methods}, {$path}, '{$route->getCallback()}', {$default}, {$requirements})";
            if (null != $route->getHost()) {
                $routeCaching .= '->setHost([\'' . implode('\',\'', $route->getHost() ?? []) .'\'])';
            }
            if (null != $route->getSchema() && $route->getSchema() != ['http']) {
                $routeCaching .= '->setSchema([\'' . implode('\',\'', $route->getSchema() ?? []) .'\'])';
            }
            if (null != $route->getFormats()) {
                $routeCaching .= '->setFormats([\'' . implode('\',\'', $route->getFormats() ?? []) . '\'])';
            }

            // Routes::match();
            file_put_contents($caching, $routeCaching . ';' . PHP_EOL, FILE_APPEND);
        }
        $output->write('Caching to ' . $caching . '......');
        $output->writeln('    [OK]', OutputInterface::STYLE_SUCCESS);
        return 1;
    }
}