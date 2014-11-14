<?php
namespace application\vendor\pop\request;

abstract class ApiRequest
{
    public $params = array();

    public function getApiParams(){
        return $this->params;
    }

    abstract function getApiMethodName();

    abstract function getRelatedObj();

    abstract function getRelatedTable();
}