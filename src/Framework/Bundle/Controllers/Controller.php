<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 上午11:18
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Framework\Bundle\Controllers;

use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Kernel\AppKernel;
use FastD\Http\Session\Storage\SessionRedis;
use FastD\Http\RedirectResponse;
use FastD\Http\Response;
use FastD\Http\JsonResponse;
use FastD\Http\Session\Session;
use FastD\Framework\Bundle\Common\Common;
use FastD\Http\Session\Storage\SessionStorageInterface;

/**
 * Class Controller
 *
 * @package FastD\Framework\Bundle\Controllers
 */
class Controller implements ControllerInterface
{
    use Common;

    const SERVER_VERSION = AppKernel::VERSION;

    /**
     * @var string
     */
    public $currentAction;

    public $statusCode;

    public $msg;

    public $code;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $session_storage = [];

    /**
     * @param $connection
     * @return SessionRedis
     */
    public function getDefaultSessionStorage($connection)
    {
        if (!isset($this->session_storage[$connection])) {
            $this->session_storage[$connection] = new SessionRedis($this->getStorage($connection));
        }

        return $this->session_storage[$connection];
    }

    /**
     * @param SessionStorageInterface|null $sessionStorageInterface
     * @return Session
     */
    public function getSession(SessionStorageInterface $sessionStorageInterface = null)
    {
        if ($this->session instanceof Session) {
            return $this->session;
        }

        $this->session = $this->container->singleton('kernel.request')->getSessionHandle($sessionStorageInterface);

        return $this->session;
    }

    /**
     * Get Active Bundle.
     *
     * @return Bundle
     */
    public function getActiveBundle()
    {
        return $this->getContainer()->singleton('kernel')->getActiveBundle();
    }

    /**
     * @param       $name
     * @param array $parameters
     * @param string$format
     * @return string
     */
    public function generateUrl($name, array $parameters = array(), $format = '')
    {
        return $this->get('kernel.dispatch')->dispatch('handle.url', [$name, $parameters, $format]);
    }

    /**
     * @param               $name
     * @param   string|int  $version
     * @return string
     */
    public function asset($name, $version = null)
    {
        return $this->get('kernel.dispatch')->dispatch('handle.asset', [$name, $version]);
    }

    /**
     * @param       $name
     * @param array $parameters
     * @return  Response
     */
    public function forward($name, array $parameters = [])
    {
        return $this->get('kernel.dispatch')->dispatch('handle.forward', [$name, $parameters]);
    }

    /**
     * Render template to html or return content.
     *
     * @param            $view
     * @param array      $parameters
     * @param bool|false $flag
     * @return Response|string
     */
    public function render($view, array $parameters = array(), $flag = false)
    {
        $content = $this->get('kernel.dispatch')->dispatch('handle.tpl')->render($view, $parameters);
        
        return $flag ? $content : $this->responseHtml($content);
    }

    /**
     * @param       $data
     * @param int   $status
     * @param array $headers
     * @return Response
     */
    public function response($data, $status = Response::HTTP_OK, array $headers = [])
    {
        switch ($this->get('kernel.request')->getFormat()) {
            case 'json':
                return $this->responseJson($data, $status, $headers);
            case 'php':
            case 'jsp':
            case 'asp':
            case 'text':
            case 'html':
            default:
                return $this->responseHtml($data, $status, $headers);
        }
    }

    /**
     * Redirect url.
     *
     * @param       $url
     * @param array $parameters
     * @param int   $statusCode
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, array $parameters = [], $statusCode = 302, array $headers = [])
    {
        return new RedirectResponse($url, $statusCode, $headers);
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

    /**
     * @param $data
     * @return mixed
     */
    public function dump($data)
    {
        return $this->get('kernel.debug')->dump($data);
    }
}