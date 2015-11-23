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

use FastD\Http\Response;
use FastD\Http\JsonResponse;
use FastD\Http\XmlResponse;

/**
 * Class ResponsiveEvent
 *
 * @package FastD\Framework\Bundle\Events\Http
 */
class ResponsiveEvent extends Event
{
    const SERVER_NAME = 'FastD';
    const SERVER_VERSION = '2.0';

    /**
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @return JsonResponse|Response|XmlResponse
     */
    public function response($data, $status = Response::HTTP_OK, array $headers = [])
    {
        switch ($this->get('kernel.request')->getFormat()) {
            case 'json':
                return $this->responseJson($data, $status, $headers);
            case 'xml':
                return $this->responseXml($data, $status, $headers);
            case 'text':
                return $this->responseText($data, $status, $headers);
            case 'html':
            default:
                return $this->responseHtml($data, $status, $headers);
        }
    }

    /**
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return XmlResponse
     */
    public function responseXml(array $data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new XmlResponse($data, $status, $headers);
    }

    /**
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function responseText($data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new Response($data, $status, $headers);
    }

    /**
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function responseHtml($data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new Response($data, $status, $headers);
    }

    /**
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return JsonResponse
     */
    public function responseJson(array $data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
}