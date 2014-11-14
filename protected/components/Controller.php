<?php
namespace application\components;

use CController;
use CJSON;
use Yii;

class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/main';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $access = '';
    public $tab = 0;
    public $css = array();
    public $js = array();

    public function renderJson($data)
    {
        $result = CJSON::encode($data);
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($result));
        echo $result;
    }

    protected function isDownLoadData()
    {
        $nick = $this->getLoginNick();
        $date = date('Y-m-d');
        $db_lock = DbLock::model()->find("nick=? AND date>=? AND type=?", array($nick, $date, 'VDATA'));
        if ($db_lock === null || $db_lock->is_finish != 1) {
            return false;
        }
        return true;
    }

    protected function getApiLoginUser()
    {
        $user = Yii::app()->session->get('ApiLoginUser');

        if ($user === null) {
            if (Yii::app()->getRequest()->getIsAjaxRequest() || isset($_REQUEST['ajax'])) {
                echo CJSON::encode(array('flag' => false, 'data' => array('msg' => "会话已经失效,请重新登录系统!")));
                Yii::app()->end();
            } else {
                $this->redirect(array('/auth/login'));
                Yii::app()->end();
                return false;
            }
        }
        return $user;
    }

    protected function setApiLoginUser($user)
    {
        Yii::app()->session->add('ApiLoginUser', $user);
    }
}
