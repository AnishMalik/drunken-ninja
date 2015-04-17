<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Anish Malik
 * Date: 9/21/13
 * Time: 11:12 AM
 * To change this template use File | Settings | File Templates.
 */

class Video {

    public function __construct()
    {
        $CI = get_instance();
    }

    /**
     * Get Enbeded code of most common video streaming sites across
     * @Access public
     * @param string sUrl :Entire Url
     * @return iframe with merged url
     */
    public function getEmbedCode($sUrl='')
    {
        if ( filter_var($sUrl, FILTER_VALIDATE_URL) === FALSE )
            return '';

        if ( strpos($sUrl, 'youtube') !== FALSE )
        {
            parse_str( parse_url( $sUrl, PHP_URL_QUERY ), $result );
            $videoId = $result['v'];
            if ( !$videoId )
                return '';

            return '<iframe type="text/html" width="500" height="280" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
        }

        if ( strpos($sUrl, 'vimeo') !== FALSE )
        {
            $videoId = (int) substr(parse_url($sUrl, PHP_URL_PATH), 1);
            if ( !$videoId )
                return '';

            return '<iframe src="http://player.vimeo.com/video/' . $videoId . '" width="500" height="280" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }

        if ( strpos($sUrl, 'metacafe') !== FALSE )
        {
            $pattern = "#(?<=watch/).*?(?=/)#";
            preg_match($pattern, $sUrl, $matches, PREG_OFFSET_CAPTURE, 3);
            $videoId = $matches[0][0];
            if ( !$videoId )
                return '';

            return '<iframe src="http://www.metacafe.com/embed/' . $videoId . '/" width="500" height="280" allowFullScreen frameborder=0></iframe>';
        }

        if ( strpos($sUrl, 'dailymotion') !== FALSE )
        {
            $videoId = strtok(basename($sUrl), '_');
            if ( !$videoId )
                return '';

            return '<iframe frameborder="0" width="500" height="280" src="http://www.dailymotion.com/embed/video/' . $videoId . '"></iframe>';
        }

        return '';
    }
}
