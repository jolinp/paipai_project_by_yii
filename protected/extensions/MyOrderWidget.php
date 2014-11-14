<?php 
class MyOrderWidget extends CWidget
{
	public $type=1;	
	
	public function run()
	{
		$user = Yii::app()->session->get('ApiLoginUser');
		if(empty($user))
		{
			return false;
		}
		
		$nick = $user->taobao_user_nick;
		if(empty($nick))
		{
			return false;
		}
        $html = null;
		$html = Yii::app()->session->get("__vas_order_html_".$nick);
		if($html!==null)
		{
			echo $html;
			return;
		}
        $vas_item_code = null;
		//$vas_item_code = Yii::app()->session->get("__vas_item_code_".$nick);
		if($vas_item_code === null)
		{
			$c = new TopClient();
			$c->appkey = Yii::app()->params['appKey'];
			$c->secretKey = Yii::app()->params['appSecret'];
			
			
			$req = new VasSubscribeGetRequest();
			$req->setNick($nick);
			$req->setArticleCode("ts-1808369");
			$resp = $c->execute($req);
			
			if(!isset($resp->code)){			
				$resp = $resp->article_user_subscribes;
				$resp = (array)$resp->article_user_subscribe;
				$vas_item_code = null;
				$deadline = null;
				foreach ($resp as $vas){
					
					if($vas_item_code === null)
					{
						$vas_item_code = $vas->item_code;
						$deadline = $vas->deadline;
					}else 
					{
						if(strtotime($vas->deadline)>=strtotime($deadline))
						{
							$vas_item_code = $vas->item_code;
							$deadline = $vas->deadline;
						}
						
					}
				}
				if($vas_item_code!==null){
					Yii::app()->session->add("__vas_item_code_".$nick,$vas_item_code);
				}
                if($deadline === null){
                    $date_2=date("Y-m-d");
                }
                else{
				    $date_2=$deadline;
                }

				$date_1=date("Y-m-d");
				$date1_arr=explode("-",$date_1);
				$date2_arr=explode("-",$date_2);
				$day1=mktime(0,0,0,$date1_arr[1],$date1_arr[2],$date1_arr[0]);
				$day2=mktime(0,0,0,$date2_arr[1],$date2_arr[2],$date2_arr[0]);
				$day=round(($day2 - $day1)/3600/24);
				$html = "剩余天数:<span style='color:red'>".$day."天</span>";
			}
		}
		
		$params = array(
			"ts-1808369-1"=>'{"param":{"aCode":"ACT_750452133_130228182313","itemList":["ts-1808369-1"],"promIds":[10034666],"type":2},"sign":"32760160BDD46BE63CA3AC615B12D84D"}',
			"ts-1808369-2"=>'{"param":{"aCode":"ACT_750452133_130228182313","itemList":["ts-1808369-2"],"promIds":[10034664],"type":2},"sign":"32126641786BA13AAB311B0BCB313D92"}',
			"ts-1808369-3"=>'{"param":{"aCode":"ACT_750452133_130228182313","itemList":["ts-1808369-3"],"promIds":[10034665],"type":2},"sign":"476C659A7271A5EBAE114F20415345E2"}',
            "ts-1808369-v4"=>'{"param":{"aCode":"ACT_750452133_130516142319","itemList":["ts-1808369-v4"],"promIds":[10058173],"type":2},"sign":"19FAE435C875DBF1450371F2DD8A794B"}',
		);
		
		$req = new FuwuSaleLinkGenRequest();
		$req->setNick($nick);
		$req->setParamStr($params[$vas_item_code]);
		
		$c = new TopClient();
		$c->appkey = Yii::app()->params['appKey'];
		$c->secretKey = Yii::app()->params['appSecret'];
		$resp = $c->execute($req);
			
		
		$html.="(<a href='{$resp->url}' target='_blank' id='rebuy'>续订优惠</a>)";
		Yii::app()->session->add("__vas_order_html_".$nick,$html);
		echo $html;
	}
}
