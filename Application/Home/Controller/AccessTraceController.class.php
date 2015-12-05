<?php

namespace Home\Controller;

use Think\Controller;
use Admin\Controller\RichEditController;

class AccessTraceController extends Controller {
	public function trace($url) {
		// session ( "access_trace_data", null);
		$ses = session ( "access_trace_data" );
		
		if ($ses == null) { // 用户首次登录
			$data ["session_id"] = session_id ();
			$data ["thedate"] = date ( "Y-m-d H:i:s", time () );
			$data ["url"] = $url;
			
			if (isMobile ()) {
				$data ["location"] = "手机";
			} else {
				$ipLocation = new \Org\Net\IpLocation ( 'QQWry.dat' ); // 实例化类 参数表示IP地址库文件
				$ip = get_client_ip ();
				$data ["ip"] = $ip;
				$area = $ipLocation->getlocation ( $ip ); // '203.34.5.66'); // 获取某个IP地址所在的位置
				$author_location = iconv ( 'gbk', 'utf-8', $area ['country'] . $area ['area'] );
				$data ["location"] = $author_location;
			}
			session ( "access_trace_data", $data ); // 设置用户已登录
			
			self::updateCounter($data);
			
		} else {
			// 检查是否已经是下一天
			$today = date ( "Y-m-d", time () );
			$data = session ( "access_trace_data" );
			if ($today > substr ( $data ["thedate"], 0, 10 )) {
				$data = session ( "access_trace_data" );
				$data ["thedate"] = date ( "Y-m-d H:i:s", time () );
				
				self::updateCounter($data);
				
			}
		}
		
		$counter = session ( "access_trace_counter" );
		$m = M ( "AccessTraceCounter" );
		// 网站发布总天数
		$totalNo = $m->query ( "select count(distinct(date(thedate))) as c from think_access_trace_counter" );
		$totalDay = intval($totalNo [0] ["c"]) - 1;
		
		// 日均访问量
		$counter ["avgNo"] = (int)(intval($counter ["totalNo"]) / $totalDay);
		
		// 最高访问量
		$totalNo = $m->query ( "select count(*) as c from think_access_trace_counter group by date(thedate) order by c desc" );
		$counter ["maxNo"] = $totalNo [0] ["c"];
		
		$data = session ( "access_trace_data" );
		$data ["thedate"] = date ( "Y-m-d H:i:s", time () );
		$data ["url"] = $url;
		
		// 记录访问详细信息
		$m = M ( "AccessTraceDetails" );
		$m->add ( $data );
		
		$this->ajaxReturn ( $counter );
	}
	
	private function updateCounter($data) {
		$m = M ( "AccessTraceCounter" );
		$m->add ( $data );
		
		// 当日访问数
		$todayNo = $m->query ( "select count(*) as c from think_access_trace_counter where date(thedate) = current_date()" );
		$counter ["todayNo"] = $todayNo [0] ["c"];
		
		// 访问总数
		$totalNo = $m->query ( "select count(*) as c from think_access_trace_counter" );
		$counter ["totalNo"] = $totalNo [0] ["c"];
					
		session ( "access_trace_counter", $counter );
	}
	
	// SELECT date(thedate), count(thedate) FROM `think_access_trace_counter` group by date(thedate)
	// SELECT year(thedate),month(thedate), count(*) FROM `think_access_trace_counter` group by year(thedate), month(thedate)
}