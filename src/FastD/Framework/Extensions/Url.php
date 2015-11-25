<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/25
 * Time: 上午10:50
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Extensions;

use FastD\Template\Extensions\Functions\TemplateFunction;

class Url extends TemplateFunction
{
    public function getExtensionName()
    {
        return 'url';
    }

    public function getExtensionContent()
    {
        return function ($name, array $parameters = [], $format = null) {};
    }
}