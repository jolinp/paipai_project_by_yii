<?php
namespace application\components;

use TopLinker_Request_Abstract;
use TopLinker_Response_Abstract;
use Yii;
use application\models\tables\AdGroup;
use application\models\tables\Campaign;
use application\models\tables\Item;
use application\models\tables\ItemDetail;
use application\models\tables\Keyword;
use application\services\command\DBLockCommandService;
use application\services\query\DBLockQueryService;

class TopLinkerTool
{
    /**
     * @var \TopLinker
     */
    private $linker;
    private $_trades = array();
    private $tradesPageCount;
    private $downloadCampaignMsg = array();
    private $downloadItemMsg = array();
    private $num_iids = array();
    private $getLoginAuthsignMsg = array();
    private $downloadCustrptMsg = array();
    private $downloadCamprptMsg = array();
    private $downloadAdgrouprptMsg = array();
    private $downloadKeywordrptMsg = array();
    private $downloadCreativeMsg = array();
    private $downloadDelAdgroupMsg = array();
    private $downloadAddAdgroupMsg = array();
    private $downloadAddCreativeMsg = array();
    private $downloadUpdateCampaignMsg = array();
    private $downloadUpdateAdgroupMsg = array();
    private $downloadBudgetCampaignMsg = array();
    private $updateBudgetCampaignMsg = array();
    private $nonsearchPlacesAddMsg = array();
    private $downloadKeywordMsg = array();
    private $deleteKeywordsMsg = array();
    private $lockQuery = null;
    private $lockCommand = null;

    function __construct($appKey, $secretKey, $maxRequest = 10)
    {
        $config = new \TopLinker_Config($appKey, $secretKey); // 先创建 TopLinker 配置对象
        $config->maxRequest = $maxRequest; // 设置最大并发请求数，默认值是 100
        if (YII_DEBUG) {
            $config->curlOptions = array(
                CURLOPT_PROXY => "192.168.2.223:8888"
            );
        }
        $this->linker = new \TopLinker($config); // 把配置传入

        $this->lockQuery = new DBLockQueryService();
        $this->lockCommand = new DBLockCommandService();
    }

    public function getSubwayToken($nick, $access_token)
    {
        $rpt_session = Yii::app()->session->get('rpt_session');
        if (empty($rpt_session[$nick])) {
            $params = array(
                'nick' => $nick,
            );

            $this->linker->call('taobao.simba.login.authsign.get', array($this, 'processAuthsign'), $params, $access_token); // 带有 Session 的请求
            $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束
            if ($this->getLoginAuthsignMsg['flag'] == false) {
                throw new \CException($nick . ' SubwayToken GET ERROR.' . $this->getLoginAuthsignMsg['msg']);
            } else {
                $rpt_session[$nick] = $this->getLoginAuthsignMsg['subway_token'];
                Yii::app()->session->add('rpt_session', $rpt_session);
            }
        }
        return $rpt_session[$nick];
    }

    public function processAuthsign(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        if (!$response->hasError()) {
            $responseArr = $response->getData();
            if (isset($responseArr['code'])) {
                $this->getLoginAuthsignMsg['flag'] = false;
                $this->getLoginAuthsignMsg['msg'] = $responseArr['msg'] . $responseArr['sub_msg'];
            } else {
                $this->getLoginAuthsignMsg['flag'] = true;
                $this->getLoginAuthsignMsg['subway_token'] = $responseArr['subway_token'];
            }

        } else {
            $this->getLoginAuthsignMsg['flag'] = false;
            $this->getLoginAuthsignMsg['msg'] = $response->getError();
        }
    }

    /*
     * 多线程下载店铺真实销量数据到ztc0120
     */

