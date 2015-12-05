<?php

namespace Admin\Controller;

use Common\Controller\AuthController;

class AccessTraceController extends AuthController {
	public function getCounterList() {
		$m = M ( "AccessTraceCounter" );
		
		$startDate = $_POST ["start_date"];
		$endDate = $_POST ["end_date"];
		$where = null;
		if ($startDate != null) {
			$where = "date(thedate) >= date('" . $startDate . "')";
			$this->assign ( "startDate", $startDate );
		}
		if ($endDate != null) {
			if ($where == null) {
				$where = "date(thedate) <= date('" . $endDate . "')";
			} else {
				$where = $where . " and date(thedate) <= date('" . $endDate . "')";
			}
			$this->assign ( "endDate", $endDate );
		}
		
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$msglist = $m->where ( $where )->order ( "thedate desc" )->limit ( $limit )->select ();
		$this->assign ( "list", $msglist );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->display ();
	}
	public function getDetailsList() {
		$m = M ( "AccessTraceDetails" );
		
		$startDate = $_POST ["start_date"];
		$endDate = $_POST ["end_date"];
		$where = null;
		if ($startDate != null) {
			$where = "date(thedate) >= date('" . $startDate . "')";
			$this->assign ( "startDate", $startDate );
		}
		if ($endDate != null) {
			if ($where == null) {
				$where = "date(thedate) <= date('" . $endDate . "')";
			} else {
				$where = $where . " and date(thedate) <= date('" . $endDate . "')";
			}
			$this->assign ( "endDate", $endDate );
		}
		
		$Page = getpage ( $m, $where, C ( "LIST_PAGE_SIZE" ) );
		
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$msglist = $m->where ( $where )->order ( "thedate desc" )->limit ( $limit )->select ();
		$this->assign ( "list", $msglist );
		
		$show = $Page->show ();
		$this->assign ( "page", $show );
		$this->display ();
	}
}