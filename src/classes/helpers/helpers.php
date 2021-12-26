<?php


/**
 * Replace parametr after : sign with given parameterValue
 * ex. test.com/:variable/somePage to  test.com/parameterValue/somePage
 *
 * @param string  $resource         URL or part of URL with parametr defined by :
 * @param string  $parameterValue   parametr to replace in given resource
 * 
 * @return string $resource
 */ 
function addParametrToUrl(string $resource, string $parameterValue): string {
    if(strpos($resource, ':')){
        $colonIndex = strpos($resource, ':');
        $lastBackSlashIndex = strrpos($resource, '/');
        if($lastBackSlashIndex < $colonIndex){
            $resource = substr($resource, 0, $colonIndex) . $parameterValue;
        }else{
            $resource = substr($resource, 0, $colonIndex) . $parameterValue . substr($resource, $lastBackSlashIndex, -1);
        }

    }
    return $resource;
}