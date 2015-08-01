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

namespace FastD\Framework\Commands;

use FastD\Console\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;

class ConfCache extends Command
{
    public function getName()
    {
        return 'config:cache';
    }

    public function configure()
    {

    }

    public function execute(Input $input, Output $output)
    {
        $config = $this->getContainer()->get('kernel.config');
        $all = $config->all();
        $all = var_export($all, true);
        $all = str_replace(' ', '', $all);
        $cache = $this->getContainer()->get('kernel')->getRootPath() . '/config.php.cache';
        file_put_contents($cache, '<?php return ' . $all . ';');
        if (file_exists($cache)) {
            $output->writeBackground('build config cache successful', Output::STYLE_BG_SUCCESS);
            return 0;
        }
        $output->writeBackground('build config cache fail.', Output::STYLE_BG_FAILURE);
        return 1;
    }
}