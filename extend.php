<?php

/*
 * This file is part of blomstra/fontawesome.
 *
 *  Copyright (c) 2022 Blomstra Ltd.
 *
 *  For the full copyright and license information, please view the LICENSE.md
 *  file that was distributed with this source code.
 *
 */

namespace Blomstra\FontAwesome;

use Blomstra\FontAwesome\Content\Frontend;
use Flarum\Extend;

return [
    // 仅注入前端资源（由 Frontend 决定是 kit .js、kit .css，还是本地 all.min.css）
    (new Extend\Frontend('forum'))
        ->content(Frontend::class),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->content(Frontend::class),

    // 语言包
    new Extend\Locales(__DIR__.'/locale'),

    // 后台设置默认值
    (new Extend\Settings())
        ->default('blomstra-fontawesome.kitUrl', '')
        ->default('blomstra-fontawesome.type', 'free'),

    // == 重要变更 ==
    // 1) 不再注册 v6 的 LESS 注入与自定义函数，避免再次生成 v6 的 @font-face：
    // (new Extend\ServiceProvider())->register(FontAwesomeLessImports::class),
    // (new Extend\Theme())->addCustomLessFunction('blomstra-fontawesome-font-urls', function (...) { ... }),

    // 2) 不再启用 v6 路径的预加载（若要预加载，请改到 webfonts 目录再单独实现）：
    // (new Extend\ServiceProvider())->register(FontAwesomePreloads::class),
];
