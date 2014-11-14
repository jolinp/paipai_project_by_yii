<?php

class ExtendedAction extends CAction
{
    protected function runWithParamsInternal($object, $method, $params)
    {
        $ps = array();
        foreach ($method->getParameters() as $methodParam) {
            $hintClass = $methodParam->getClass();
            if (null !== $hintClass) {
                $modelName = $hintClass->name;
                if (is_subclass_of($modelName, 'CModel')) {
                    /** @var CModel $model */
                    $model = $this->createHintModel($modelName, $params);
                    if (is_subclass_of($model, 'ExcendedRequestModel')) {
                        /** @var ExcendedRequestModel $model */
                        $model->checkRenderHelp();
                    }
                    $ps[] = $model;
                }
            } else {
                $name = $methodParam->getName();
                if (isset($params[$name])) {
                    if ($methodParam->isArray())
                        $ps[] = is_array($params[$name]) ? $params[$name] : array($params[$name]);
                    elseif (!is_array($params[$name]))
                        $ps[] = $params[$name];
                    else
                        return false;
                } elseif ($methodParam->isDefaultValueAvailable())
                    $ps[] = $methodParam->getDefaultValue();
                else
                    return false;
            }
        }
        $method->invokeArgs($object, $ps);
        return true;
    }

    private function createHintModel($modelName, $params)
    {
        /** @var CModel $model */
        $model = new $modelName;
        $model->scenario = 'request';
        $this->assignModelAttributes($model, $params);
        return $model;
    }

    private function assignModelAttributes(CModel $model, $params)
    {
        $key = $this->findModelParamKey($model, $params);
        if (null === $key) {
            $model->attributes = $params;
        } else {
            $model->attributes = $params[$key];
        }
    }

    private function findModelParamKey(CModel $model, $params)
    {
        $keys = array_keys($params);
        $result = $this->getModelParamLongKey($model);
        if (!in_array($result, $keys)) {
            $result = $this->getModelParamShortKey($model);
            if (!in_array($result, $keys)) {
                $result = null;
            }
        }
        return $result;
    }

    private function getModelParamLongKey($model)
    {
        $result = get_class($model);
        $result = str_replace('\\', '_', $result);
        return $result;
    }

    private function getModelParamShortKey($model)
    {
        $result = get_class($model);
        $result = str_replace('\\', '/', $result);
        $result = basename($result);
        return $result;
    }
} 