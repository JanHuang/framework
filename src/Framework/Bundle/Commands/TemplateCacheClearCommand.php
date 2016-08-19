<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 16-8-18 下午10:51
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Command\Command;
use FastD\Console\Input\Input;
use FastD\Console\Output\Output;

class TemplateCacheClearCommand extends CommandAware
{

    /**
     * @return string
     */
    public function getDescription()
    {
        // TODO: Implement getDescription() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'template:cache:clear';
    }

    /**
     * @return void
     */
    public function configure()
    {
        // TODO: Implement configure() method.
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        $path = $this->getContainer()->get('kernel')->getRootPath() . '/storage/templates';
        if(!file_exists($path)) {
            $output->writeln('done');
            return true;
        }

        $recursion = null;

        $recursion = function($path) use (&$recursion) {
            $fp = opendir($path);
            while($file = readdir($fp)) {
                if('.' === $file || '..' === $file) {
                    continue;
                }
                $file = "{$path}/{$file}";
                if(is_dir($file)) {
                    $recursion($file);
                    rmdir($file);
                }else {
                    unlink($file);
                }
            }
            closedir($fp);
        };

        $output->write('Clearing template cache ... ');

        try {
            $recursion($path);
        }catch (\Exception $e) {
            $output->writeln('<error>failed</error>');
            return 0;
        }

        $output->writeln('<success>success</success>');
        return 1;
    }
}