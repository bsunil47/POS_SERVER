<?php
/**
 * Created by PhpStorm.
 * User: Kesav
 * Date: 2/23/2016
 * Time: 6:13 PM
 */
if(!function_exists('base_url')) {
    function base_url($atRoot = FALSE, $atCore = FALSE, $parse = FALSE)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];
            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }
        return $base_url;
    }
}
if(file_exists('config.php')){
    require_once('config.php');
    require_once('MysqliDb.php');

    require_once('api.php');
    require_once('callfunction.php');
//require_once('lib/nusoap.php');


    $query_str = trim($_SERVER['QUERY_STRING'],'/');
    $method_array = explode('/',$query_str);
    $method = empty($method_array[0])? 'index' : $method_array[0];

    if(method_exists('callfunction',$method)){
        $call = new callfunction($params['db_array'],$params['Toll_details'],$method);
        $call->base_url = base_url();
        $call->$method();
    }else{
        $call = new callfunction($params['db_array'],$params['Toll_details'],$method);
        $call->method_not();
    }
}else{
    require_once('Toll.php');
    $file = new Toll();
    $file->base_url = base_url();
    $file->intialize();
}



