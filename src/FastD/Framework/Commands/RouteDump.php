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

namespace FastD\Framework\Commands;

use FastD\Console\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
use FastD\Routing\RouteInterface;
use FastD\Routing\Router;

/**
 * Class RouteDump
 *
 * @package FastD\Framework\Commands
 */
class RouteDump extends Command
{
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
            ->setOption('bundle', null)
            ->setDescription('Thank for you use routing dump tool.')
        ;
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @return void
     */
    public function execute(Input $input, Output $output)
    {
        $router = \Routes::getRouter();

        $output->writeln('');

        $name = $input->getParameterArgument(0);

        $bundle = '' == ($bundle = $input->getParameterOption('bundle')) ? null : $bundle;

        if (false !== strpos($bundle, ':')) {
            $bundle = str_replace(':', '\\', $bundle);
        }

        if ('' == $name) {
            $this->showRouteCollections($router, $output, $bundle);
        } else {
            $route = $router->getRoute($name);
            $this->formatOutput($route, $output);
        }

        return 0;
    }

    public function showRouteCollections(Router $router, Output $output, $bundleName = null)
    {
        $allRoutes = [];
        $bundles = $this->getContainer()->get('kernel')->getBundles();
        foreach ($router->getCollections() as $name => $route) {
            $callback = $route->getCallback();
            if (is_array($callback)) {
                $callback = get_class($callback[0]) . '@' . $callback[1];
            }
            foreach ($bundles as $bundle) {
                if (0 === strpos($callback, $bundle->getNamespace())) {
                    $allRoutes[$bundle->getFullName()][] = $route;
                    break;
                } else {
                    $allRoutes['others'][] = $route;
                }
            }
        }

        if (null === $bundleName) {
            foreach ($allRoutes as $name => $routes) {
                $output->writeln($name, Output::STYLE_SUCCESS);
                foreach ($routes as $route) {
                    $this->formatOutput($route, $output);
                }
            }
            return 0;
        }

        foreach ($allRoutes as $name => $routes) {
            if ($name == $bundleName) {
                $output->writeln($name, Output::STYLE_SUCCESS);
                foreach ($routes as $route) {
                    $this->formatOutput($route, $output);
                }
            }

        }
    }

    public function formatOutput(RouteInterface $routeInterface, Output $output)
    {
        $group = ('' == ($group = str_replace('//', '/', $routeInterface->getGroup())) ? '/' : $group);
        $output->write('Route [');
        $output->write('"' . $routeInterface->getName() . '"', Output::STYLE_SUCCESS);
        $output->writeln(']');
        $output->writeln("Group:\t\t" . str_replace('//', '/', $group));
        $output->writeln("Path:\t\t" . str_replace('//', '/', $routeInterface->getPath()));
        $output->writeln("Method:\t\t" . implode(', ', $routeInterface->getMethods()));
        $output->writeln("Format:\t\t" . implode(', ', $routeInterface->getFormats()));
        $output->writeln("Callback:\t" . (is_callable($routeInterface->getCallback()) ? 'Closure' : $routeInterface->getCallback()));
        $output->writeln("Defaults:\t" . implode(', ', $routeInterface->getDefaults()));
        $output->writeln("Requirements:\t" . implode(', ', $routeInterface->getRequirements()));
        $output->writeln("Path-Regex:\t" . $routeInterface->getPathRegex());
        $output->writeln('');
    }
}