<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Anish Malik
 * Date: 21/02/13
 * Time: 10:27 PM
 * To change this template use File | Settings | File Templates.
 */

class DbClientService {
    private static $instance;
    private static $CI;
    private static $clients = array();

    public static function getInstance() {
        if ( !self::$instance )
            self::$instance = new self();

        return self::$instance;
    }

    public function __construct() {
        if ( self::$instance )
            throw new Exception('Singleton');

        $this->initialize();
    }

    private function initialize() {
        self::$CI = & get_instance();
    }

    public function getDbClient($identifier) {
        if ( !isset(self::$clients[$identifier]) )
            self::$clients[$identifier] = self::$CI->load->database($identifier, TRUE);

        return self::$clients[$identifier];
    }

    public function executeQuery( $client, $query, $options = null ){

        $start = microtime(true);
        if($options){
            $result = $client->query($query, $options);
        }else{
            $result = $client->query($query);
        }
        $logData = array(
            'execTime' => (microtime(true) - $start)*1000,
            'io' => 'db',
            'input' => $query,
            'time' => microtime(true)
        );
        LoggingService::getInstance()->logData('dimension', $logData);
        return $result;
    }

    public function closeAll() {
        while (count(self::$clients) > 0) {
            $client = array_pop(self::$clients);
            @$client->close();
            $client = null;
            unset($client);
        }
    }
}
