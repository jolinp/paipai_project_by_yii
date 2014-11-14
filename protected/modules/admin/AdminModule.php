<?php

class AdminModule extends CWebModule
{
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'admin.models.*',
            'admin.components.*',
            'admin.controllers.*',
            'admin.views.*',
        ));
    }

    /**
     * @param CController $controller
     * @param CAction $action
     * @return bool|void
     */
    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            $login = Yii::app()->session->get("__login_manager");
            if ($login === null && $action->getId() != 'login') {
                return $controller->redirect(array("/admin/admin/login"));
            }
            return true;
        } else
            return false;
    }
}
