<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/6/30
 * Time: 下午2:56
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Events\Http;

use FastD\Framework\Api\Counter;
use FastD\Http\Response;
use FastD\Http\JsonResponse;

/**
 * Class RestEvent
 *
 * @package FastD\Framework\Events
 */
class ResponseEvent extends Event
{
    const SERVER_NAME = 'FastD';
    const SERVER_VERSION = '2.0';

    public function response($data, $status = Response::HTTP_OK, array $headers = []) {}

    public function responseXml(array $data, $status = Response::HTTP_OK, array $headers = [])
    {}

    public function responseText($data, $status = Response::HTTP_OK, array $headers = [])
    {}

    public function responseImage($data, $status = Response::HTTP_OK, array $headers = [])
    {}

    public function responseHtml($data, $status = Response::HTTP_OK, array $headers = [])
    {}

    public function responseJson(array $data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
}