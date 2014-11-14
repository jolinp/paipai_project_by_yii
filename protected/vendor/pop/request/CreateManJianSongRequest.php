<?php
namespace application\vendor\pop\request;

class CreateManJianSong extends ApiRequest
{

    function getApiMethodName()
    {
        return '/yingxiao/createManJianSong.xhtml';
    }

    function getRelatedObj()
    {
        // TODO: Implement getRelatedObj() method.
    }

    function getRelatedTable()
    {
        // TODO: Implement getRelatedTable() method.
    }

    public function setUin($number)
    {
        $this->params['uin'] = $number;
    }

    public function setSellerUin($number)
    {
        $this->params['sellerUin'] = $number;
    }

    public function setBeginTime($string)
    {
        $this->params['beginTime'] = $string;
    }

    public function setEndTime($string)
    {
        $this->params['endTime'] = $string;
    }

    public function setActivityDesc($string)
    {
        $this->params['activityDesc'] = $string;
    }

    public function setContentJson($string)
    {
        $this->params['contentJson'] = $string;
    }
}