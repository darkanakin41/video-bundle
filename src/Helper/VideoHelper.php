<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Helper;

use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;

class VideoHelper
{
    /**
     * Retrieve the identifier based on the given url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getIdentifier($url)
    {
        if (strpos($url, 'youtube')) {
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?v=', $step1);
            $step3_explode = explode('&', $step2_explode[1]);

            return $step3_explode[0];
        }

        return $url;
    }

    /**
     * Extract the platform based on the url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getPlatform($url)
    {
        if (false !== strpos($url, 'youtube')) {
            return PlatformNomenclature::YOUTUBE;
        }

        return PlatformNomenclature::OTHER;
    }
}
