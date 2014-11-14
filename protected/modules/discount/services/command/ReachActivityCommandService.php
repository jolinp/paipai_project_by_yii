<?php
namespace application\modules\discount\services\command;

use application\modules\discount\services\CommonService;

class ReachActivityCommandService extends CommonService
{
    private $_params;

    public function __construct(\CModel $model)
    {
        parent::__construct($model);
    }

    public function create()
    {
        $ApiClassName = 'CreateManJianSong';
        //$this->setAfterInvoke('SAVE_DB'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);

        $result = $this->getApiResult();
        print_r($result);
    }

    public function update()
    {
        $ApiClassName = 'UpdateManJianSong';
        //$this->setAfterInvoke('DELETE_DB'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);

        $result = $this->getApiResult();
        print_r($result);
    }

    public function delete()
    {
        $ApiClassName = 'DeleteManJianSong';
        //$this->setAfterInvoke('DELETE_DB'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);

        $result = $this->getApiResult();
        print_r($result);
    }
}