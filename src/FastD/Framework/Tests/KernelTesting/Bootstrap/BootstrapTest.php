<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/18
 * Time: 上午1:06
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Tests\KernelTesting;

use FastD\Framework\Tests\KernelTesting\Bootstrap\App;
use FastD\Http\Request;
use FastD\Framework\Tests\FrameworkTestCase;

class BootstrapTest extends FrameworkTestCase
{
    protected function getServer()
    {
        return array (
            'UNIQUE_ID' => 'VfPNV8CoZGgAAAPkFEcAAAAD',
            'HTTP_HOST' => 'localhost',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36',
            'HTTP_REFERER' => 'http://localhost/demo/http_demo/',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
            'HTTP_ACCEPT_LANGUAGE' => 'zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4',
            'PATH' => '/usr/bin:/bin:/usr/sbin:/sbin',
            'DYLD_LIBRARY_PATH' => '/Applications/XAMPP/xamppfiles/lib:/Applications/XAMPP/xamppfiles/lib',
            'SERVER_SIGNATURE' => '',
            'SERVER_SOFTWARE' => 'Apache/2.4.7 (Unix) PHP/5.5.9 OpenSSL/1.0.1f mod_perl/2.0.8-dev Perl/v5.16.3',
            'SERVER_NAME' => 'localhost',
            'SERVER_ADDR' => '::1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '::1',
            'DOCUMENT_ROOT' => '/Users/janhuang/Documents/htdocs',
            'REQUEST_SCHEME' => 'http',
            'CONTEXT_PREFIX' => '',
            'CONTEXT_DOCUMENT_ROOT' => '/Users/janhuang/Documents/htdocs',
            'SERVER_ADMIN' => 'you@example.com',
            'SCRIPT_FILENAME' => '/Users/janhuang/Documents/htdocs/demo/http_demo/demo.php',
            'REMOTE_PORT' => '53022',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'REQUEST_URI' => '/demo/http_demo/demo.php',
            'SCRIPT_NAME' => '/demo/http_demo/demo.php',
            'PHP_SELF' => '/demo/http_demo/demo.php',
            'REQUEST_TIME_FLOAT' => 1442041175.5610001087188720703125,
            'REQUEST_TIME' => 1442041175,
        );
    }

    public function testRestAPI()
    {
        $app = self::kernelBootstrap();

        print_r($app);
    }

    public static function kernelBootstrap($env = 'dev')
    {
        $app = new App($env);
        $app->boot();
        return $app;
    }
}