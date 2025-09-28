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

namespace Blomstra\FontAwesome\Content;

use Flarum\Frontend\Document;
use Flarum\Settings\SettingsRepositoryInterface;

class Frontend
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(Document $document): void
    {
        $type   = (string) $this->settings->get('blomstra-fontawesome.type', 'free');
        $kitUrl = trim((string) $this->settings->get('blomstra-fontawesome.kitUrl', ''));

        // 1) 注入 v7：支持 .css（link）与 .js（kit）；否则走本地 all.min.css 兜底
        if ($type === 'kit' && $kitUrl !== '') {
            if (preg_match('/\.css(\?.*)?$/i', $kitUrl)) {
                $document->head[] = '<link rel="stylesheet" href="' . htmlspecialchars($kitUrl, ENT_QUOTES, 'UTF-8') . '" />';
            } else {
                $document->head[] = '<script src="' . htmlspecialchars($kitUrl, ENT_QUOTES, 'UTF-8') . '" crossorigin="anonymous"></script>';
            }
        } else {
            $localHref = '/assets/extensions/blomstra-fontawesome/fontawesome-6-free/css/all.min.css?v=7';
            $document->head[] = '<link rel="stylesheet" href="' . htmlspecialchars($localHref, ENT_QUOTES, 'UTF-8') . '" />';
        }

        // 2) 过滤核心 FA5 的 font 预加载，避免主动请求 /assets/fonts/fa-*.woff2
        if (!empty($document->head)) {
            $document->head = array_values(array_filter(
                $document->head,
                static function ($tag) {
                    if (!is_string($tag)) return true;

                    // 过滤掉类似：/assets/fonts/fa-(regular-400|solid-900|brands-400).woff2 的 preload
                    if (stripos($tag, 'rel="preload"') !== false
                        && stripos($tag, 'as="font"') !== false
                        && preg_match('~\/assets\/fonts\/fa-(regular-400|solid-900|brands-400)\.woff2~i', $tag)) {
                        return false;
                    }
                    return true;
                }
            ));
        }

        // 3) 强制把 FA5 的 family 指到 v7 的 webfonts；并把常见类映射到 v7 家族
        //    这样即使核心/第三方仍声明 "Font Awesome 5 Free/Brands"，也会加载 v7 的字体文件
        $v7Base = '/assets/extensions/blomstra-fontawesome/fontawesome-6-free/webfonts';
        $css = <<<CSS
<style id="fa7-override">
/* Rebind FA5 family names to v7 sources (stop /assets/fonts/fa-*.woff2) */
@font-face {
  font-family: "Font Awesome 5 Free";
  font-style: normal;
  font-weight: 900;
  font-display: swap;
  src: url("{$v7Base}/fa-solid-900.woff2") format("woff2");
}
@font-face {
  font-family: "Font Awesome 5 Free";
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url("{$v7Base}/fa-regular-400.woff2") format("woff2");
}
@font-face {
  font-family: "Font Awesome 5 Brands";
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url("{$v7Base}/fa-brands-400.woff2") format("woff2");
}

/* Force common classes to v7 families (covers both new and old class names) */
.fa, .fa-solid, .fa-regular, .fa-light, .fa-thin, .fa-brands,
.fas, .far {
  font-family: "Font Awesome 7 Free" !important;
}
.fab {
  font-family: "Font Awesome 7 Brands" !important;
}
</style>
CSS;
        $document->head[] = $css;
    }
}
