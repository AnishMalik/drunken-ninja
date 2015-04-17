<?php

/**
 * This class holds the common JS/CSS merging,saving and fetching saved file logic
 * @author Anish Malik
 * @version 1.0.0
 */

class AssetHandling
{
    /**
     * Aggregates all passed files content in single one
     * @Access public
     * @param array aFiles :Filenames
     * @return string all files data in merged state
     */
    function assetFileAggregator($sFiles)
    {
        if (!is_array($sFiles))
            return '';
        $sContent = '';
        foreach ($sFiles as $sFileName) {
            $sContent .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $sFileName) . "\n";
        }
        return $sContent;
    }

    /**
     * Fetch all file names with extension to be included on page
     * @Access public
     * @param string sLoc :name location where data is to be placed,for identification
     * @param string sFileType :Type of file-css/js
     * @param string sCdnUrl :cdn url defined in settings file
     * @param boolean bDebug :debug mode-true/false
     * @param string sFiles :all file names
     * @return string files data with type for direct inclusion on template
     */
    function assetReturnFileName($sLoc, $sFileType, $sCdnUrl, $bDebug, $sFiles)
    {
        if (isset($_SERVER['HTTPS']))
            $sCdnUrl = '/';
        elseif (empty($sCdnUrl))
            $sCdnUrl = config_item('cdn_url');

        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'memcached'));
        $sEnv_Var = config_item('Env_Var');
        $asset_version = $this->asset_version();
        $bDebug = get_configuration('STATIC', 'AssetHandlingDebug');
        if (is_array($bDebug)) {
            if (isset($bDebug[$sLoc]))
                $bDebug = $bDebug[$sLoc];
            elseif (isset($bDebug['myProject']))
                $bDebug = $bDebug['myProject'];
            else
                $bDebug = true;
        }

        if ($bDebug) {
            $sRet = '';
            foreach ($sFiles as $sFileName) {
                if ($sFileType == 'css')
                    $sRet = $sRet . '<link rel="stylesheet" type="text/css" href="' . $sCdnUrl . $sFileName . '?version=' . $asset_version . '" />' . "\n";
                else
                    $sRet = $sRet . '<script type="text/javascript" src="' . $sCdnUrl . $sFileName . '?version=' . $asset_version . '"></script>' . "\n";
            }
            return $sRet;
        } else {
            $sKey = $CI->cache->memcached->get($sEnv_Var . '_file_' . $sLoc . $asset_version . '_' . $sFileType);
            if (empty($sKey)) {
                $sResult = $this->assetFileAggregator($sFiles);
                if ($sFileType == 'css') {
                    $CI->load->library('CssMin');
                    $sContent = CssMin::minify($sResult);
                }
                else {
                    $CI->load->library('JSMin');
                    $sContent = JSMin::minify($sResult);
                }

                $sKey = sha1($sContent) . '.' . $sFileType;
                $CI->cache->memcached->save($sKey, $sContent, 0);
                $CI->cache->memcached->save($sEnv_Var . '_file_' . $sLoc . $asset_version . '_' . $sFileType, $sKey, 0);
            }
            if ($sFileType == 'css')
                return '<link rel="stylesheet" type="text/css" href="' . $sCdnUrl . 'assets/' . $sKey . '" />';
            else
                return '<script type="text/javascript" src="' . $sCdnUrl . 'assets/' . $sKey . '"></script>';
        }
    }

    /**
     * Get the combined,minified merged data from memcache
     * @Access public
     * @param string sLoc :name location where data is to be placed,for identification
     * @param string sFileType :Type of file-css/js
     * @return string all files data in merged state from memcache
     */
    function assetReturnFile($sLoc, $sFileType)
    {
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'memcached'));
        $sEnv_Var = config_item('Env_Var');
        $sContent = '';
        $sKey = $CI->cache->memcached->get($sEnv_Var . '_file_' . $sLoc . '_' . $sFileType);
        if (!empty($sKey)) {
            $sContent = $CI->cache->memcached->get($sKey);
        }
        return $sContent;
    }

    /**
     * Get the asset file saved in memcache
     * @Access public
     * @param string sKey :key name of saved file
     * @return string all files data in merged state from memcache
     */
    function assetReturnFileKey($sKey)
    {
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'memcached'));
        return $CI->cache->memcached->get($sKey);
    }

    function asset_version()
    {
        $configurator = Configurator::getInstance();
        return $configurator->getAttribute("versioning", "asset_version");
    }
}
