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
        $kitUrlRaw = (string) $this->settings->get('blomstra-fontawesome.kitUrl', '');
        $kitUrl = trim($kitUrlRaw);

        if ($type === 'kit' && $kitUrl !== '') {
            // 如果是 .css，使用 <link> 注入；否则（通常是 .js kit）使用 <script>
            if (preg_match('/\.css(\?.*)?$/i', $kitUrl)) {
                $href = htmlspecialchars($kitUrl, ENT_QUOTES, 'UTF-8');
                $document->head[] = '<link rel="stylesheet" href="'.$href.'" />';
            } else {
                $src = htmlspecialchars($kitUrl, ENT_QUOTES, 'UTF-8');
                $document->head[] = '<script src="'.$src.'" crossorigin="anonymous"></script>';
            }
            return;
        }

        // 兜底：加载本地 FA7 CSS（放在 fontawesome-6-free/css/ 下以最小改动兼容）
        $localHref = '/assets/extensions/blomstra-fontawesome/fontawesome-6-free/css/all.min.css';
        $document->head[] = '<link rel="stylesheet" href="'.htmlspecialchars($localHref, ENT_QUOTES, 'UTF-8').'" />';
    }
}
