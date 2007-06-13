<?php
/*
 * Created on 2007/05/17
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 require_once('JSON.php');

    $json = new JSON();

    $obj = array(
       id   => array("foo", "bar", array( aa => 'bb')),
       hoge => 'boge',
       no   => 123 ,
       bo   => true
    );
    
    $js = $json->encode($obj);
    print "$js";
 
?>
