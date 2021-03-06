<?php

/**
 * 增强版 ActiveRecord
 *
 * 提供以下几个功能
 * 1.不同 Model对应不用数据库功能
 * 2.自动使用语言文件翻译字段的功能
 * 3.合并错误信息输出文本功能
 *
 * @author Jeffrey Au <fly88oj@163.com>
 * @version $Id$
 */
class ExtendedActiveRecord extends CActiveRecord
{
    public static $db = array();

    public function getDbConnection()
    {
        $model = get_class($this);
        if (isset(self::$db[$model])) {
            return self::$db[$model];
        } else {
            $componentName = $this->connectionName();
            self::$db[$model] = Yii::app()->getComponent($componentName);
            if (self::$db[$model] instanceof CDbConnection)
                return self::$db[$model];
            else {
                $message = 'Active Record keyword requires a "' . $componentName . '" CDbConnection application component.';
                Yii::log($message, CLogger::LEVEL_ERROR, 'extended');
                throw new CDbException(Yii::t('yii', $message));
            }
        }
    }

    public function connectionName()
    {
        return 'db';
    }

    public function attributeLabels()
    {
        $result = array();
        $attributes = $this->attributeNames();
        $relations = array_keys($this->relations());
        $attributes = array_merge($relations, $attributes);
        foreach ($attributes as $attribute) {
            $special = get_class($this) . '.' . $attribute;
            $label = Yii::t('models', $special);
            if ($label == $special)
                $label = Yii::t('models', $attribute);
            $result[$attribute] = $label;
        }
        return $result;
    }

    public function getErrorsText($separator = '<br/>')
    {
        $messages = array();
        $errors = $this->getErrors();
        foreach ($errors as $error)
            $messages[] = pos($error);
        return implode($separator, $messages);
    }

    public function getIdentity()
    {
        return $this->getPrimaryKey();
    }
}