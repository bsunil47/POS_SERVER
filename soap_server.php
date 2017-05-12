<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/25/2016
 * Time: 12:41 AM
 */
require_once('lib/nusoap.php');
function getData($data){
    return ['ListArray'=> $data];
}

$server = new nusoap_server();
$server->configureWSDL('soapData','urn:soapData');
$server->wsdl->addComplexType('myData','complexType','struct','all','',
    array(  'email'  => array('name' => 'email','type' => 'xsd:string'),
            'password'      => array('password' => 'password','type' => 'xsd:string')
    )
);
$server->wsdl->addComplexType('package','complexType','struct','all','',
    array(  'CODE'      => array('name' => 'data','type' => 'tns:myData'),
        'INFO'      => array('name' => 'data','type' => 'tns:myData')
    )
);
$server->register('getData',array('data'=> 'xsd:array'),array('return'=> 'tns:package'));
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);