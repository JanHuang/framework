<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/23
 * Time: 下午6:57
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Controllers;

use FastD\Http\RedirectResponse;
use FastD\Http\Response;
use FastD\Framework\Bundle\Common\CommonInterface;

/**
 * Interface EventInterface
 *
 * @package FastD\Framework\Bundle\Events
 */
interface ControllerInterface extends CommonInterface
{
    /**
     * @param       $name
     * @param array $parameters
     * @param null  $format
     * @return string
     */
    public function generateUrl($name, array $parameters = [], $format = null);

    /**
     * @param       $view
     * @param array $parameters
     * @return string
     */
    public function render($view, array $parameters = []);

    /**
     * @param       $name
     * @param array $parameters
     * @param int   $statusCode
     * @param array $header
     * @return RedirectResponse
     */
    public function redirect($name, array $parameters = [], $statusCode = 302, array $header = []);

    /**
     * @param       $name
     * @param array $parameters
     * @return string|Response
     */
    public function forward($name, array $parameters = []);

    /**
     * @param      $name
     * @param null $version
     * @return string
     */
    public function asset($name, $version = null);
}