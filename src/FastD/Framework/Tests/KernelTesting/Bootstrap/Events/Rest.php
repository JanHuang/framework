<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/23
 * Time: 下午2:50
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Tests\Bootstrap\Events;

use FastD\Framework\Events\RestEvent;
use FastD\Framework\Events\RestInterface;

class Rest extends RestEvent implements RestInterface
{
    public function getMediaType()
    {
        // TODO: Implement getMediaType() method.
    }

    public function getRequestId()
    {
        // TODO: Implement getRequestId() method.
    }

    public function getRateLimit()
    {
        // TODO: Implement getRateLimit() method.
    }

    public function getRateRemaining()
    {
        // TODO: Implement getRateRemaining() method.
    }

    public function getRateLimitReset()
    {
        // TODO: Implement getRateLimitReset() method.
    }

    public function getVersion()
    {
        // TODO: Implement getVersion() method.
    }

    public function getServer()
    {
        // TODO: Implement getServer() method.
    }
}