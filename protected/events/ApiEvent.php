<?php
namespace application\events;

use Yii;
use CComponent;
use CEvent;
use application\components\ActiveRecord;

class ApiEvent extends CComponent
{
    /**
     * @param CEvent $event
     */
    public function saveToDb($event)
    {
        $req = $event->params['req'];
        $resp = $event->params['resp'];

        if (!$this->hasError($resp)) {
            /** @var ActiveRecord $objectName */
            $objectName = $req->getRelatedObj();
            $translateAttributes = $this->translateAttributes($resp);
            $this->refreshDbData($objectName, $translateAttributes);
        }
    }

    public function batchToDb($event)
    {
        $req = $event->params['req'];
        $resp = $event->params['resp'];
        $rootName = $event->params['rootName'];

        if (!$this->hasError($resp)) {
            $objectName = $req->getRelatedObj();
            $mergeData = $this->mergeResponseData($resp, $rootName);
            foreach ($mergeData as $data) {
                $translateAttributes = $this->translateAttributes($data);
                $this->refreshDbData($objectName, $translateAttributes);
            }
        }
    }

    private function hasError($resp)
    {
        if (isset($resp['errorCode'])) {
            Yii::log($resp['errorCode'] . $resp['errorMessage'], 'error', 'api');
            return true;
        } else {
            return false;
        }
    }

    private function refreshDbData($objectName, $translateAttributes)
    {
        /** @var ActiveRecord $objectName */
        $object = $objectName::model()->findUniqueRecord($translateAttributes);
        $object->attributes = $translateAttributes;
        $object->save();

        Yii::log($object->getErrorsText(), 'error', 'api');
    }

    private function translateAttributes($resp)
    {
        $result = array();
        foreach ($resp as $key => $value) {
            $newKey = \fGrammar::underscorize($key);
            $result[$newKey] = $value;
        }
        return $result;
    }

    private function mergeResponseData($resp, $rootName)
    {
        $result = array();
        if (isset($resp[$rootName]) && !empty($resp[$rootName])) {
            $array = $resp[$rootName];
            unset($resp[$rootName]);    //此处unset是为了用于下面的merge其他应该有的数据
            foreach ($array as $value) {
                $result[] = array_merge($value, $resp);
            }
        }
        return $result;
    }
}