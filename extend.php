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
    // 前台：仅由 Frontend 决定注入（kit .js / kit .css / 本地 all.min.css）
    (new Extend\Frontend('forum'))
        ->content(Frontend::class),

    // 后台：保留 admin JS（如有），并同样注入 Frontend（图标在后台也可用）
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->content(Frontend::class),

    // 多语言
    new Extend\Locales(__DIR__.'/locale'),

    // 设置默认值
    (new Extend\Settings())
        ->default('blomstra-fontawesome.kitUrl', '')
        ->default('blomstra-fontawesome.type', 'free'),

    // ⚠️ 已移除 v6 的 LESS 注入、自定义函数和预加载，避免与 v7 冲突
    // (new Extend\ServiceProvider())->register(FontAwesomePreloads::class),
    // (new Extend\ServiceProvider())->register(FontAwesomeLessImports::class),
    // (new Extend\Theme())->addCustomLessFunction('blomstra-fontawesome-font-urls', ...)
];
