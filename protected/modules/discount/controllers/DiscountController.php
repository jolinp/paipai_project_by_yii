<?php
namespace application\modules\discount\controllers;

use application\components\ActiveUser;
use application\modules\discount\components\ExtendedFormBuilder;
use application\modules\discount\models\tables\DiscountActivity;
use application\modules\discount\models\tables\DiscountItem;
use application\modules\discount\services\command\DiscountActivityCommandService;
use application\modules\discount\services\command\ItemsCommandService;
use application\modules\discount\services\query\DiscountItemsQueryService;

class DiscountController extends \ExtendedController
{
    public function actionForm()
    {
        $model = new DiscountActivity();
        $model->unsetAttributes();

        $builder = new ExtendedFormBuilder($model);
        $builder->setAction(array('create'));
        $builder->setNotDisplay('seller_uin, create_time, api_time, item_num, activity_id');
        $builder->setHtmlOptions(array('id' => 'activity-form'));
        $form = $builder->render();

        $this->render('form', array('form' => $form));
    }

    public function actionCreate(DiscountActivity $discountActivity)
    {
        try {
            $service = new DiscountActivityCommandService($discountActivity);
            $newActivityId = $service->create();
            $service->sync();

            ActiveUser::getInstance()->setActivityId($newActivityId);
            $this->redirect(array('/discount/item/list'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionUpdate($id)
    {
        $model = DiscountActivity::model()->findByPk($id);

        $builder = new ExtendedFormBuilder($model, array('activity_id' => 'hidden'));
        $builder->setAction(array('modify'));
        $builder->setNotDisplay('seller_uin, create_time, api_time, item_num');
        $builder->setHtmlOptions(array('id' => 'activity-form'));
        $form = $builder->render();

        $this->render('form', array('form' => $form));
    }

    public function actionModify(DiscountActivity $discountActivity)
    {
        try {
            $service = new DiscountActivityCommandService($discountActivity);
            $service->Modify();
            $service->sync();
            $this->redirect(array('list'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionDelete($id)
    {
        try {
            $discountActivity = DiscountActivity::model()->findByPk($id);
            $service = new DiscountActivityCommandService($discountActivity);
            $service->delete();
            $discountActivity->delete();

            $this->redirect(array('list'));
        } catch (\Exception $e) {
            echo $e->getMessage() . ",删除失败";
        }
    }

    public function actionList()
    {
        $model = new DiscountActivity('search');
        $model->unsetAttributes();
        $model->seller_uin = \Yii::app()->session->get('ActiveUser');

        $this->render('list', array('model' => $model));
    }

    public function actionItems(DiscountActivity $discountActivity)
    {
        $user = ActiveUser::getInstance();
        $user->setActivityId($discountActivity->activity_id);

        $commandService = new ItemsCommandService($discountActivity);
        $commandService->initForDiscount();

        $queryService = new DiscountItemsQueryService($discountActivity);
        $dataProvider = $queryService->queryDiscountItems();

        $this->render('items_list', array('dataProvider' => $dataProvider));
    }

    public function actionChange_id($activityId)
    {
        ActiveUser::getInstance()->setActivityId($activityId);
    }
}