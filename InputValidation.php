<?php
/**
 * Created by Anish Malik
 * User: MySite
 * Date: 9/10/12
 * Time: 11:34 AM
 * Validation of Input Items
 */
class InputValidation
{
    function __construct()
    {
        $this->id = 0;
    }

    function add_fields($postVar, $authType, $error) {
        $index = $this->id++;
        $this->check_vars[$index]['data'] = $postVar;
        $this->check_vars[$index]['authtype'] = $authType;
        $this->check_vars[$index]['error'] = $error;
    }

    function validate() {
        $errorMsg = "";

        for($i = 0; $i < $this->id; $i++) {
            $postVar  = $this->check_vars[$i]['data'];
            $authType = $this->check_vars[$i]['authtype'];
            $error    = $this->check_vars[$i]['error'];

            $pos = strpos($authType, '=');
            if($pos !== false) {
                $authType = substr($this->check_vars[$i]['authtype'], 0, $pos);
                $value    = substr($this->check_vars[$i]['authtype'], $pos+1);
            }

            switch($authType) {

                case "req": {
                    if(isset($postVar['name']) && is_array($postVar['name'])) {
                        $count = count($postVar['name']);

                        for($j=0; $j<$count; $j++) {
                            $length = strlen(trim($postVar['name'][$j]));
                            if(!$length)
                                $errorMsg .= $error." :File ".($j+1)."<br>";
                        }
                    }
                    elseif(isset($postVar['name']) && empty($postVar['name'])) {
                        $length = strlen(trim($postVar['name']));
                        if(!$length)
                            $errorMsg .= $error."<br>";
                    }
                    else{
                        $length = strlen(trim($postVar));
                        if(!$length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "alpha": {
                    $regExAlpha	= '/[a-zA-Z]+/';
                    $regexp = '/[^a-zA-Z\s\.]/';
                    if(preg_match($regExAlpha, trim($postVar)))
                    {
                        if (preg_match($regexp, trim($postVar)))
                        {
                            $length = strlen(trim($postVar));
                            if($length)
                                $errorMsg .= $error."<br>";
                        }
                    }
                    else
                        $errorMsg .= $error."<br>";

                    break;
                }

                case "alphanum": {
                    $regexp = '/[^A-za-z0-9]$/';
                    if (preg_match($regexp, trim($postVar))) {
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "num": {
                    $regexp = '/^0-9$/';
                    if (preg_match($regexp, trim($postVar))) {
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "max": {
                    $length = strlen(trim($postVar));
                    if ($length > $value)
                        $errorMsg .= $error."<br>";
                    break;
                }

                case "min": {
                    $length = strlen(trim($postVar));
                    if ($length < $value && $length != 0)
                        $errorMsg .= $error."<br>";
                    break;
                }

                case "lte": {
                    if(is_array($postVar)) {
                        $count = count($postVar);
                        if ($count > $value)
                            $errorMsg .= $error."<br>";
                    }
                    else {
                        if ($postVar > $value)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "gte":{
                    if(is_array($postVar)){
                        $count = count($postVar);
                        if ($count < $value)
                            $errorMsg .= $error."<br>";
                    }
                    else {
                        if ($postVar < $value){
                            $length = strlen(trim($postVar));
                            if($length)
                                $errorMsg .= $error."<br>";
                        }
                    }
                    break;
                }

                case "username": {
                    $regexp1 = '/^[0-9]$/';
                    $regexp2 = '/^[a-zA-Z]+[a-zA-Z\s]*[a-zA-Z]+$/';
                    if (preg_match($regexp1, trim($postVar)) && !preg_match($regexp2, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "name":{
                    $regexp = '/[^A-Za-z\s\.]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "addrname":{
                    $regexp = '/[^A-Za-z0-9\s]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "zone":{
                    $regexp = '/[^A-Za-z0-9\s]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "country":{
                    $regexp = '/[^0-9]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "city":{
                    $regexp = '/[^A-Za-z0-9\#\-\&\/\:\,\.\(\)\[\]\{\}\_\s]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "fullname":{
                    $regexp = '/[^A-Za-z\s]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "address":{
                    $regexp = '/[^A-Za-z0-9\#\-\&\/\:\,\.\(\)\[\]\{\}\_\s]/';
                    if (preg_match($regexp, trim($postVar)))
                    {
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "phone":
                {
                    if(isset($value)){
                        $found = strpos($value, ',');
                        if($found === false){
                            $options[0] = $value;
                        }
                        else{
                            $options = explode(",", $value);
                        }
                    }

                    $patternMatch = 0;
                    foreach($options as $opt){
                        $type = $this->availablePhoneType($opt);
                        foreach($type as $regexp) {
                            if(preg_match($regexp, $postVar)) {
                                $patternMatch = 1;
                            }
                        }
                        if($patternMatch)
                            break;
                    }

                    if(!$patternMatch) {
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "mobile": {
                    $regexp1 = '/[^0-9]$/';
                    if (preg_match($regexp1, trim($postVar)))
                    {
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "zip":{
                    $regexp = '/[^0-9]$/';
                    if (preg_match($regexp, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }
                case "currency":{
                    $regexp1 = '/^[0-9]+\.[0-9]+$/';
                    $regexp2 = '/^[0-9]+$/';
                    if (preg_match($regexp1, trim($postVar)) && preg_match($regexp2, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "email":{
                    $regexp = '/^[A-Za-z0-9]+((\.|\_|\-){1}[a-zA-Z0-9]+)*@([a-zA-Z0-9]+([\-]{1}[a-zA-Z0-9]+)*[\.]{1})+[a-zA-Z]{2,4}$/';
                    if (!preg_match($regexp, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "url":{
                    $regexp = '|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';
                    if (preg_match($regexp, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "ip":{
                    $regexp = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
                    if (preg_match($regexp, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }

                case "date":{
                    $errorMsg .= $this->validateDate(trim($postVar), $value, $error);
                    break;
                }
                case "custom":{
                    if (preg_match($value, trim($postVar))){
                        $length = strlen(trim($postVar));
                        if($length)
                            $errorMsg .= $error."<br>";
                    }
                    break;
                }
            }
        }

        return $errorMsg;
    }

    function validateDate($postVar, $value, $error)
    {
        $errorMsg = "";

        $length = strlen(trim($postVar));
        if($length){

            if(isset($value)){
                $found = strpos($value, ',');
                if($found === false){
                    $options[0] = $value;
                }
                else{
                    $options = explode(",", $value);
                }
            }
            else{
                $options[0] = 'dd-mm-yyyy';
            }

            $patternMatch = 0;
            foreach($options as $opt){
                $pos1 = strpos($opt, '-');
                $pos2 = strpos($opt, '/');
                $pos3 = strpos($opt, '.');

                if($pos1 !== false){
                    if($pos1==2){
                        if(strlen($opt) == 8)
                            $regexp = '/^[0-9]{2}[\-][0-9]{2}[\-][0-9]{2}$/';
                        else
                            $regexp = '/^[0-9]{2}[\-][0-9]{2}[\-][0-9]{4}$/';
                    }
                    if($pos1==4)
                        $regexp = '/^[0-9]{4}[\-][0-9]{2}[\-][0-9]{2}$/';
                }

                if($pos2 !== false){
                    if($pos2==2){
                        if(strlen($opt) == 8)
                            $regexp = '/^[0-9]{2}[\/][0-9]{2}[\/][0-9]{2}$/';
                        else
                            $regexp = '/^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$/';
                    }
                    if($pos2==4)
                        $regexp = '/^[0-9]{4}[\/][0-9]{2}[\/][0-9]{2}$/';
                }

                if($pos3 !== false){
                    if($pos3==2){
                        if(strlen($opt) == 8)
                            $regexp = '/^[0-9]{2}[\.][0-9]{2}[\.][0-9]{2}$/';
                        else
                            $regexp = '/^[0-9]{2}[\.][0-9]{2}[\.][0-9]{4}$/';
                    }
                    if($pos3==4)
                        $regexp = '/^[0-9]{4}[\.][0-9]{2}[\.][0-9]{2}$/';
                }

                if(preg_match($regexp, $postVar)){
                    $patternMatch = 1;
                    if((isset($pos1) && $pos1==2) || (isset($pos2) && $pos2==2) || (isset($pos3) && $pos3==2)){
                        $str1 = substr($opt, 0, 2);
                        $str2 = substr($opt, 3, 2);

                        if($str1 == 'dd'){
                            $DD = substr($postVar, 0, 2);
                            $MM = substr($postVar, 3, 2);
                            $YY = substr($postVar, 6);
                        }
                        if($str1 == 'mm'){
                            $MM = substr($postVar, 0, 2);
                            $DD = substr($postVar, 3, 2);
                            $YY = substr($postVar, 6);
                        }
                        if($str1 == 'yy'){
                            if($str2 == 'mm'){
                                $YY = substr($postVar, 0, 2);
                                $MM = substr($postVar, 3, 2);
                                $DD = substr($postVar, 6);
                            }
                            else{
                                $MM = substr($postVar, 0, 2);
                                $DD = substr($postVar, 3, 2);
                                $YY = substr($postVar, 6);
                            }
                        }
                    }

                    if((isset($pos1) && $pos1==4) || (isset($pos2) && $pos2==4) || (isset($pos3) && $pos3==4)){
                        $str = substr($opt, 5, 2);

                        if($str == 'dd'){
                            $YY = substr($postVar, 0, 4);
                            $DD = substr($postVar, 6, 2);
                            $MM = substr($postVar, 8, 2);
                        }
                        if($str == 'mm'){
                            $YY = substr($postVar, 0, 4);
                            $MM = substr($postVar, 6, 2);
                            $DD = substr($postVar, 6, 2);
                        }
                    }

                    if($DD == 0 || $MM == 0 || $YY==0){
                        $errorMsg .= "Invalid Date...<br>";
                    }

                    if($MM <= 12){
                        switch($MM) {
                            case 4:
                            case 6:
                            case 9:
                            case 11:
                                if ($DD > 30){
                                    $errorMsg .= "Selected month has maximum 30 days.<br>";
                                }
                            default:
                                if ($DD > 31){
                                    $errorMsg .= "Selected month has maximum 31 days.<br>";
                                }
                                break;
                        }
                    }

                    if (($YY % 4) == 0) {
                        if (($MM == 2) && ($DD > 29)) {
                            $errorMsg .= "Invalid days in February for leap year.<br>";
                        }
                    }
                    else {
                        if (($MM == 2) && ($DD > 28)) {
                            $errorMsg .= "Invalid days in February for non leap year.<br>";
                        }
                    }
                }

                if($patternMatch)           break;
            }

            if(!$patternMatch)	$errorMsg .= $error."<br>";

        }
        return $errorMsg;
    }
    function availablePhoneType($country)
    {
        $type[0]  = '/[^0-9]$/';
        return $type;
    }
    /**
     * Function to make Mysql queries safe
     *
     * @access public
     * @param scalar
     * @return scalar
     */
    function MakeStringSafe($argValue)
    {
        $retValue	= true;
        $argValue	= strip_tags($argValue);
        if(isset($argValue) && !is_numeric($argValue))
        {
            if(get_magic_quotes_gpc())
                $retValue = stripslashes($argValue);
            $retValue = mysql_real_escape_string($argValue);
        }
        else
            $retValue	= $argValue;
        return $retValue;
    }
    /**
     * Function to make Mysql queries safe with removal of single and double quotes also
     *
     * @access public
     * @param scalar
     * @return scalar
     */
    function MakeStringQuoteSafe($argValue)
    {
        $retValue	= true;

        $quotes = array('/"/',"/'/");
        $replacements = array('','');
        $argValue = preg_replace($quotes,$replacements,$argValue);
        $argValue	= strip_tags($argValue);
        if(isset($argValue) && !is_numeric($argValue))
        {
            if(get_magic_quotes_gpc())
                $retValue = stripslashes($argValue);
            $retValue = mysql_real_escape_string($argValue);
        }
        else
            $retValue	= $argValue;
        return $retValue;
    }
}

?>
