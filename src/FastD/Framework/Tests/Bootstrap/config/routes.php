<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/12
 * Time: 下午3:18
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

Routes::get('/', 'FastD:Framework:Tests:Bootstrap:Events:Demo@indexAction');
Routes::get('/session', 'FastD:Framework:Tests:Bootstrap:Events:Demo@sessionAction');
Routes::get('/rest', 'FastD:Framework:Tests:Bootstrap:Events:Demo@apiAction');