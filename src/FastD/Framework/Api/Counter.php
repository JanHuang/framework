<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/23
 * Time: 下午3:23
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Api;

use FastD\Storage\StorageInterface;

/**
 * Class Counter
 *
 * @package FastD\Framework\Api
 */
class Counter implements CounterInterface, CounterSerializeInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var array|string
     */
    protected $content;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $limited;

    /**
     * @var int
     */
    protected $remaining;

    /**
     * @var int
     */
    protected $reset;

    /**
     * @var int
     */
    protected $timeout;

    protected $excess = 0;

    /**
     * @param StorageInterface $storageInterface
     * @param null             $id
     * @param int              $limited
     * @param int              $timeout
     */
    public function __construct(StorageInterface $storageInterface, $id = null, $limited = 10, $timeout = 1)
    {
        $this->storage = $storageInterface;

        $this->setId($id);

        $this->setLimited($limited);

        $this->setRemaining($limited);

        $this->timeout = $timeout;

        $this->setResetTime(time() + 3600);

        if (null === $this->getContent()) {
            $this->content = $storageInterface->get($this->getId());
            $this->decode();
        }
    }

    /**
     * @return int
     */
    public function getExcess()
    {
        return $this->excess;
    }

    /**
     * @param int $excess
     * @return $this
     */
    public function setExcess($excess)
    {
        $this->excess = $excess;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function setLimited($limit)
    {
        $this->limited = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimited()
    {
        return $this->limited;
    }

    /**
     * @param $remaining
     * @return $this
     */
    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;

        return $this;
    }

    /**
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * @return int
     */
    public function getResetTime()
    {
        return $this->reset;
    }

    /**
     * @param $timestamp
     * @return $this
     */
    public function setResetTime($timestamp)
    {
        $this->reset = $timestamp;

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param bool $isAuthorization
     * @return bool
     */
    public function validation($isAuthorization = false)
    {
        if (!$this->storage->exists($this->getId())) {
            $this->setRemaining(--$this->remaining);
            $this->flush();
            return true;
        }

        if (false == $this->content) {
            return false;
        }

        if (time() > $this->content['reset'] || ($this->limited != $this->content['limit'])) {
            $this->setRemaining(--$this->remaining);
            $this->setResetTime(time() + 3600);
            $this->setExcess(0);
        } else {
            if ($this->content['remaining'] <= 0) {
                if (!$isAuthorization) {
                    return false;
                }
                $this->setExcess(++$this->content['excess']);
            }
            $remaing = --$this->content['remaining'];
            $this->setRemaining($remaing <= 0 ? 0 : $remaing);
        }

        $this->flush();
        return true;
    }

    /**
     * @return bool
     */
    public function flush()
    {
        $this->content = [
            'limit' => $this->getLimited(),
            'reset' => $this->getResetTime(),
            'remaining' => $this->getRemaining(),
            'excess' => $this->getExcess(),
        ];

        $this->storage->set($this->getId(), $this->encode());

        return true;
    }

    /**
     * @return array|string
     */
    public function encode()
    {
        return json_encode($this->content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return bool
     */
    public function decode()
    {
        return !is_array($this->content = json_decode($this->content, true)) ? false : $this->content;
    }
}