<?php

/**
 * Created by PhpStorm.
 * User: Kesav
 * Date: 2/23/2016
 * Time: 6:14 PM
 */
class api extends MysqliDb
{
    protected $http_accept;
    protected $http_method;
    private $http_headers;
    public $base_url;
    public $soap_client;
    private $accepts_headers = ['application/xml','application/xhtml+xml'];
    protected $viewFile;
    private $server_url = "http://115.124.125.42/Tolls/api/2019/ps/";



    public function __construct()
    {
        parent::__construct();
        $this->http_headers = getallheaders();
        $this->http_accept = $this->http_headers['Accept'];
        $this->http_method = $_SERVER['REQUEST_METHOD'];

    }

    protected function output_other($data, $code)
    {
        $this->setHeader($code);
        if (in_array($this->http_accept,$this->accepts_headers)) {
            $xml = new SimpleXMLElement('<DATA/>');
            array_walk_recursive($data, array ($xml, 'addChild'));
            print $xml->asXML();
            exit;
        } else {
            echo json_encode($data);
            exit;
        }
    }

    private function array_reverse_recursive($arr) {
        foreach ($arr as $key => $val) {
            if (is_array($val))
                'asdsa';
            else
                $arr[$val] = $key;
        }
        return $arr;
    }

    public function sucess_data($data, $code = 200)
    {
        $this->setHeader($code);
        if (in_array($this->http_accept,$this->accepts_headers)) {
           /* require_once('lib/nusoap.php');
            $client = new nusoap_client('http://localhost/POS_SEERVER/soap_server.php?wsdl');
            //$data = $this->array2xml($data);

            $daas = $client->call('getData',array('data'=> $data));
            //print_r($client);
            print_r($daas);
            //echo $client;
            //echo $daas;*/
            print $this->array2xml($data);
            exit;
        } else {
            echo json_encode($data);
            exit;
        }
    }




    private function array2xml($array, $node_name="DATA") {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $root = $dom->createElement($node_name);
        $dom->appendChild($root);

        $array2xml = function ($node, $array) use ($dom, &$array2xml) {
            foreach($array as $key => $value){
                if ( is_array($value) ) {
                    if(is_numeric($key)){
                        $n = $dom->createElement('item');
                    }else{
                        $n = $dom->createElement($key);
                    }

                    $node->appendChild($n);
                    $array2xml($n, $value);
                }else{
                    $attr = $dom->createAttribute($key);
                    $attr->value = $value;
                    $node->appendChild($attr);
                }
            }
        };

        $array2xml($root, $array);

        return $dom->saveXML();
    }

    protected function setHeader($code = NULL)
    {

        if ($code !== NULL) {

            switch ($code) {
                case 100:
                    $text = 'Continue';
                    break;
                case 101:
                    $text = 'Switching Protocols';
                    break;
                case 200:
                    $text = 'OK';
                    break;
                case 201:
                    $text = 'Created';
                    break;
                case 202:
                    $text = 'Accepted';
                    break;
                case 203:
                    $text = 'Non-Authoritative Information';
                    break;
                case 204:
                    $text = 'No Content';
                    break;
                case 205:
                    $text = 'Reset Content';
                    break;
                case 206:
                    $text = 'Partial Content';
                    break;
                case 300:
                    $text = 'Multiple Choices';
                    break;
                case 301:
                    $text = 'Moved Permanently';
                    break;
                case 302:
                    $text = 'Moved Temporarily';
                    break;
                case 303:
                    $text = 'See Other';
                    break;
                case 304:
                    $text = 'Not Modified';
                    break;
                case 305:
                    $text = 'Use Proxy';
                    break;
                case 400:
                    $text = 'Bad Request';
                    break;
                case 401:
                    $text = 'Unauthorized';
                    break;
                case 402:
                    $text = 'Payment Required';
                    break;
                case 403:
                    $text = 'Forbidden';
                    break;
                case 404:
                    $text = 'Not Found';
                    break;
                case 405:
                    $text = 'Method Not Allowed';
                    break;
                case 406:
                    $text = 'Not Acceptable';
                    break;
                case 407:
                    $text = 'Proxy Authentication Required';
                    break;
                case 408:
                    $text = 'Request Time-out';
                    break;
                case 409:
                    $text = 'Conflict';
                    break;
                case 410:
                    $text = 'Gone';
                    break;
                case 411:
                    $text = 'Length Required';
                    break;
                case 412:
                    $text = 'Precondition Failed';
                    break;
                case 413:
                    $text = 'Request Entity Too Large';
                    break;
                case 414:
                    $text = 'Request-URI Too Large';
                    break;
                case 415:
                    $text = 'Unsupported Media Type';
                    break;
                case 500:
                    $text = 'Internal Server Error';
                    break;
                case 501:
                    $text = 'Not Implemented';
                    break;
                case 502:
                    $text = 'Bad Gateway';
                    break;
                case 503:
                    $text = 'Service Unavailable';
                    break;
                case 504:
                    $text = 'Gateway Time-out';
                    break;
                case 505:
                    $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }
    }

    public function getRenderedHTML($path)
    {
        ob_start();
        include($path);
        $var=ob_get_contents();
        ob_end_clean();
        return $var;
    }

    protected function setViewFile($method){
        return $this->viewFile = 'views/'.$method.'.php';
    }

    protected function curl_hit($url,$data){
        $url = $this->server_url.$url;
        $fields_string="";
        foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

//open connection
        $ch = curl_init();

//set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute post
        $result = curl_exec($ch);

//close connection
        curl_close($ch);
        return $result;
    }

}