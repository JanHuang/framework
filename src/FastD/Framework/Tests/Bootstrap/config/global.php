<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/12
 * Time: 下午3:03
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

return [
    // 模板引擎
    'template' => [
        'paths' => [
            __DIR__ . '/../views',
            __DIR__ . '/../../src',
        ],
        'cache' => __DIR__ . '/../storage/templates',
    ],

    // 日志对象
    'logger' => [
        'access' => '%root.path%/storage/logs/%date%/access.log',
        'error' => '%root.path%/storage/logs/%date%/error.log',
    ],
];