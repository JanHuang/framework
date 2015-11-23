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

namespace FastD\Framework\Bundle\Http\Events;

use FastD\Framework\Api\Counter;
use FastD\Http\Response;
use FastD\Http\JsonResponse;

/**
 * Class RestEvent
 *
 * @package FastD\Framework\Events
 */
abstract class RestEvent extends BaseEvent
{
    /**
     * @var Counter
     */
    protected $counter;

    /**
     * @return string
     */
    public function getVersion()
    {
        return 'v1';
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return 'FastD';
    }

    /**
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @return JsonResponse
     */
    public function responseJson(array $data, $status = Response::HTTP_OK, array $headers = [])
    {
        if ($this->counter instanceof Counter) {
            $headers['Access-Control-Allow-Credentials'] = 'true';
            $headers['Access-Control-Allow-Origin'] = '*';
            $headers['Access-Control-Expose-Headers'] = 'ETag, Link, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset';
            $headers['X-' . $this->getServer() . '-Media-Type'] = strtolower($this->getServer()) . '.' . $this->getVersion();
            $headers['X-' . $this->getServer() . '-Request-Id'] = $this->counter->getId();
            $headers['X-RateLimit-Limit'] = $this->counter->getLimited();
            $headers['X-RateLimit-Remaining'] = $this->counter->getRemaining();
            $headers['X-RateLimit-Excess'] = $this->counter->getExcess();
            $headers['X-RateLimit-Reset'] = $this->counter->getResetTime();
            $headers['X-Served-By'] = $this->getServer();
        }

        return new JsonResponse($data, $status, $headers);
    }
}