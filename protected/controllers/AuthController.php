<?php
namespace application\controllers;

use Yii;
use application\models\tables\AuthSession;
use application\components\Controller;
use application\components\ActiveUser;

class AuthController extends Controller
{
    public function actionCallback()
    {
        if (!empty($_GET['?access_token'])) {

            $auth = new AuthSession();
            $auth->uin = $_GET['useruin'];
            $auth->access_token = $_GET['?access_token'];
            $auth->sign = $_GET['sign'];
            $auth->refresh();

            $user = ActiveUser::getInstance();
            $user->setUin($auth->uin);
            $user->setAccessToken($auth->access_token);

            $this->redirect(Yii::app()->homeUrl);
        }
    }

    public function actionLogin()
    {
        $appOAuthID = Yii::app()->params['appOAuthID'];
        $this->redirect('http://fuwu.paipai.com/my/app/authorizeGetAccessToken.xhtml?responseType=access_token&appOAuthID=' . $appOAuthID);
    }

    public function actionLogout()
    {
        $user = ActiveUser::getInstance();
        $user->clearIdentity();

        $appOAuthID = Yii::app()->params['appOAuthID'];
        $this->redirect('http://fuwu.paipai.com/my/app/authorizeGetAccessToken.xhtml?responseType=access_token&appOAuthID=' . $appOAuthID);
    }

}
