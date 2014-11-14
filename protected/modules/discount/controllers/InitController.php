<?php
namespace application\modules\discount\controllers;

use application\modules\discount\models\tables\Items;
use application\modules\discount\models\tables\DiscountActivity;
use application\modules\discount\services\command\DiscountActivityCommandService;
use application\modules\discount\services\command\ItemsCommandService;

class InitController extends \ExtendedController
{
    public function actionInit_all()
    {
        $this->actionDiscount();
        $this->actionItems();

        $this->redirect(array('/discount/discount/list'));
    }

    public function actionDiscount()
    {
        try {
            $discountActivity = new DiscountActivity();
            $service = new DiscountActivityCommandService($discountActivity);
            $service->sync();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionItems()
    {
        try {
            $items = new Items();
            $service = new ItemsCommandService($items);
            $service->init();
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function actionDitems()
    {
        try {
            $discountActivity = DiscountActivity::model()->findAll('activity_id = 576460752310069499');
            foreach ($discountActivity as $activity) {
                $service = new ItemsCommandService($activity);
                $service->initForDiscount();
            }
        } catch (\Exception $e) {
            $this->redirect(array('create', 'errors' => $e->getMessage()));
        }
    }

}