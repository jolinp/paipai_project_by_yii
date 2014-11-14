<?php

class ExtendedController extends CController
{
    /**
     * Creates the action instance based on the action name.
     * The action can be either an inline action or an object.
     * The latter is created by looking up the action map specified in {@link actions}.
     * @param string $actionID ID of the action. If empty, the {@link defaultAction default action} will be used.
     * @throws CException
     * @return CAction the action instance, null if the action does not exist.
     * @see actions
     */
    public function createAction($actionID)
    {
        if ($actionID === '')
            $actionID = $this->defaultAction;
        if (method_exists($this, 'action' . $actionID) && strcasecmp($actionID, 's')) // we have actions method
            return new ExtendedInlineAction($this, $actionID);
        else {
            $action = $this->createActionFromMap($this->actions(), $actionID, $actionID);
            if ($action !== null && !method_exists($action, 'run'))
                throw new CException(Yii::t('yii', 'Action class {class} must implement the "run" method.', array('{class}' => get_class($action))));
            return $action;
        }
    }

    public function getActionParams()
    {
        return array_merge($_GET, $_POST);
    }
}