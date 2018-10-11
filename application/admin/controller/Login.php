<?php

namespace app\admin\controller;

use app\admin\common\Base;
use app\admin\model\Admin;
use think\Request;

class Login extends Base
{
    /**
     * 登录界面
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch('login');
    }

    /**
     * 检查权限.
     *
     * @return \think\Response
     */
    public function check(Request $request)
    {

        $status = 0;
        $data = $request->param();

        $username = $data['username'];
        $password = md5($data['password']);

        $map = ['username' => $username];
        $admin = Admin::get($map);

        if(is_null($admin)) {
            $message = "用户名不正确!";
        }elseif ($admin->password != $password) {
            $message = "密码不正确!";
        }else {
            $admin->setInc('login_count');//setInc字段自动加1
            $admin->save(['last_time' => time()]);
            session('user_id', $username);
            session('user_info', $data);

            $status = 1;
            $message = "验证通过！请点击确定进入后台";
        }
        return ["status" => $status, "message" => $message];
    }

    /**
     * 退出登录
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function logout()
    {
        session('user_id', null);
        session('user_info', null);
        $this->success('注销成功，正在返回...','login/index');
    }

   
}
