<?php
const BR = '</br>';
function pr(...$args){
    foreach($args as $arr){

        var_dump($arr);

        
    }
}
function pre(...$args){
    foreach($args as $arr){
        print('<pre>');
        htmlspecialchars($arr);
        print('</pre>');
        
    }
}
function prs(...$args){
    foreach($args as $arr){

        var_dump($arr);

    }
    die();
}
function prsh(...$args){
    foreach($args as $arr){
        echo('<pre>');
        htmlspecialchars($arr);
        echo('</pre>');
    }
    die();
}
function included($path, $data){
    include $path;
}