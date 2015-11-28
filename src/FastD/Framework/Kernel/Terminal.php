<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/6/30
 * Time: 下午3:58
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Kernel;

use FastD\Console\Environment\BaseEnvironment;
use FastD\Framework\Bundle\Bundle;
use FastD\Console\Command;

/**
 * Class Terminal
 *
 * @package FastD\Framework\Kernel
 */
abstract class Terminal extends BaseEnvironment implements TerminalInterface, AppKernelInterface
{
    /**
     * Application process shutdown.
     *
     * @param AppKernel $appKernel
     * @return void
     */
    public function shutdown(AppKernel $appKernel)
    {
        $this->getContainer()->singleton('kernel.dispatch')->dispatch('handle.shutdown', [$appKernel->isDebug()]);
    }

    /**
     * Initialize preset command.
     *
     * @return void
     */
    public function register()
    {
        $bundles = array_merge($this->getBundles(), [new Bundle()]);
        foreach ($bundles as $bundle) {
            $dir = $bundle->getRootPath() . '/Commands';
            if (!is_dir($dir)) {
                continue;
            }
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (in_array($file, ['.', '..']) || is_dir($dir. DIRECTORY_SEPARATOR . $file)) {
                        continue;
                    }
                    $fileName = $bundle->getNamespace() . '\\Commands\\' . pathinfo($file, PATHINFO_FILENAME);
                    $command = new $fileName();
                    if ($command instanceof Command) {
                        $command->setEnv($this);
                        $command->setContainer($this->getContainer());
                        $this->setCommand($command);
                    }
                }
                closedir($dh);
            }
        }
    }
}