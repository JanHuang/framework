<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/19
 * Time: 下午5:34
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Command\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;

/**
 * Class Generator
 *
 * @package FastD\Framework\Commands
 */
class BundleGeneratorCommand extends Command
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'bundle:generate';
    }

    /**
     * @return void|$this
     */
    public function configure()
    {
        $this->setArgument('bundle');
        $this->setDescription('Thank for you use bundle generator tool.');
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @return void
     */
    public function execute(Input $input, Output $output)
    {
        try {
            $bundle = $input->get('bundle');
        } catch(\Exception $e) {
            $output->writeln('Bundle name is empty or null. Please you try again.');
            exit;
        }

        if (empty($bundle)) {
            $output->writeln('Bundle name is empty or null. Please you try again.');
            exit;
        }

        $bundle = str_replace(':', DIRECTORY_SEPARATOR, $bundle) . 'Bundle';

        $source = $this->getApplication()->getKernel()->getRootPath() . '/../src';

        $this->builderStructure($source, $bundle, str_replace(DIRECTORY_SEPARATOR, '', $bundle));

        $output->writeln('Building in ' . $source);
    }

    public function builderStructure($path, $bundle, $fullName)
    {
        $bundlePath = implode(DIRECTORY_SEPARATOR, array(
            $path,
            $bundle
        ));

        foreach (array(
                     'Controllers',
                     'Repository',
                     'Extensions',
                     'Commands',
                     'Services',
                     'Standard',
                     'Resources/views',
                     'Resources/config',
                     'Resources/orm',
                     'Testing'
                 ) as $dir) {
            $directory = implode(DIRECTORY_SEPARATOR, array(
                $bundlePath,
                $dir
            ));

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        $bundleArray = explode(DIRECTORY_SEPARATOR, $bundle);

        $controller = sprintf(
            $this->getControllerTemplate(),
            str_replace(DIRECTORY_SEPARATOR, '\\', $bundle),
            strtolower($bundleArray[0]),
            '/' . strtolower(end($bundleArray)),
            strtolower(str_replace(DIRECTORY_SEPARATOR, '_', $bundle)) . '_index'
        );

        $controllerFile = $bundlePath . DIRECTORY_SEPARATOR . 'Controllers/Index.php';

        if (!file_exists($controllerFile)) {
            file_put_contents($controllerFile, $controller);
        }

        $bootstrap = sprintf(
            $this->getBootstrapTemplate(),
            str_replace(DIRECTORY_SEPARATOR, '\\', $bundle),
            $fullName
        );

        $bootstrapFile = $bundlePath . DIRECTORY_SEPARATOR . $fullName . '.php';

        if (!file_exists($bootstrapFile)) {
            file_put_contents($bootstrapFile, $bootstrap);
        }

        $routes = $bundlePath . DIRECTORY_SEPARATOR . 'Resources/config/routes.php';
        if (!file_exists($routes)) {
            file_put_contents($routes, '<?php ' . PHP_EOL);
        }

        $config = $bundlePath . DIRECTORY_SEPARATOR . 'Resources/config/config.php';
        if (!file_exists($config)) {
            file_put_contents($config, '<?php return [];' . PHP_EOL);
        }
    }

    public function getControllerTemplate()
    {
        return <<<CONTROLLER
<?php

namespace %s\Events;

use FastD\Framework\Bundle\Controllers\Controller;

/**
 * @Route("/%s")
 */
class Index extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return 'hello world';
    }
}
CONTROLLER;
    }

    public function getBootstrapTemplate()
    {
        return <<<BUNDLE
<?php

namespace %s;

use FastD\Framework\Bundle\Bundle;

class %s extends Bundle
{

}
BUNDLE;
    }
}