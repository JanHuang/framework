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

use FastD\Debug\Exceptions\ForbiddenHttpException;
use FastD\Framework\Api\Counter;
use FastD\Framework\Events\RestEvent;
use FastD\Http\Response;

class Demo extends RestEvent
{
    public function __initialize()
    {
        $id = md5($this->getRequest()->server->get('HTTP_USER_AGENT'));
        $this->counter = new Counter($this->getStorage('counter'), $id, 10, 0.01);
        if (!$this->counter->validation()) {
            return $this->responseJson(['Access over.'], Response::HTTP_FORBIDDEN);
        }
    }

    public function indexAction()
    {
        return 'hello world';
    }

    public function sessionAction()
    {
        return $this->responseJson(['name' => 'janhuang']);
    }

    public function apiAction()
    {
        return 'hello world';
    }
}