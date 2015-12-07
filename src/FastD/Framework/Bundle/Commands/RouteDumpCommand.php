<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/18
 * Time: 下午4:32
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
use FastD\Routing\Route;
use FastD\Routing\Router;

/**
 * Class RouteDump
 *
 * @package FastD\Framework\Commands
 */
class RouteDumpCommand extends Command
{
    const STYLE_LIST = 1;
    const STYLE_DETAIL = 2;

    /**
     * @return string
     */
    public function getName()
    {
        return 'route:dump';
    }

    /**
     * @return void|$this
     */
    public function configure()
    {
        $this
            ->setOption('bundle')
            ->setOption('list')
            ->setDescription('Thank for you use routing dump tool.')
        ;
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        $router = \Routes::getRouter();

        $output->writeln('');

        $name = $input->getParameterArgument(0);

        $bundle = '' == ($bundle = $input->getParameterOption('bundle')) ? null : $bundle;
        $style = $input->hasParameterOption('--list');

        if (false !== strpos($bundle, ':')) {
            $bundle = str_replace(':', '\\', $bundle);
        }

        if ('' == $name) {
            $this->showRouteCollections($router, $output, $bundle, $style ? self::STYLE_LIST : self::STYLE_DETAIL);
        } else {
            $route = $router->getRoute($name);
            $this->formatOutput($route, $output, self::STYLE_DETAIL);
        }

        return 0;
    }

    /**
     * @param Router $router
     * @param Output $output
     * @param null   $bundleName
     * @return int
     */
    public function showRouteCollections(Router $router, Output $output, $bundleName = null, $style = self::STYLE_DETAIL)
    {
        $allRoutes = [];
        $bundles = $this->getContainer()->singleton('kernel')->getBundles();
        foreach ($router as $name => $route) {
            $callback = $route->getCallback();
            foreach ($bundles as $bundle) {
                if (0 === strpos($callback, $bundle->getNamespace())) {
                    $allRoutes[$bundle->getName()][] = $route;
                    break;
                } else {
                    $allRoutes['others'][] = $route;
                }
            }
        }

        if ($style === self::STYLE_LIST) {
            $output->write('Name' . str_repeat(' ', 25 - strlen('name')));
            $output->write('Method' . str_repeat(' ', 15 - strlen('Method')));
            $output->write('Schema' . str_repeat(' ', 15 - strlen('Schema')));
            $output->writeln('Path');
        }

        if (null === $bundleName) {
            foreach ($allRoutes as $name => $routes) {
                $output->writeln('Bundle: ' . $name, Output::STYLE_SUCCESS);
                foreach ($router as $route) {
                    $this->formatOutput($route, $output, $style);
                }
            }
            return 0;
        }

        foreach ($allRoutes as $name => $routes) {
            if ($name == $bundleName) {
                $output->writeln('Bundle: ' . $name, Output::STYLE_SUCCESS);
                foreach ($routes as $route) {
                    $this->formatOutput($route, $output, $style);
                }
            }
        }
        return 0;
    }

    public function formatOutput(Route $route, Output $output, $type = RouteDump::STYLE_DETAIL)
    {
        switch ($type) {
            case self::STYLE_DETAIL:
                $output->write(' Route [');
                $output->write('"' . $route->getName() . '"', Output::STYLE_SUCCESS);
                $output->writeln(']');
                $output->writeln(" Path:\t\t" . str_replace('//', '/', $route->getPath()));
                $output->writeln(" Method:\t\t" . implode(', ', $route->getMethods()));
                $output->writeln(" Format:\t\t" . implode(', ', $route->getFormats()));
                $output->writeln(" Callback:\t" . (is_callable($route->getCallback()) ? 'Closure' : $route->getCallback()));
                $output->writeln(" Defaults:\t" . implode(', ', $route->getDefaults()));
                $output->writeln(" Requirements:\t" . implode(', ', $route->getRequirements()));
                $output->writeln(" Path-Regex:\t" . $route->getPathRegex());
                $output->writeln('');
                break;
            case self::STYLE_LIST:
            default:
                $name = $route->getName();
                $method = implode(',', $route->getMethods());
                $schema = implode(',', $route->getSchema());
                $path = $route->getPath();
                $output->write(' ' . $name . str_repeat(' ', 25 - strlen($name)));
                $output->write($method . str_repeat(' ', 15 - strlen($method)));
                $output->write($schema . str_repeat(' ', 15 - strlen($schema)));
                $output->writeln($path);
        }

        return;
    }
}