<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Helper;

use Darkanakin41\VideoBundle\Nomenclature\PlatformNomenclature;

class ChannelHelper
{
    /**
     * Retrieve the identifier based on the given url.
     *
     * @param string $url
     *
     * @return array
     */
    public static function getIdentifier($url)
    {
        if (strpos($url, 'youtube')) {
            $url = str_replace(array('/featured', '/videos', '/playlists', '/channels', '/discussion', '/about'), '', $url);
            $url_pieces = explode('/', $url);
            if (5 == count($url_pieces) && 'user' == $url_pieces[3]) {
                return array('identifier' => null, 'name' => isset($url_pieces[4]) ? $url_pieces[4] : $url_pieces[3]);
            }
            if (5 == count($url_pieces) && 'channel' == $url_pieces[3]) {
                return array('identifier' => $url_pieces[4], 'name' => null);
            }

            return array('nom' => array_slice($url_pieces, -1)[0], 'identifier' => null);
        }

        return array();
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