    public function actionTradesSoldGet($nick, $days, $access_token)
    {
        $date = date("Y-m-d");
        $start_date = date("Y-m-d", strtotime("-{$days} day"));
        $end_date = date("Y-m-d");

        $lock_type = 'SALES';

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $lock_type, 10);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $lock_type);

        $fields = 'seller_nick, title,num_iid, status,payment,total_fee,num,price,orders,pay_time';
        $type = 'fixed,auction,guarantee_trade,step,independent_simple_trade,independent_shop_trade,auto_delivery,ec,cod,shopex_trade,netcn_trade,external_trade,instant_trade,b2c_cod,hotel_trade,super_market_trade,super_market_cod_trade,taohua,waimai,nopaid,step,eticket';

        try {
            //请求第一页
            $params = array(
                'page_no' => 1,
                'page_size' => 100,
                'fields' => $fields,
                'type' => $type,
                'status' => 'WAIT_BUYER_CONFIRM_GOODS,TRADE_FINISHED',
                'start_created' => $start_date,
                'end_created' => $end_date,
            );
            $this->linker->call('taobao.trades.sold.get', array($this, 'processTradesSold'), $params, $access_token); // 带有 Session 的请求
            $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

            //取得总页数
            $page_count = $this->tradesPageCount;

            //取得其他页数据
            for ($i = 2; $i <= $page_count; $i++) {
                $params = array(
                    'page_no' => $i,
                    'page_size' => 100,
                    'fields' => $fields,
                    'type' => $type,
                    'status' => 'WAIT_BUYER_CONFIRM_GOODS,TRADE_FINISHED',
                    'start_created' => $start_date,
                    'end_created' => $end_date,
                );

                $this->linker->call('taobao.trades.sold.get', array($this, 'processTradesSold'), $params, $access_token); // 带有 Session 的请求

            }

            $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

            $columns = array();
            foreach ($this->_trades as $key => $num) {
                $columns[] = array(
                    'nick' => $nick,
                    'item_id' => $key,
                    'sales_count' => $num['num'],
                    'api_time' => date('Y-m-d H:i:s'),
                );
            }
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                $batchCommand->replace('ztc0120', $columns);
            }

            $this->_trades = array();

        } catch (Exception $e) {
            $this->lockCommand->delLock($nick, $date, $lock_type);
            return array('flag' => false, 'msg' => '执行失败:' . $e->getMessage());
        }

        $this->lockCommand->unlock($lock);

        return array('flag' => true, 'msg' => '执行成功');

    }

    public function processTradesSold(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        if (!$response->hasError()) {
            // 处理结果
            $responseArr = $response->getData();
            if (!empty($responseArr['trades_sold_get_response'])) {
                $page_count = (int)($responseArr['trades_sold_get_response']['trades'] / 101) + 1;
                $this->tradesPageCount = $page_count;

                $trades = empty($responseArr['trades_sold_get_response']['trades']['trade']) ? array() : $responseArr['trades_sold_get_response']['trades']['trade'];
                foreach ($trades as $trade) {
                    $orders = $trade['orders']['order'];
                    if (empty($orders)) $orders = array();

                    foreach ($orders as $order) {
                        $num_iid = $order['num_iid'];
                        if (empty($this->_trades[$num_iid])) {
                            $this->_trades[$num_iid] = array('num' => $order['num']);
                        } else {
                            $this->_trades[$num_iid]['num'] += $order['num'];
                        }
                    }
                }
            }

        } else {
            // 处理错误
            print_r($response->getError());
        }
    }

    /**
     * @param $nick
     * @param $access_token
     * @return array
     * 下载推广计划
     */
    public function actionCampaignGet($nick, $access_token)
    {
        $date = date("Y-m-d");
        $type = 'CAMPAIGN';

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 5);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $type);

        $params = array(
            'nick' => $nick,
        );

        $this->linker->call('taobao.simba.campaigns.get', array($this, 'processCampaign'), $params, $access_token); // 带有 Session 的请求
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        if ($this->downloadCampaignMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
        } else {
            $this->lockCommand->unlock($lock);
        }

        return $this->downloadCampaignMsg;

    }

    public function processCampaign(\TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadCampaignMsg['flag'] = false;
            $this->downloadCampaignMsg['msg'] = $retArr['msg'];
            return;
        }

        $responseArr = $retArr['response'];
        $campaigns = $responseArr['campaigns']['campaign'];
        $columns = array();
        foreach ($campaigns as $campaign) {
            $campaign = (object)$campaign;
            $columns[] = array(
                'nick' => $campaign->nick,
                'campaign_id' => $campaign->campaign_id,
                'title' => $campaign->title,
                'settle_status' => $campaign->settle_status,
                'settle_reason' => $campaign->settle_reason,
                'create_time' => $campaign->create_time,
                'modified_time' => $campaign->modified_time,
                'online_status' => $campaign->online_status,
                'api_time' => date('Y-m-d H:i:s')
            );
        }
        try {
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                $batchCommand->replace(Campaign::model()->tableName(), $columns);
            }
            $this->downloadCampaignMsg['flag'] = true;
        } catch (Exception $e) {
            $this->downloadCampaignMsg['flag'] = false;
            $this->downloadCampaignMsg['msg'] = $e->getMessage();
        }
    }

    protected function processCommon(TopLinker_Response_Abstract $response)
    {
        if (!$response->hasError()) {
            // 处理结果
            $responseArr = $response->getData();
            if (isset($responseArr['code'])) {
                if (isset($responseArr['sub_msg']))
                    return array('flag' => false, 'msg' => $responseArr['msg'] . $responseArr['sub_msg']);
                else
                    return array('flag' => false, 'msg' => $responseArr['msg']);
            } else {
                return array('flag' => true, 'response' => $responseArr);
            }

        } else {
            // 处理错误
            $responseArr = $response->getError();
            if(isset($responseArr->code))
                return array('flag' => false, 'msg' => $responseArr['msg'].$responseArr['sub_msg']);
            else
                return array('flag' => false, 'msg' => 'processCommon Errors!');
        }
    }

    /**
     * @param $nick
     * @param array $campaigns
     * @param $access_token
     * @param reflesh :是否强制重刷数据
     * @return array
     * 初始化推广组
     */
    public function actionAdgroupGet($nick, array $campaigns, $access_token, $refresh = false)
    {
        $date = date("Y-m-d");
        $type = 'ADGROUP';

        if ($refresh == false) {
            $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 10);
            if (isset($result['isFinish']) && $result['isFinish'] == true)
                return array('flag' => true, 'msg' => '已下载过');
            if (isset($result['isLock']) && $result['isLock'] == true)
                return array('flag' => false, 'msg' => '其他用户在下载中');
        }
        $lock = $this->lockCommand->lock($nick, $date, $type);

        //先下载第一页
        foreach ($campaigns as $campaign) {
            $params = array(
                'nick' => $nick,
                'campaign_id' => $campaign,
                'page_no' => 1,
                'page_size' => 200,
            );

            $this->linker->call('taobao.simba.adgroupsbycampaignid.get', array($this, 'processAdgroup'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        if ($this->download_adgroup_msg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
            return $this->download_adgroup_msg;
        } else {
            //下载其他页
            if (isset($this->download_adgroup_msg['pages'])) {
                $campaigns_pages = $this->download_adgroup_msg['pages'];
                foreach ($campaigns_pages as $k => $v) {
                    if ($v > 1) {
                        for ($p = 2; $p <= $v; $p++) {
                            $params = array(
                                'nick' => $nick,
                                'campaign_id' => $k,
                                'page_no' => $p,
                                'page_size' => 200,
                            );
                            $this->linker->call('taobao.simba.adgroupsbycampaignid.get', array($this, 'processAdgroup'), $params, $access_token); // 带有 Session 的请求
                        }
                    }
                }
                $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束
            }
            if ($this->download_adgroup_msg['flag'] === false) {
                $this->lockCommand->delLock($nick, $date, $type);
            } else {
                $this->lockCommand->unlock($lock);
            }
            return $this->download_adgroup_msg;
        }

    }

    public function processAdgroup(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $campaign_id = $request->campaign_id;
        $page_no = $request->page_no;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->download_adgroup_msg['flag'] = false;
            $this->download_adgroup_msg['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        if (!isset($responseArr['adgroups']['adgroup_list']['a_d_group'])) {
            $this->download_adgroup_msg['flag'] = true;
            return;
        }
        $adgroups = $responseArr['adgroups']['adgroup_list']['a_d_group'];
        $page_count = (int)($responseArr['adgroups']['total_item'] / 201) + 1;
        $columns = array();
        foreach ($adgroups as $adgroup) {
            $adgroup = (object)$adgroup;
            $columns[] = array(
                'adgroup_id' => $adgroup->adgroup_id,
                'campaign_id' => $adgroup->campaign_id,
                'nick' => $adgroup->nick,
                'category_ids' => $adgroup->category_ids,
                'num_iid' => $adgroup->num_iid,
                'default_price' => $adgroup->default_price,
                'nonsearch_max_price' => $adgroup->nonsearch_max_price,
                'is_nonsearch_default_price' => $adgroup->is_nonsearch_default_price == 'true' ? 1 : 0,
                'online_status' => $adgroup->online_status,
                'offline_type' => $adgroup->offline_type,
                'reason' => $adgroup->reason,
                'create_time' => $adgroup->create_time,
                'modified_time' => $adgroup->modified_time,
                'api_time' => date('Y-m-d H:i:s')
            );
        }
        try {
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                AdGroup::model()->deleteAll("campaign_id=? and api_time <(NOW() - INTERVAL 20 second)", array($campaign_id));
                $batchCommand->replace(AdGroup::model()->tableName(), $columns);
            }
            $this->download_adgroup_msg['flag'] = true;
            if ($page_no == 1)
                $this->download_adgroup_msg['pages'][$campaign_id] = $page_count;
        } catch (Exception $e) {
            $this->download_adgroup_msg['flag'] = false;
            $this->download_adgroup_msg['msg'] = $e->getMessage();
        }

    }

    /**
     * @param $nick
     * @param array $adgroups
     * @param $access_token
     * @return array
     * 批量下载推广组创意标题
     */
    public function actionCreativeGet($nick, array $adgroups, $access_token)
    {
        $date = date("Y-m-d");
        $type = 'CREATIVE';

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 10);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $type);

        //先下载第一页
        foreach ($adgroups as $adgroup) {
            $params = array(
                'nick' => $nick,
                'adgroup_id' => $adgroup,
            );

            $this->linker->call('taobao.simba.creatives.get', array($this, 'processCreative'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        if ($this->downloadCreativeMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
        } else {

            $this->lockCommand->unlock($lock);
        }

        return $this->downloadCreativeMsg;

    }

    public function processCreative(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadCreativeMsg['flag'] = false;
            $this->downloadCreativeMsg['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];

        $creatives = $responseArr['creatives']['creative'];
        $columns = array();
        foreach ($creatives as $creative) {
            $creative = (object)$creative;
            $columns[] = array(
                'nick' => $creative->nick,
                'campaign_id' => $creative->campaign_id,
                'adgroup_id' => $creative->adgroup_id,
                'creative_id' => $creative->creative_id,
                'title' => $creative->title,
                'img_url' => $creative->img_url,
                'audit_status' => $creative->audit_status,
                'audit_desc' => $creative->audit_desc,
                'create_time' => $creative->create_time,
                'modified_time' => $creative->modified_time,
                'api_time' => date('Y-m-d H:i:s'),
            );
        }
        try {
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                $batchCommand->replace(\application\models\tables\Creative::model()->tableName(), $columns);
            }
            $this->downloadCreativeMsg['flag'] = true;
        } catch (Exception $e) {
            $this->downloadCreativeMsg['flag'] = false;
            $this->downloadCreativeMsg['msg'] = $e->getMessage();
        }

    }

    /**
     * @param $nick
     * @param $access_token
     * @return array
     * 初始化宝贝信息
     */
    public function actionItemGet($nick, $access_token)
    {
        $date = date("Y-m-d");
        $type = 'ITEM';

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 10);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $type);
        ItemDetail::model()->deleteAll("nick=? and api_time<curdate()", array($nick));
        //先下载在线销售的全部宝贝第一页
        $params = array(
            'nick' => $nick,
            'page_no' => 1,
            'page_size' => 200,
        );

        $this->linker->call('taobao.simba.adgroup.onlineitemsvon.get', array($this, 'processItemOnline'), $params, $access_token); // 带有 Session 的请求
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        if ($this->downloadItemMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
            return $this->downloadItemMsg;
        }

        //下载在线销售的全部宝贝其他页
        $pages = $this->downloadItemMsg['pages'];
        if ($pages > 1) {
            if ($pages > 50) $pages = 50;

            for ($p = 2; $p <= $pages; $p++) {
                $params = array(
                    'nick' => $nick,
                    'page_no' => $p,
                    'page_size' => 200,
                );
                $this->linker->call('taobao.simba.adgroup.onlineitemsvon.get', array($this, 'processItemOnline'), $params, $access_token); // 带有 Session 的请求
            }
        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束
        if ($this->downloadItemMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
            return $this->downloadItemMsg;
        }

        //批量获取商品信息
        Item::model()->deleteAll("nick=? and api_time<curdate()", array($nick));

        $adgroups = AdGroup::model()->findAll("nick=?", array($nick));
        foreach ($adgroups as $adgroup) {
            $this->num_iids[$adgroup->num_iid] = $adgroup->num_iid;
        }
        $this->num_iids = array_unique($this->num_iids);

        if (!empty($this->num_iids)) {
            $chunk_itmess = array_chunk($this->num_iids, 20);
            foreach ($chunk_itmess as $chunk_item) {
                $chunk_item = implode(',', $chunk_item);
                $params = array(
                    'fields' => 'detail_url,num_iid,title,pic_url,price,approve_status,nick,cid,list_time,delist_time',
                    'num_iids' => $chunk_item,
                );
                $this->linker->call('taobao.items.list.get', array($this, 'processItem'), $params, $access_token); // 带有 Session 的请求
            }
            $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束
        }
        if ($this->downloadItemMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
        } else {
            $this->lockCommand->unlock($lock);
        }

        return $this->downloadItemMsg;


    }

    public function processItemOnline(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $nick = $request->nick;
        $page_no = $request->page_no;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadItemMsg['flag'] = false;
            $this->downloadItemMsg['msg'] = $retArr['msg'];
            $this->downloadItemMsg['method'] = 'processItemOnline';
            return;
        }
        $responseArr = $retArr['response'];

        $items_detail = $responseArr['page_item']['item_list']['subway_item'];
        $page_count = (int)($responseArr['page_item']['total_item'] / 201) + 1;
        $columns = array();
        foreach ($items_detail as $item) {
            $item = (object)$item;
            $this->num_iids[$item->num_id] = $item->num_id;
            $columns[] = array(
                'nick' => empty($item->nick) ? $nick : $item->nick,
                'num_id' => $item->num_id,
                'price' => $item->price,
                'title' => $item->title,
                'publish_time' => $item->extra_attributes->publish_time,
                'quantity' => $item->extra_attributes->quantity,
                'sales_count' => $item->extra_attributes->sales_count,
                'api_time' => date('Y-m-d H:i:s')
            );
        }
        try {
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                $batchCommand->replace(\application\models\tables\ItemDetail::model()->tableName(), $columns);
            }
            $this->downloadItemMsg['flag'] = true;
            if ($page_no == 1)
                $this->downloadItemMsg['pages'] = $page_count;
        } catch (Exception $e) {
            $this->downloadItemMsg['flag'] = false;
            $this->downloadItemMsg['msg'] = $e->getMessage();
            $this->downloadItemMsg['method'] = 'processItemOnline';
        }

    }

    public function processItem(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->downloadItemMsg['flag'] = false;
            $this->downloadItemMsg['msg'] = $retArr['msg'];
            $this->downloadItemMsg['method'] = 'processItem';
            return;
        }
        $responseArr = $retArr['response'];

        $items = $responseArr['items']['item'];
        $columns = array();
        foreach ($items as $item) {
            $item = (object)$item;
            if (empty($item)) {
                continue;
            }
            $columns[] = array(
                'num_iid' => $item->num_iid,
                'detail_url' => $item->detail_url,
                'title' => $item->title,
                'pic_url' => $item->pic_url,
                'list_time' => $item->list_time,
                'delist_time' => $item->delist_time,
                'approve_status' => $item->approve_status,
                'nick' => $item->nick,
                'cid' => $item->cid,
                'price' => $item->price,
                'api_time' => date('Y-m-d H:i:s')
            );
        }
        try {
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                $batchCommand->replace(\application\models\tables\Item::model()->tableName(), $columns);
            }
            $this->downloadItemMsg['flag'] = true;
        } catch (Exception $e) {
            $this->downloadItemMsg['flag'] = false;
            $this->downloadItemMsg['msg'] = $e->getMessage();
            $this->downloadItemMsg['method'] = 'processItem';
        }

    }

    /**
     * @param $nick
     * @param $days
     * @param $session
     * @return array
     * 下载客户账户报表数据
     */
    public function actionCustRpt($nick, $days, $access_token, $subway_token)
    {
        $date = date("Y-m-d");
        $type = 'CUSTRPT';

        $start_time = date('Y-m-d', time() - $days * 24 * 60 * 60);
        $end_time = date('Y-m-d', time() - 1 * 24 * 60 * 60);

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 10);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $type);

        $params = array(
            'nick' => $nick,
            'subway_token' => $subway_token,
            'source' => 'SUMMARY',
            'start_time' => $start_time,
            'end_time' => $end_time,
        );

        $this->linker->call('taobao.simba.rpt.custbase.get', array($this, 'processCustRpt'), $params, $access_token); // 带有 Session 的请求
        $this->linker->call('taobao.simba.rpt.custeffect.get', array($this, 'processCustRpt'), $params, $access_token); // 带有 Session 的请求
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束
        if ($this->downloadCustrptMsg['flag'] == false) {
            $this->lockCommand->delLock($nick, $date, $type);
        } else {
            $this->lockCommand->unlock($lock);
        }

        return $this->downloadCustrptMsg;
    }

    public function processCustRpt(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $nick = $request->nick;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadCustrptMsg['flag'] = false;
            $this->downloadCustrptMsg['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $columns = array();
        if (isset($responseArr['rpt_cust_base_list'])) {
            $bases = $responseArr['rpt_cust_base_list'];
            try {
                foreach ($bases as $base) {
                    $base = (object)$base;
                    $columns[] = array(
                        'nick' => $base->nick
                    , 'date' => $base->date
                    , 'impressions' => $base->impressions
                    , 'click' => $base->click
                    , 'cost' => $base->cost
                    , 'aclick' => $base->aclick
                    , 'ctr' => $base->ctr
                    , 'cpc' => $base->cpc
                    , 'cpm' => $base->cpm
                    , 'source' => $base->source
                    , 'api_time' => date('Y-m-d H:i:s')
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    $batchCommand->replace(\application\models\tables\RptCustBase::model()->tableName(), $columns);
                }
                $this->downloadCustrptMsg['flag'] = true;
            } catch (Exception $e) {
                $this->downloadCustrptMsg['flag'] = false;
                $this->downloadCustrptMsg['msg'] = $e->getMessage();
            }
        } else if (isset($responseArr['rpt_cust_effect_list'])) {
            $effects = $responseArr['rpt_cust_effect_list'];
            try {
                foreach ($effects as $effect) {
                    $effect = (object)$effect;
                    $columns[] = array(
                        'nick' => $effect->nick
                    , 'date' => $effect->date
                    , 'directpay' => $effect->directpay
                    , 'indirectpay' => $effect->indirectpay
                    , 'directpaycount' => $effect->directpaycount
                    , 'indirectpaycount' => $effect->indirectpaycount
                    , 'favitemcount' => $effect->favitemcount
                    , 'favshopcount' => $effect->favshopcount
                    , 'source' => $effect->source
                    , 'api_time' => date('Y-m-d H:i:s')
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    $batchCommand->replace(\application\models\tables\RptCustEffect::model()->tableName(), $columns);
                }
                $this->downloadCustrptMsg['flag'] = true;
            } catch (Exception $e) {
                $this->downloadCustrptMsg['flag'] = false;
                $this->downloadCustrptMsg['msg'] = $e->getMessage();
            }
        } else {
            $this->downloadCustrptMsg['flag'] = false;
            $this->downloadCustrptMsg['msg'] = '没有报表可下';
        }
    }

    /**
     * @param $nick
     * @param array $campaigns
     * @param $days
     * @param $access_token
     * @param $subway_token
     * @return array
     * 下载推广计划报表
     */
    public function actionCampRpt($nick, array $campaigns, $days, $access_token, $subway_token)
    {
        $date = date("Y-m-d");
        $type = 'CAMPAIGNRPT';

        $start_time = date('Y-m-d', time() - $days * 24 * 60 * 60);
        $end_time = date('Y-m-d', time() - 1 * 24 * 60 * 60);

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 10);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $type);

        foreach ($campaigns as $campaign_id) {

            $params = array(
                'nick' => $nick,
                'subway_token' => $subway_token,
                'source' => 'SUMMARY',
                'search_type' => 'SUMMARY',
                'campaign_id' => $campaign_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
            );

            $this->linker->call('taobao.simba.rpt.campaignbase.get', array($this, 'processCampRpt'), $params, $access_token); // 带有 Session 的请求
            $this->linker->call('taobao.simba.rpt.campaigneffect.get', array($this, 'processCampRpt'), $params, $access_token); // 带有 Session 的请求
        }


        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        if ($this->downloadCamprptMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
        } else {
            $this->lockCommand->unlock($lock);
        }

        return $this->downloadCamprptMsg;

    }

    public function processCampRpt(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $nick = $request->nick;
        $campaign_id = $request->campaign_id;
        $start_time = $request->start_time;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadCamprptMsg['flag'] = false;
            $this->downloadCamprptMsg['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];

        $columns = array();
        try {
            if (isset($responseArr['rpt_campaign_base_list'])) {
                $resp = $responseArr['rpt_campaign_base_list'];
                foreach ($resp as $rpt_campaign_base) {
                    $columns[] = array(
                        'nick' => $rpt_campaign_base->nick,
                        'date' => $rpt_campaign_base->date,
                        'campaignid' => $rpt_campaign_base->campaignId,
                        'impressions' => $rpt_campaign_base->impressions,
                        'click' => ($rpt_campaign_base->click),
                        'cost' => ($rpt_campaign_base->cost),
                        'ctr' => $rpt_campaign_base->ctr,
                        'cpc' => $rpt_campaign_base->cpc,
                        'cpm' => $rpt_campaign_base->cpm,
                        'avgPos' => $rpt_campaign_base->avgpos,
                        'source' => $rpt_campaign_base->source,
                        'searchtype' => $rpt_campaign_base->searchtype
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    \application\models\tables\CampaignBase::model()->deleteAll("nick=? and campaignId=? AND date>=? ", array($nick, $campaign_id, $start_time));
                    $batchCommand->insert(\application\models\tables\CampaignBase::model()->tableName(), $columns);
                }
                $this->downloadCamprptMsg['flag'] = true;

            } else if (isset($responseArr['rpt_campaign_effect_list'])) {
                $resp = $responseArr['rpt_campaign_effect_list'];
                foreach ($resp as $rpt_campaign_effect) {
                    $columns[] = array(
                        'nick' => $rpt_campaign_effect->nick,
                        'date' => $rpt_campaign_effect->date,
                        'campaignid' => $rpt_campaign_effect->campaignId,
                        'directpay' => ((int)($rpt_campaign_effect->directpay)),
                        'indirectpay' => ((int)($rpt_campaign_effect->indirectpay)),
                        'directpaycount' => ((int)($rpt_campaign_effect->directpaycount)),
                        'indirectpaycount' => ((int)($rpt_campaign_effect->indirectpaycount)),
                        'favItemCount' => ((int)($rpt_campaign_effect->favItemCount)),
                        'favShopCount' => ((int)($rpt_campaign_effect->favShopCount)),
                        'source' => $rpt_campaign_effect->source,
                        'searchtype' => $rpt_campaign_effect->searchtype
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    \application\models\tables\CampaignEffect::model()->deleteAll("nick=? and campaignId=? AND date>=?", array($nick, $campaign_id, $start_time));
                    $batchCommand->insert(\application\models\tables\CampaignEffect::model()->tableName(), $columns);
                }
                $this->downloadCamprptMsg['flag'] = true;
            } else {
                $this->downloadCamprptMsg['flag'] = false;
                $this->downloadCamprptMsg['msg'] = "没有数据--camprpt";
            }
        } catch (Exception $e) {
            $this->downloadCamprptMsg['flag'] = false;
            $this->downloadCamprptMsg['msg'] = $e->getMessage();
        }

    }

    /**
     * @param $nick
     * @param array $campaigns
     * @param $days
     * @param $access_token
     * @param $subway_token
     * @return array
     * 下载推广计划下推广组报表
     */
    public function actionAdgroupRpt($nick, array $campaigns, $days, $access_token, $subway_token)
    {
        $date = date("Y-m-d");
        $type = 'ADGROUPRPT';

        $start_time = date('Y-m-d', time() - $days * 24 * 60 * 60);
        $end_time = date('Y-m-d', time() - 1 * 24 * 60 * 60);

        $result = $this->lockQuery->actionIsLockOrFinish($nick, $date, $type, 10);
        if (isset($result['isFinish']) && $result['isFinish'] == true)
            return array('flag' => true, 'msg' => '已下载过');
        if (isset($result['isLock']) && $result['isLock'] == true)
            return array('flag' => false, 'msg' => '其他用户在下载中');

        $lock = $this->lockCommand->lock($nick, $date, $type);

        foreach ($campaigns as $campaign_id) {
            $params = array(
                'nick' => $nick,
                'subway_token' => $subway_token,
                'source' => 'SUMMARY',
                'search_type' => 'SUMMARY',
                'campaign_id' => $campaign_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
            );

            $this->linker->call('taobao.simba.rpt.campadgroupbase.get', array($this, 'processAdgroupRpt'), $params, $access_token); // 带有 Session 的请求
            $this->linker->call('taobao.simba.rpt.campadgroupeffect.get', array($this, 'processAdgroupRpt'), $params, $access_token); // 带有 Session 的请求
        }

        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        if ($this->downloadAdgrouprptMsg['flag'] === false) {
            $this->lockCommand->delLock($nick, $date, $type);
        } else {
            $this->lockCommand->unlock($lock);
        }

        return $this->downloadAdgrouprptMsg;

    }

    public function processAdgroupRpt(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $nick = $request->nick;
        $campaign_id = $request->campaign_id;
        $start_time = $request->start_time;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadAdgrouprptMsg['flag'] = false;
            $this->downloadAdgrouprptMsg['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $columns = array();
        try {
            if (isset($responseArr['rpt_campadgroup_base_list'])) {
                $resp = (object)$responseArr['rpt_campadgroup_base_list'];
                foreach ($resp as $rpt_campadgroup_base) {
                    $columns[] = array(
                        'nick' => $rpt_campadgroup_base->nick,
                        'date' => $rpt_campadgroup_base->date,
                        'campaignid' => $rpt_campadgroup_base->campaignId,
                        'adgroupid' => $rpt_campadgroup_base->adgroupId,
                        'impressions' => $rpt_campadgroup_base->impressions,
                        'click' => ($rpt_campadgroup_base->click),
                        'cost' => ($rpt_campadgroup_base->cost),
                        'ctr' => $rpt_campadgroup_base->ctr,
                        'cpc' => $rpt_campadgroup_base->cpc,
                        'cpm' => $rpt_campadgroup_base->cpm,
                        'avgPos' => $rpt_campadgroup_base->avgpos,
                        'source' => $rpt_campadgroup_base->source,
                        'searchtype' => $rpt_campadgroup_base->searchtype,
                        'api_time' => date('Y-m-d H:i:s')
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    \application\models\tables\RptAdgroupBase::model()->deleteAll("nick=? and campaignId=? AND date>=? ", array($nick, $campaign_id, $start_time));
                    $batchCommand->insert(\application\models\tables\RptAdgroupBase::model()->tableName(), $columns);
                }
                $this->downloadAdgrouprptMsg['flag'] = true;

            } else if (isset($responseArr['rpt_campadgroup_effect_list'])) {
                $resp = (object)$responseArr['rpt_campadgroup_effect_list'];
                foreach ($resp as $rpt_campadgroup_effect) {
                    $columns[] = array(
                        'nick' => $rpt_campadgroup_effect->nick,
                        'date' => $rpt_campadgroup_effect->date,
                        'campaignid' => $rpt_campadgroup_effect->campaignId,
                        'adgroupid' => $rpt_campadgroup_effect->adgroupId,
                        'directpay' => ((int)($rpt_campadgroup_effect->directpay)),
                        'indirectpay' => ((int)($rpt_campadgroup_effect->indirectpay)),
                        'directpaycount' => ((int)($rpt_campadgroup_effect->directpaycount)),
                        'indirectpaycount' => ((int)($rpt_campadgroup_effect->indirectpaycount)),
                        'favItemCount' => ((int)($rpt_campadgroup_effect->favItemCount)),
                        'favShopCount' => ((int)($rpt_campadgroup_effect->favShopCount)),
                        'source' => $rpt_campadgroup_effect->source,
                        'searchtype' => $rpt_campadgroup_effect->searchtype,
                        'api_time' => date('Y-m-d H:i:s')
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    \application\models\tables\RptAdGroupEffect::model()->deleteAll("nick=? and campaignId=? AND date>=?", array($nick, $campaign_id, $start_time));
                    $batchCommand->insert(\application\models\tables\RptAdGroupEffect::model()->tableName(), $columns);
                }
                $this->downloadAdgrouprptMsg['flag'] = true;
            } else {
                $this->downloadAdgrouprptMsg['flag'] = false;
                $this->downloadAdgrouprptMsg['msg'] = "没有数据--adgrouprpt";
            }
        } catch (Exception $e) {
            $this->downloadAdgrouprptMsg['flag'] = false;
            $this->downloadAdgrouprptMsg['msg'] = $e->getMessage();
        }

    }

    public function actionRefreshKeywordRpt($nick, array $adgroups, $days, $access_token, $subway_token)
    {
        $start_time = date('Y-m-d', time() - $days * 24 * 60 * 60);
        $end_time = date('Y-m-d', time() - 1 * 24 * 60 * 60);

        foreach ($adgroups as $adgroup) {
            $adgroup = (object)$adgroup;
            $params = array(
                'nick' => $nick,
                'subway_token' => $subway_token,
                'source' => 'SUMMARY',
                'search_type' => 'SUMMARY',
                'campaign_id' => $adgroup->campaign_id,
                'adgroup_id' => $adgroup->adgroup_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'page_no' => 1,
                'page_size' => 4000
            );

            $this->linker->call('taobao.simba.rpt.adgroupkeywordbase.get', array($this, 'processKeywordRpt'), $params, $access_token); // 带有 Session 的请求
            $this->linker->call('taobao.simba.rpt.adgroupkeywordeffect.get', array($this, 'processKeywordRpt'), $params, $access_token); // 带有 Session 的请求
        }

        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadKeywordrptMsg;

    }

    public function processKeywordRpt(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $nick = $request->nick;
        $campaign_id = $request->campaign_id;
        $adgroup_id = $request->adgroup_id;
        $start_time = $request->start_time;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadKeywordrptMsg['flag'] = false;
            $this->downloadKeywordrptMsg['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $columns = array();
        try {
            if (isset($responseArr['rpt_adgroupkeyword_base_list'])) {
                $resp = (object)$responseArr['rpt_adgroupkeyword_base_list'];
                foreach ($resp as $rpt_adgroupkeyword_base) {
                    $columns[] = array(
                        'nick' => $rpt_adgroupkeyword_base->nick,
                        'date' => $rpt_adgroupkeyword_base->date,
                        'campaignid' => $rpt_adgroupkeyword_base->campaignid,
                        'adgroupid' => $rpt_adgroupkeyword_base->adgroupid,
                        'keywordid' => $rpt_adgroupkeyword_base->keywordid,
                        'keywordstr' => $rpt_adgroupkeyword_base->keywordstr,
                        'impressions' => $rpt_adgroupkeyword_base->impressions,
                        'click' => ($rpt_adgroupkeyword_base->click),
                        'cost' => ($rpt_adgroupkeyword_base->cost),
                        'ctr' => $rpt_adgroupkeyword_base->ctr,
                        'cpc' => $rpt_adgroupkeyword_base->cpc,
                        'cpm' => $rpt_adgroupkeyword_base->cpm,
                        'avgPos' => $rpt_adgroupkeyword_base->avgpos,
                        'source' => $rpt_adgroupkeyword_base->source,
                        'searchtype' => $rpt_adgroupkeyword_base->searchtype,
                        'api_time' => date('Y-m-d H:i:s')
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    \application\models\tables\RptKeywordBase::model()->deleteAll("nick=? and campaignId=? and adgroupId=? AND date>=? ", array($nick, $campaign_id, $adgroup_id, $start_time));
                    $batchCommand->insert(\application\models\tables\RptKeywordBase::model()->tableName(), $columns);
                }
                $this->downloadKeywordrptMsg['flag'] = true;

            } else if (isset($responseArr['rpt_adgroupkeyword_effect_list'])) {
                $resp = (object)$responseArr['rpt_adgroupkeyword_effect_list'];
                foreach ($resp as $rpt_campadgroup_effect) {
                    $columns[] = array(
                        'nick' => $rpt_campadgroup_effect->nick,
                        'date' => $rpt_campadgroup_effect->date,
                        'campaignid' => $rpt_campadgroup_effect->campaignid,
                        'adgroupid' => $rpt_campadgroup_effect->adgroupid,
                        'keywordid' => $rpt_campadgroup_effect->keywordid,
                        'keywordstr' => $rpt_campadgroup_effect->keywordstr,
                        'directpay' => ((int)($rpt_campadgroup_effect->directpay)),
                        'indirectpay' => ((int)($rpt_campadgroup_effect->indirectpay)),
                        'directpaycount' => ((int)($rpt_campadgroup_effect->directpaycount)),
                        'indirectpaycount' => ((int)($rpt_campadgroup_effect->indirectpaycount)),
                        'favItemCount' => ((int)($rpt_campadgroup_effect->favItemCount)),
                        'favShopCount' => ((int)($rpt_campadgroup_effect->favShopCount)),
                        'source' => $rpt_campadgroup_effect->source,
                        'searchtype' => $rpt_campadgroup_effect->searchtype,
                        'api_time' => date('Y-m-d H:i:s')
                    );
                }
                if (!empty($columns)) {
                    $batchCommand = new CDbBatchCommand(Yii::app()->db);
                    \application\models\tables\RptKeywordEffect::model()->deleteAll("nick=? and campaignId=? and adgroupId = ? AND date>=?", array($nick, $campaign_id, $adgroup_id, $start_time));
                    $batchCommand->insert(\application\models\tables\RptKeywordEffect::model()->tableName(), $columns);
                }
                $this->downloadKeywordrptMsg['flag'] = true;
            } else {
                $this->downloadKeywordrptMsg['flag'] = false;
                $this->downloadKeywordrptMsg['msg'] = "没有数据--keywordrpt";
            }
        } catch (Exception $e) {
            $this->downloadKeywordrptMsg['flag'] = false;
            $this->downloadKeywordrptMsg['msg'] = $e->getMessage();
        }

    }

    /*
     * 批量删除推广组
     */

    public function actionDelAdgroups($nick, array $adgroups, $access_token)
    {
        foreach ($adgroups as $adgroup) {
            $params = array(
                'nick' => $nick,
                'adgroup_id' => $adgroup,
            );

            $this->linker->call('taobao.simba.adgroup.delete', array($this, 'processDelAdgroup'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadDelAdgroupMsg;

    }

    public function processDelAdgroup(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $adgroup_id = $request->adgroup_id;
        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadDelAdgroupMsg[$adgroup_id]['flag'] = false;
            $this->downloadDelAdgroupMsg[$adgroup_id]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];

        $adgroup = $responseArr['adgroup'];
        $nick = $adgroup['nick'];
        $adgroup_id = $adgroup['adgroup_id'];

        try {
            AdGroup::model()->deleteAll("nick=? and adgroup_id=?", array($nick, $adgroup_id));
            $this->downloadDelAdgroupMsg[$adgroup_id]['flag'] = true;
        } catch (Exception $e) {
            $this->downloadDelAdgroupMsg[$adgroup_id]['flag'] = false;
            $this->downloadDelAdgroupMsg[$adgroup_id]['msg'] = $e->getMessage();
        }
    }

    /*
     * 批量添加推广组
     */

    public function actionAddAdgroups($nick, array $addItems, $access_token)
    {
        foreach ($addItems as $item) {

            $item = (object)$item;

            $params = array(
                'nick' => $nick,
                'campaign_id' => $item->campaign_id,
                'item_id' => $item->item_id,
                'default_price' => $item->default_price,
                'title' => $item->title,
                'img_url' => $item->img_url,
            );

            $this->linker->call('taobao.simba.adgroup.add', array($this, 'processAddAdgroup'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadAddAdgroupMsg;

    }

    public function processAddAdgroup(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $num_iid = $request->item_id;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadAddAdgroupMsg[$num_iid]['flag'] = false;
            $this->downloadAddAdgroupMsg[$num_iid]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];

        $adgroup = $responseArr['adgroup'];
        $adgroup = (object)$adgroup;
        $model = new AdGroup();
        $model->nick = $adgroup->nick;
        $model->campaign_id = $adgroup->campaign_id;
        $model->adgroup_id = $adgroup->adgroup_id;
        $model->category_ids = $adgroup->category_ids;
        $model->num_iid = $adgroup->num_iid;
        $model->default_price = $adgroup->default_price;
        $model->nonsearch_max_price = $adgroup->nonsearch_max_price;
        $model->is_nonsearch_default_price = $adgroup->is_nonsearch_default_price ? 1 : 0;
        $model->online_status = $adgroup->online_status;
        $model->offline_type = $adgroup->offline_type;
        $model->reason = $adgroup->reason;
        $model->create_time = date("Y-m-d H:i:s");
        $model->modified_time = $adgroup->modified_time;
        $model->api_time = date("Y-m-d H:i:s");
        try {
            $model->save();
            $this->downloadAddAdgroupMsg[$num_iid]['flag'] = true;
            $this->downloadAddAdgroupMsg[$num_iid]['adgroup_id'] = $adgroup->adgroup_id;
        } catch (Exception $e) {
            $this->downloadAddAdgroupMsg[$num_iid]['flag'] = false;
            $this->downloadAddAdgroupMsg[$num_iid]['msg'] = $e->getMessage();
        }
    }

    /*
     * 批量添加副标题！
     */

    public function actionAddCreatives($nick, array $addCreatives, $access_token)
    {
        foreach ($addCreatives as $creative) {
            $creative = (object)$creative;
            $params = array(
                'nick' => $nick,
                'adgroup_id' => $creative->adgroup_id,
                'title' => $creative->title,
                'img_url' => $creative->img_url,
            );

            $this->linker->call('taobao.simba.creative.add', array($this, 'processAddCreative'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadAddCreativeMsg;

    }

    public function processAddCreative(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $adgroup_id = $request->adgroup_id;

        $retArr = $this->processCommon($response);

        if ($retArr['flag'] == false) {
            $this->downloadAddCreativeMsg[$adgroup_id]['flag'] = false;
            $this->downloadAddCreativeMsg[$adgroup_id]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];

        try {
            $this->downloadAddCreativeMsg[$adgroup_id]['flag'] = true;
        } catch (Exception $e) {
            $this->downloadAddCreativeMsg[$adgroup_id]['flag'] = false;
            $this->downloadAddCreativeMsg[$adgroup_id]['msg'] = $e->getMessage();
        }
    }

    /*
     * 批量修改推广计划标题和推广状态
     */

    public function actionUpdateCampaigns($nick, array $updateCampaigns, $access_token)
    {
        foreach ($updateCampaigns as $campaign) {
            $campaign = (object)$campaign;
            $params = array(
                'nick' => $nick,
                'campaign_id' => $campaign->campaign_id,
                'title' => $campaign->title,
                'online_status' => $campaign->online_status,
            );
            $this->linker->call('taobao.simba.campaign.update', array($this, 'processUpdateCampaign'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadUpdateCampaignMsg;

    }

    public function processUpdateCampaign(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $campaign_id = $request->campaign_id;
        $nick = $request->nick;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->downloadUpdateCampaignMsg[$campaign_id]['flag'] = false;
            $this->downloadUpdateCampaignMsg[$campaign_id]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $responseArr = $responseArr['campaign'];
        $model = Campaign::model()->find("nick=? and campaign_id=?", array($nick, $campaign_id));
        if ($model != null) {
            $model->title = $responseArr['title'];
            $model->online_status = $responseArr['online_status'];
            $model->modified_time = $responseArr['modified_time'];
            $model->api_time = date('Y-m-d H:i:s');

            if ($model->save()) {
                $this->downloadUpdateCampaignMsg[$campaign_id]['flag'] = true;
            } else {
                $this->downloadUpdateCampaignMsg[$campaign_id]['flag'] = false;
                $this->downloadUpdateCampaignMsg[$campaign_id]['msg'] = $model->getErrors();
            }
        }
    }


    /*
    * 批量修改推广组信息
    */

    public function actionUpdateAdgroups($nick, array $updateAdgroups, $access_token)
    {
        foreach ($updateAdgroups as $adgroup) {
            $adgroup = (object)$adgroup;
            $params = array(
                'nick' => $nick,
                'adgroup_id' => $adgroup->adgroup_id,
            );

            if (isset($adgroup->default_price))
                $params['default_price'] = $adgroup->default_price;
            if (isset($adgroup->online_status))
                $params['online_status'] = $adgroup->online_status;
            if (isset($adgroup->use_nonsearch_default_price))
                $params['use_nonsearch_default_price'] = $adgroup->use_nonsearch_default_price;
            if (isset($adgroup->nonsearch_max_price))
                $params['nonsearch_max_price'] = $adgroup->nonsearch_max_price;

            $this->linker->call('taobao.simba.adgroup.update', array($this, 'processUpdateAdgroup'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadUpdateAdgroupMsg;

    }

    public function processUpdateAdgroup(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $adgroup_id = $request->adgroup_id;
        $nick = $request->nick;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->downloadUpdateAdgroupMsg[$adgroup_id]['flag'] = false;
            $this->downloadUpdateAdgroupMsg[$adgroup_id]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $responseArr = $responseArr['adgroup'];
        $model = AdGroup::model()->find("nick=? and adgroup_id=?", array($nick, $adgroup_id));
        if ($model != null) {
            $model->default_price = $responseArr['default_price'];
            $model->online_status = $responseArr['online_status'];
            $model->modified_time = date('Y-m-d H:i:s');
            $model->api_time = date('Y-m-d H:i:s');

            if ($model->save()) {
                $this->downloadUpdateAdgroupMsg[$adgroup_id]['flag'] = true;
            } else {
                $this->downloadUpdateAdgroupMsg[$adgroup_id]['flag'] = false;
                $this->downloadUpdateAdgroupMsg[$adgroup_id]['msg'] = $model->getErrors();
            }
        }
    }

    /*
    * 批量刷新关键词
    */

    public function actionRefreshKeyword($nick, array $adgroupIds, $access_token)
    {
        foreach ($adgroupIds as $adgroup) {
            $adgroup = (object)$adgroup;
            $params = array(
                'nick' => $nick,
                'adgroup_id' => $adgroup->adgroup_id,
            );

            $this->linker->call('taobao.simba.keywordsbyadgroupid.get', array($this, 'processRefreshKeyword'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadKeywordMsg;

    }

    public function processRefreshKeyword(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $adgroup_id = $request->adgroup_id;
        $nick = $request->nick;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->downloadKeywordMsg[$adgroup_id]['flag'] = false;
            $this->downloadKeywordMsg[$adgroup_id]['msg'] = $retArr['msg'];
            return;
        }
        $resp = $retArr['response'];
        $resp = (object)$resp;
        $resp = $resp->keywords;
        $resp = empty($resp->keyword) ? array() : $resp->keyword;
        $columns = array();
        foreach ($resp as $keyword) {
            $columns[] = array('adgroup_id' => $keyword->adgroup_id,
                'audit_status' => $keyword->audit_status,
                'audit_desc' => $keyword->audit_desc,
                'campaign_id' => $keyword->campaign_id,
                'create_time' => $keyword->create_time,
                'is_default_price' => $keyword->is_default_price == 'true' ? 1 : 0,
                'is_garbage' => $keyword->is_garbage == 'true' ? 1 : 0,
                'keyword_id' => $keyword->keyword_id,
                'modified_time' => $keyword->modified_time,
                'max_price' => $keyword->max_price,
                'nick' => $keyword->nick,
                'qscore' => $keyword->qscore,
                'word' => $keyword->word,
                'match_scope' => $keyword->match_scope,
                'api_time' => date('Y-m-d H:i:s'));
        }
        try {
            Keyword::model()->deleteAll("nick=? and adgroup_id=?", array($nick, $adgroup_id));
            if (!empty($columns)) {
                $batchCommand = new CDbBatchCommand(Yii::app()->db);
                $batchCommand->insert('ztc0001', $columns);
            }
            $this->downloadKeywordMsg[$adgroup_id]['flag'] = true;
        } catch (\Exception $e) {
            $this->downloadKeywordMsg[$adgroup_id]['flag'] = false;
            $this->downloadKeywordMsg[$adgroup_id]['msg'] = $e->getMessage();
        }
    }


    /*
    * 删除一批关键词
    */

    public function actionDeleteKeywords($nick, $campaign_id, $keyword_ids, $access_token)
    {

        $params = array(
            'nick' => $nick,
            'campaign_id' => $campaign_id,
            'keyword_ids' => $keyword_ids,
        );

        $this->linker->call('taobao.simba.keywords.delete', array($this, 'processDeleteKeywords'), $params, $access_token); // 带有 Session 的请求
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->deleteKeywordsMsg;

    }

    public function processDeleteKeywords(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $nick = $request->nick;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->deleteKeywordsMsg[$nick]['flag'] = false;
            $this->deleteKeywordsMsg[$nick]['msg'] = $retArr['msg'];
            return;
        }
        $resp = $retArr['response'];
        $resp = (object)$resp;
        $resp = $resp->keywords;
        $this->deleteKeywordsMsg[$nick]['flag'] = true;
        $this->deleteKeywordsMsg[$nick]['keywords'] = $resp;
    }

    /*
    * 批量取得推广计划日限额
    */

    public function actionGetCampaignsBudget($nick, array $campaignIds, $access_token)
    {
        foreach ($campaignIds as $id) {
            $params = array(
                'nick' => $nick,
                'campaign_id' => $id,
            );
            $this->linker->call('taobao.simba.campaign.budget.get', array($this, 'processGetBudget'), $params, $access_token); // 带有 Session 的请求
        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->downloadBudgetCampaignMsg;
    }

    public function processGetBudget(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $campaign_id = $request->campaign_id;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->downloadBudgetCampaignMsg[$campaign_id]['flag'] = false;
            $this->downloadBudgetCampaignMsg[$campaign_id]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $responseArr = $responseArr['campaign_budget'];
        $this->downloadBudgetCampaignMsg[$campaign_id]['flag'] = true;
        $this->downloadBudgetCampaignMsg[$campaign_id]['budget'] = $responseArr['budget'];
        $this->downloadBudgetCampaignMsg[$campaign_id]['is_smooth'] = $responseArr['is_smooth'];
    }


    /*
   * 批量修改推广计划日限额
   */

    public function actionUpdateCampaignsBudget($nick, array $budgetCampaigns, $access_token)
    {
        foreach ($budgetCampaigns as $campaign) {
            $campaign = (object)$campaign;
            $params = array(
                'nick' => $nick,
                'campaign_id' => $campaign->campaign_id,
                'budget' => $campaign->budget,
                'use_smooth' => $campaign->use_smooth,
            );

            $this->linker->call('taobao.simba.campaign.budget.update', array($this, 'processUpdateBudget'), $params, $access_token); // 带有 Session 的请求

        }
        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->updateBudgetCampaignMsg;

    }

    public function processUpdateBudget(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $campaign_id = $request->campaign_id;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->updateBudgetCampaignMsg[$campaign_id]['flag'] = false;
            $this->updateBudgetCampaignMsg[$campaign_id]['msg'] = $retArr['msg'];
            return;
        }
        $responseArr = $retArr['response'];
        $responseArr = $responseArr['campaign_budget'];
        $this->updateBudgetCampaignMsg[$campaign_id]['flag'] = true;
        $this->updateBudgetCampaignMsg[$campaign_id]['budget'] = $responseArr['budget'];
        $this->updateBudgetCampaignMsg[$campaign_id]['is_smooth'] = $responseArr['is_smooth'];

    }

    /*
   * 添加推广组定向投放位置
   */

    public function actionNonsearchPlacesAdd($nick, $campaign_id, $adgroupPlacesJson, $access_token)
    {
        $params = array(
            'nick' => $nick,
            'campaign_id' => $campaign_id,
            'adgroup_places_json' => $adgroupPlacesJson,
        );
        $this->linker->call('taobao.simba.nonsearch.adgroupplaces.add', array($this, 'processNonsearchPlacesAdd'), $params, $access_token); // 带有 Session 的请求

        $this->linker->finish(); // 等待所有异步请求完成，防止请求全部完成前 PHP 提前结束

        return $this->nonsearchPlacesAddMsg;

    }

    public function processNonsearchPlacesAdd(TopLinker_Request_Abstract $request, TopLinker_Response_Abstract $response)
    {
        $campaign_id = $request->campaign_id;

        $retArr = $this->processCommon($response);
        if ($retArr['flag'] == false) {
            $this->nonsearchPlacesAddMsg[$campaign_id]['flag'] = false;
            $this->nonsearchPlacesAddMsg[$campaign_id]['msg'] = $retArr['msg'];
            return;
        }
        //$responseArr = $retArr['response'];
        //$responseArr = $responseArr['campaign_budget'];
        $this->nonsearchPlacesAddMsg[$campaign_id]['flag'] = true;
    }

    public function actionGetCredit($accessToken)
    {
        $result = array();
        $params = array(
            'fields' => 'nick,seller_credit,type,status',
        );

        $response = $this->linker->load('taobao.user.seller.get', $params, $accessToken);
        if (!$response->hasError()) {
            $result['level'] = $response->user->seller_credit->level;
            $result['type'] = $response->user->type;
        }
        return $result;
    }

    public function getCredit($nick, $appKey, $appSecret, $access_token)
    {
        $req = new \UserSellerGetRequest();
        //$req->setNick($nick);
        $req->setFields("nick,seller_credit,type,status");
        $c = new \TopClient();
        $c->appkey = $appKey;
        $c->secretKey = $appSecret;
        $resp = $c->execute($req, $access_token);
        print_r($resp);

    }

    public function getBalance($nick, $appKey, $appSecret, $access_token)
    {
        $req = new \SimbaAccountBalanceGetRequest();
        $req->setNick($nick);
        $c = new \TopClient();
        $c->appkey = $appKey;
        $c->secretKey = $appSecret;
        $resp = $c->execute($req, $access_token);
        if (isset($resp->code)) {
            if ($resp->msg == "App Call Limited") {
                sleep(1);
                $resp = $c->execute($req, $access_token);
            }
        }
        if (!isset($resp->code))
            return $resp->balance;
        else
            return '-';

    }

    protected function getTopClient()
    {
        $c = new TopClient();
        $c->appkey = Yii::app()->params['appKey'];
        $c->secretKey = Yii::app()->params['appSecret'];
        return $c;
    }


}