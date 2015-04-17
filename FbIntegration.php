<?php

/**
 * This class holds FB login and integration logic
 * @author Anish Malik
 * @version 1.0.0.0
 */

include_once(APPPATH . "third_party/facebook/facebook.php");

class FbIntegration
{
    private $facebook;
    private $access_token = NULL;

    public function __construct()
    {
        $this->facebook = new Facebook(
            array(
                'appId' => '*****',
                'secret' => '*********************',
                'cookie' => true,
            )
        );
    }

    public function fetchFBUser()
    {
        $this->updateAccessTokenFromRequest();

        return $this->facebook->getUser();
    }

    /**
     * Fetches fbuser profile
     * @param int iFetchedUser:fbuserid
     * @return mixed fbuserprofile:array fbuserprofile/string fail
     */
    function fetchFBUserProfile( $iFetchedUser )
    {
        if ( $iFetchedUser )
        {
            try
            {
                // Proceed knowing you have a logged in user who's authenticated.
                return $this->facebook->api('/me');
            }
            catch ( FacebookApiException $facebookApiException )
            {
                error_log( $facebookApiException );
            }
        }

        return 'fail';
    }

    /**
     * Fetches facebook login url
     * @param void:none
     * @return string sloginurl,facebook login url
     */
    function facebookLoginUrl($destUrl = null)
    {
        $CI = & get_instance();

        $conf = $CI->config->config;
        $redirect_uri = $conf['secure_base_url'];

        $login_url = $this->facebook->getLoginUrl(
            array('redirect_uri' => $redirect_uri . 'auth/facebook' . ( ($destUrl!==null && !empty($destUrl)) ? '?dest_url='.urlencode($destUrl) : '' ),
                'scope' => 'email,user_birthday,user_location,user_about_me,user_hometown',
            )
        );

        return $login_url;
    }

    private function fetchAccessTokenFromRequest()
    {
        if ( $this->access_token === NULL && isset($_GET['code']) )
        {
            $url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->facebook->getAppId() . '&client_secret=' . $this->facebook->getApiSecret() . '&code=' . $_GET['code'] . '&redirect_uri=' . urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/auth/facebook');

            $rpc = curl_init($url);

            curl_setopt($rpc, CURLOPT_HEADER, 0);
            curl_setopt($rpc, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($rpc, CURLOPT_TIMEOUT, 10);
            curl_setopt($rpc, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($rpc, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($rpc);

            curl_close($rpc);

            parse_str($response, $response_array);

            if ( isset($response_array['access_token']) )
            {
                return $response_array['access_token'];
            }
        }

        return NULL;
    }

    private function updateAccessTokenFromRequest()
    {
        if ( $this->access_token !== NULL ) return;

        $access_token = $this->fetchAccessTokenFromRequest();

        if ( $access_token )
        {
            $this->setAccessToken($access_token);
        }
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        $this->facebook->setAccessToken( $access_token );
    }
}
