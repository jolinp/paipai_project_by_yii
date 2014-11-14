<?php
namespace application\behaviors;

use CBehavior;
use application\components\ActiveUser;
use CEvent;
use application\vendor\pop\PopClient;
use application\events\ApiEvent;

class InvokeApiBehavior extends CBehavior
{
    private $_rootName;

    private $_result;

    private $afterInvoke = null; //用afterInvoke属性记录API调用之后，需要把返回的数据进行如何处理


    /**
     * @param $ApiClassName 接口类名，放在protected/vendor/pop.request里面
     * @param array $params 接口需要传的参数
     */
    public function invokeApi($ApiClassName, $params = array())
    {
        /** @var ActiveUser $owner */
        $owner = $this->owner;

        $c = new PopClient();
        $c->uin = $owner->getUin();
        $c->accessToken = $owner->getAccessToken();

        $req = $this->createApiRequest($ApiClassName, $params);
        $resp = $c->execute($req);
        $this->_result = $resp;

        if (null !== $this->afterInvoke) {
            $this->onAfterInvoke = $this->afterInvoke;
            $this->onAfterInvoke($req, $resp, $this->_rootName);
        }
    }

    /**
     * @param $method
     * Example: setAfterInvoke('SAVE_DB');   表示返回的数据保存到DB中，必须在对应API的类中指定getRelatedObj()
     * @return $this->afterInvoke
     */
    public function setAfterInvoke($method)
    {
        $Object = new ApiEvent();
        return $this->afterInvoke = array($Object, $method);
    }

    public function onAfterInvoke($req, $resp, $rootName)
    {
        $event = new CEvent($this, array(
            'resp' => $resp,
            'req' => $req,
            'rootName' => $rootName,
        ));
        $this->raiseEvent('onAfterInvoke', $event);
    }

    public function getApiResult($key = '')
    {
        if (empty($key)) {
            return $this->_result;
        } else {
            return $this->_result[$key];
        }
    }

    public function noErrors()
    {
        return empty($this->_result['errorCode']) ? true : false;
    }

    public function getError()
    {
        return $this->_result['errorMessage'];
    }

    public function setRootName($string)
    {
        $this->_rootName = $string;
    }

    /**
     * below are private function
     */
    protected function createApiRequest($ApiClassName, $params)
    {
        $class = 'application\vendor\pop\request\\' . $ApiClassName . 'Request';
        $req = new $class();
        foreach ($params as $key => $value) {
            $methodName = 'set' . $key;
            if (method_exists($req, $methodName)) {
                $req->{'set' . $key}($value);
            }
        }

        return $req;
    }
}