<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/12/24
 * Time: 下午7:01
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Input\Input;
use FastD\Console\Output\Output;
use FastD\Database\Schema\SchemaParser;

/**
 * Class OrmRevertCommand
 *
 * @package FastD\Framework\Bundle\Commands
 */
class FdbReflexCommand extends CommandAware
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'fdb:reflex';
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this
            ->setArgument('connection')
            ->setOption('bundle')
        ;
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        $connection = $input->getArgument('connection');
        if (empty($connection)) {
            $connection = 'read';
        }

        $bundle = $input->getOption('bundle');

        $driver = $this->getDriver($connection);

        $parser = new SchemaParser($driver);

        if (null !== $bundle) {
            $bundle = $this->getContainer()->singleton('kernel')->getBundle($bundle);
            $path = $bundle->getRootPath() . '/ORM/' . ucfirst($driver->getDbName());
            $namespace = $bundle->getNamespace() . '\\ORM\\' . ucfirst($driver->getDbName());
            $parser->getSchemaReflex()->reflex($path, $namespace);
            return 0;
        }

        $bundles = $this->getContainer()->singleton('kernel')->getBundles();

        foreach ($bundles as $bundle) {
            $path = $bundle->getRootPath() . '/ORM/' . ucfirst($driver->getDbName());
            $namespace = $bundle->getNamespace() . '\\ORM\\' . ucfirst($driver->getDbName());
            $parser->getSchemaReflex()->reflex($path, $namespace);
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '反射数据库结构到实体对象';
    }
}