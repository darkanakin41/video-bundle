<?php


namespace PLejeune\VideoBundle\Helper;

use PLejeune\VideoBundle\Nomenclature\ProviderNomenclature;

class ChannelHelper
{
    /**
     * Retrieve the identifier based on the given url
     *
     * @param string $url
     *
     * @return array
     */
    public static function getIdentifier($url){
        if(strpos ($url, "youtube" )){
            $url = str_replace(array("/featured","/videos","/playlists","/channels","/discussion","/about"), "", $url);
            $url_pieces = explode('/', $url);
            if(count($url_pieces) == 5 && $url_pieces[3] == "user"){
                return ["identifier" => null , "name" => isset($url_pieces[4]) ? $url_pieces[4] : $url_pieces[3]];
            }
            if(count($url_pieces) == 5 && $url_pieces[3] == "channel"){
                return ["identifier" => $url_pieces[4], "name" => null];
            }
            return ['nom' => array_slice($url_pieces,-1)[0], 'identifier' => null];
        }
        return [];
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
        }
//        if(strpos($url, "azubu" )){
//            return ProviderNomenclature::AZUBU;
//        }
//        if(strpos($url, "beam" )){
//            return ProviderNomenclature::BEAM;
//        }
        return "OTHER";
    }
}
