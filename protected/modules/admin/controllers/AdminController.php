<?php

class AdminController extends \application\components\Controller
{
    public $layout = '//layouts/main2';

    public function actionIndex()
    {
        if (isset($_REQUEST["nick"])) {
            $nick = $_REQUEST["nick"];
            //@todo 测试环境使用
            if ($nick == '大麦信息001') {
                $_REQUEST["nick"] = 'lilys5';
                $nick = 'lilys5';
            }
            $auth = \application\models\tables\AuthSession::model()->find("taobao_user_nick=? ORDER BY create_time DESC", array($nick));
            if ($auth !== null) {
                $auth->login_user_nick = $auth->taobao_user_nick;
                $this->setApiLoginUser($auth);
                return $this->redirect(array("/site/index"));
            }
        }
        $where = "";
        if (isset($_REQUEST['search'])) {
            $nick = $_REQUEST['search'];
            $where = " AND nick LIKE '{$nick}%'";
        }
        $t = date("Y-m-d", time() - 60 * 24 * 3600);
        $adminQueryService = new \application\services\query\AdminQueryService();
        $users = $adminQueryService->getLoginNicks($where, $t);

        return $this->render('index', array('users' => $users));
    }

    public function actionLogin()
    {
        if (isset($_POST['LoginForm'])) {
            $nick = $_POST['LoginForm']['nick'];
            $screkey = $_POST['LoginForm']['screkey'];

            $adminQueryService = new \application\services\query\AdminQueryService();
            $user = $adminQueryService->getManager($nick);
            if ($user !== false) {
                if ($user["userpwd"] == $screkey) {
                    Yii::app()->session->add("__login_manager", $user);
                    return $this->redirect(array("index"));
                }
            }
        }
        $this->render('login');
    }

    public function actionLoginSetting()
    {
        $nick = $_REQUEST['nick'];
        $isLogin = $_REQUEST['is_login'];
        $onlineFlag = ($isLogin == "FALSE") ? "TRUE" : "FALSE";

        $adminCommandService = new \application\services\command\AdminCommandService();
        $adminCommandService->changeLoginState($nick, $onlineFlag);

        Yii::app()->user->setFlash('success', "修改成功");

        $this->redirect(array('index'));
        //echo "<script>alert('修改完毕，请重新刷新窗口');history.back();</script>";
    }

    public function actionInit()
    {
        $nick = $_REQUEST['nick'];
        $date = date('Y-m-d');
        $dbLockCommandService = new \application\services\command\DBLockCommandService();
        $dbLockCommandService->delAllLock($nick, $date);
        echo CJSON::encode(array('flag' => true, 'data' => array('msg' => '重置成功')));
        return;
    }

    public function loadModel($id)
    {
        $model = Agreement::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'agreement-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}