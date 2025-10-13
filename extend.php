<?php

/*
 * This file is part of blomstra/fontawesome.
 *
 *  Copyright (c) 2022 Blomstra Ltd.
 *
 *  For the full copyright and license information, please view the LICENSE.md
 *  file that was distributed with this source code.
 */

namespace Blomstra\FontAwesome;

use Blomstra\FontAwesome\Content\Frontend;
use Blomstra\FontAwesome\Providers\FontAwesomeLessImports;
use Blomstra\FontAwesome\Providers\FontAwesomePreloads;
use Flarum\Extend;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Factory;

return [
    // ====== Forum ======
    (new Extend\Frontend('forum'))
        // 强制注入 FA6 兼容入口与字体声明（关键）
        ->css(__DIR__.'/fontawesome-6-free/less/fa6.less')
        ->css(__DIR__.'/fontawesome-6-free/less/faces.less')

        // 原有样式
        ->css(__DIR__.'/less/forum.less')
        ->css(__DIR__.'/less/common.less')
        ->content(Frontend::class),

    // ====== Admin ======
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')

        // 同样把 FA6 入口与字体声明注入到后台（关键）
        ->css(__DIR__.'/fontawesome-6-free/less/fa6.less')
        ->css(__DIR__.'/fontawesome-6-free/less/faces.less')

        // 原有样式
        ->css(__DIR__.'/less/admin.less')
        ->css(__DIR__.'/less/common.less')
        ->content(Frontend::class),

    // 语言包
    new Extend\Locales(__DIR__.'/locale'),

    // 预加载与 Less import 路径提供者（保留）
    (new Extend\ServiceProvider())
        ->register(FontAwesomePreloads::class)
        ->register(FontAwesomeLessImports::class),

    // 默认设置
    (new Extend\Settings())
        ->default('blomstra-fontawesome.kitUrl', '')
        ->default('blomstra-fontawesome.type', 'free'),

    // 旧的 URL 生成函数（兼容历史写法，保留无妨）
    (new Extend\Theme())
        ->addCustomLessFunction('blomstra-fontawesome-font-urls', function ($style) {
            /** @var Cloud $disk */
            $disk = resolve(Factory::class)->disk('flarum-assets');
            $uri = $disk->url('extensions/blomstra-fontawesome/fontawesome-6-free/fa-' . $style);

            if ($style === 'solid') {
                $uri .= '-900';
            } else {
                $uri .= '-400';
            }

            return "url('$uri.woff2') format('woff2'), url('$uri.ttf') format('truetype')";
        }),
];
