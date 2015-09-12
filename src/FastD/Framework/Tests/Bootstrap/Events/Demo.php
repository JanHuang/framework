<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/12
 * Time: 下午3:17
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Tests\Bootstrap\Events;

use FastD\Framework\Events\BaseEvent;

class Demo extends BaseEvent
{
    public function indexAction()
    {
        return 'hello world';
    }
}