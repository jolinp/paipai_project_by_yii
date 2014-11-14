<?php
namespace application\vendor\pop;

use CJSON;
use PaiPaiOpenApiOauth;
use Yii;
use application\vendor\pop\request\ApiRequest;

require_once('src/PaiPaiOpenApiOauth.php');

class PopClient
{
    public $uin; //QQ号码

    public $accessToken;

    public function execute(ApiRequest $request, $session = '')
    {
        $client = $this->init();

        $api_path = $request->getApiMethodName();
        $client->setApiPath($api_path);

        $api_params = $request->getApiParams();
        foreach ($api_params as $name => $param) {
            $client->params[$name] = $param;
        }
        try {
            $response = $client->invoke();
            return $this->processResponse($response);
        } catch (Exception $e) {
            return array('errorCode' => $e->getCode(), 'errorMessage' => $e->getMessage());
        }
    }

    private function init()
    {
        $appOAuthID = Yii::app()->params['appOAuthID'];
        $appOAuthkey = Yii::app()->params['appOAuthkey'];

        $client = new PaiPaiOpenApiOauth($appOAuthID, $appOAuthkey, $this->accessToken, $this->uin);
        $client->setDebugOn(false); //开启debug
        $client->setMethod("get"); //or use post
        $client->setCharset("utf-8");
        $client->setFormat('json');
        $client->params['pureData'] = 1;
        $client->params['needRoot'] = 1;

        return $client;
    }

    private function processResponse($response)
    {
        $result = \CJSON::decode($response);
        if ($result['errorCode'] == 0 && $result['errorMessage'] == "") {
            unset($result['errorCode']);
            unset($result['errorMessage']);
        }

        return $result;
    }
}