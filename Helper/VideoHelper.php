<?php


namespace PLejeune\VideoBundle\Helper;

use PLejeune\VideoBundle\Nomenclature\ProviderNomenclature;

class VideoHelper
{
    /**
     * Retrieve the identifier based on the given url
     *
     * @param string $url
     *
     * @return array
     */
    public static function getIdentifier($url){
        if(strpos ($url, "dailymotion" )){
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('_', $step1);
            return $step2_explode[0];
        }
        if(strpos ($url, "twitch" )){
            $url = str_replace("/profile", "", $url);
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?', $step1);
            return $step2_explode[0];
        }
        if(strpos ($url, "hitbox" )){
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?', $step1);
            return $step2_explode[0];
        }
        if(strpos ($url, "youtube" )){
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?v=', $step1);
            $step3_explode = explode('&', $step2_explode[1]);
            return $step3_explode[0];
        }
        return $url;
    }

    /**
     * Extract the platform based on the url
     *
     * @param string $url
     *
     * @return string
     */
    public static function getPlatform($url){
//        if(strpos($url, "dailymotion" )){
//            return ProviderNomenclature::DAILYMOTION;
//        }
//        if(strpos($url, "twitch" )){
//            return ProviderNomenclature::TWITCH;
//        }
//        if(strpos($url, "hitbox" )){
//            return ProviderNomenclature::HITBOX;
//        }
        if(strpos($url, "youtube" ) !== false){
            return ProviderNomenclature::YOUTUBE;
//        }
//        if(strpos($url, "azubu" )){
//            return ProviderNomenclature::AZUBU;
//        }
//        if(strpos($url, "beam" )){
//            return ProviderNomenclature::BEAM;
        }
        return "OTHER";
    }
}
