<?php
namespace application\modules\discount\services\command;

use application\modules\discount\models\tables\DiscountActivity;
use application\modules\discount\services\CommonService;

class DiscountActivityCommandService extends CommonService
{
    public function __construct(\CModel $model)
    {
        parent::__construct($model);
    }

    public function sync()
    {
        DiscountActivity::model()->deleteAll('seller_uin = ' . $this->getUin());

        $ApiClassName = 'GetLtdActive';
        $this->setRootName('activityList');
        $this->setAfterInvoke('batchToDb'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);
        /*$result = $this->getApiResult();
        print_r($result);*/
    }

    public function create()
    {
        $ApiClassName = 'AddLtdActive';
        $this->invokeApi($ApiClassName, $this->_params);

        if ($this->noErrors()) {
            $result = $this->getApiResult();
            return $result['activityId'];
        } else {
            throw new \Exception($this->getError());
        }
    }

    public function Modify()
    {
        $ApiClassName = 'ModifyLtdActive';
        $this->invokeApi($ApiClassName, $this->_params);

        return $this->noErrors();
    }

    public function delete()
    {
        $ApiClassName = 'DelLtdActive';
        $this->invokeApi($ApiClassName, $this->_params);

        if (!$this->noErrors()) {
            throw new \Exception($this->getError());
        }
    }
}