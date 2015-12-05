<?php
namespace Common\Controller;
use Think\Controller;
//use Think\Auth;

class AuthController extends Controller {
	protected function _initialize() {
		// 获取当前用户ID 
        $userAuth = session("user_auth");
		if ($userAuth == false) {
			$this->error("您尚未登录！正在跳转到登录页面", U ("Login/index"));
		}

        return true;

        //$uid = $userAuth["uid"];
        // 是否是超级管理员，UID == 1的就是管理员
        //if(($uid == 1)){
        //    return true;//管理员允许访问任何页面
        //} else {
        //    $this->error("没有权限访问本页面!", U("Login/index"));
        //}
        //  检测当前页是否有权限访问      // ' admin/index/index'
        // $rule = strtolower(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME);
 
        // static $auth = null;
        // if (!$auth) {
        //     $auth = new \Think\Auth();
        // }

        // if(!$auth->check($rule, $uid, array("in","1,2"), $mode="url")) {
        //     $this->error("没有权限访问本页面!", U("Public/logout"));
        // }
	}
}