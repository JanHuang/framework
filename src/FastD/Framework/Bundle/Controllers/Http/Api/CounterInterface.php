<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/23
 * Time: 下午2:22
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Api;

/**
 * Interface CounterInterface
 *
 * @package FastD\Framework\Api
 */
interface CounterInterface
{
    /**
     * @param $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $limit
     * @return $this
     */
    public function setLimited($limit);

    /**
     * @return int
     */
    public function getLimited();

    /**
     * @param $remaining
     * @return $this
     */
    public function setRemaining($remaining);

    /**
     * @return int
     */
    public function getRemaining();

    /**
     * @return $this
     */
    public function getResetTime();

    /**
     * @param $timestamp
     * @return int
     */
    public function setResetTime($timestamp);

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content);

    /**
     * @return array
     */
    public function getContent();

    /**
     * @return bool
     */
    public function validation();

    /**
     * @return bool
     */
    public function flush();
}