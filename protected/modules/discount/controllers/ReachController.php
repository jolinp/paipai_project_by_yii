<?php
namespace application\modules\discount\controllers;

use application\components\Controller;
use application\modules\discount\components\ExtendedFormBuilder;
use application\modules\discount\models\tables\ReachActivity;
use application\modules\discount\services\command\ReachActivityCommandService;

class ReachController extends Controller
{
    public function actionForm()
    {
        $model = new ReachActivity();
        $model->unsetAttributes();

        $builder = new ExtendedFormBuilder($model);
        $builder->setAction(array('create'));
        $form = $builder->render();

        echo $form;
    }

    public function actionCreate(ReachActivity $reachActivity)
    {
        try {
            $service = new ReachActivityCommandService($reachActivity);
            $service->create();
        } catch (\Exception $e) {
            $this->redirect(array('create', 'errors' => $e->getMessage()));
        }
    }

    public function actionUpdate(ReachActivity $reachActivity)
    {
        try {
            $service = new ReachActivityCommandService($reachActivity);
            $service->update();
        } catch (\Exception $e) {

        }
    }

    public function actionDelete(ReachActivity $reachActivity)
    {
        try {
            $service = new ReachActivityCommandService($reachActivity);
            $service->delete();
        } catch (\Exception $e) {

        }
    }

    public function actionList()
    {
        echo "草";
    }
}