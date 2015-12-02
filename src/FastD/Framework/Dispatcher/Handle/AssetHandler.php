<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/26
 * Time: 上午11:45
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Framework\Dispatcher\Dispatch;

/**
 * Class AssetHandler
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class AssetHandler extends Dispatch
{
    protected $baseUrl;

    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.asset';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        list($name, $version) = $parameters;

        if (null === $this->baseUrl) {
            $request = $this->getContainer()->singleton('kernel.request');

            $this->baseUrl = $request->getDomain() . $request->getBaseUrl();

            if ('' != pathinfo($this->baseUrl, PATHINFO_EXTENSION)) {
                $this->baseUrl = pathinfo($this->baseUrl, PATHINFO_DIRNAME);
            }
        }

        return '//' . $this->baseUrl . '/bundle/' . $name . (null === $version ? '' : '?v=' . $version);
    }
}