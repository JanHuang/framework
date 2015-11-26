<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/26
 * Time: 上午12:07
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Framework\Dispatcher\Dispatch;
use FastD\Framework\Extensions\Preset;

/**
 * Framework template generator.
 *
 * Class TplHandler
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class TplHandler extends Dispatch
{
    protected $tpl;

    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.tpl';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        if (null !== $this->tpl) {
            return $this->tpl;
        }

        $appKernel = $this->getContainer()->singleton('kernel');

        $extensions = [new Preset()];
        $paths = [
            $appKernel->getRootPath() . '/views',
            $appKernel->getRootPath() . '/../src'
        ];
        $bundles = $appKernel->getBundles();
        foreach ($bundles as $bundle) {
            $paths[] = dirname($bundle->getRootPath());
            $extensions = array_merge($extensions, $bundle->registerExtensions());
        }

        $options = [];
        if (!($isDebug = $appKernel->isDebug())) {
            $options = [
                'cache' => $appKernel->getRootPath() . '/storage/templates',
                'debug' => $isDebug,
            ];
        }

        $this->tpl = $this->container->singleton('kernel.template', [$paths, $options]);
        foreach ($extensions as $extension) {
            $extension->setContainer($this->getContainer());
            $this->tpl->addExtension($extension);
        }

        return $this->tpl;
    }
}