<?php
namespace Admin\Controller;
use Common\Controller\AuthController;

// 继承了AuthController，需要登陆才能访问
class IndexController extends AuthController {	
    public function admin(){
        $this->display();
    }
}