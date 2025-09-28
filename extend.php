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
        $type = (string) $this->settings->get('blomstra-fontawesome.type', 'free');
        $kitUrl = trim((string) $this->settings->get('blomstra-fontawesome.kitUrl', ''));

        // 1) 注入 v7：支持 .css（link）和 .js（kit）
        if ($type === 'kit' && $kitUrl !== '') {
            if (preg_match('/\.css(\?.*)?$/i', $kitUrl)) {
                $document->head[] = '<link rel="stylesheet" href="'.htmlspecialchars($kitUrl, ENT_QUOTES, 'UTF-8').'" />';
            } else {
                $document->head[] = '<script src="'.htmlspecialchars($kitUrl, ENT_QUOTES, 'UTF-8').'" crossorigin="anonymous"></script>';
            }
        } else {
            // 本地兜底到 v7 all.min.css（保持你当前的最小改动目录）
            $localHref = '/assets/extensions/blomstra-fontawesome/fontawesome-6-free/css/all.min.css';
            $document->head[] = '<link rel="stylesheet" href="'.htmlspecialchars($localHref, ENT_QUOTES, 'UTF-8').'" />';
        }

        // 2) 过滤掉核心 FA5 的字体预加载（避免同时加载 /assets/fonts/fa-*.woff2）
        //    只移除针对 Font Awesome 的 font preload，不影响其它字体。
        if (!empty($document->head)) {
            $document->head = array_values(array_filter(
                $document->head,
                static function ($tag) {
                    if (!is_string($tag)) return true;

                    // 移除类似：<link rel="preload" as="font" href="/assets/fonts/fa-regular-400.woff2" ...>
                    if (stripos($tag, 'rel="preload"') !== false
                        && stripos($tag, 'as="font"') !== false
                        && preg_match('~\/assets\/fonts\/fa-(?:regular-400|solid-900|brands-400)\.woff2~i', $tag)) {
                        return false;
                    }

                    return true;
                }
            ));
        }
    }
}
