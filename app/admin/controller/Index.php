<?php
namespace app\admin\controller;

use app\admin\library\Controller;
use app\admin\library\User;
use app\admin\model\HandleLog;
use think\Exception;

class Index extends Controller
{
    /**
     * 登录
     * @method   login
     * @DateTime 2017-03-31T11:45:13+0800
     * @return   [type]                   [description]
     */
    public function login()
    {
        if ($this->request->isAjax()) {

            try {
                // 验证请求的数据
                $this->validate([], 'login');
                // 执行登录
                User::instance()->login();

            } catch (Exception $e) {
                $this->error($e->getMessage());
            }

            $this->success('用户登录', 'index/index');
        }
        $this->view->engine->layout(true);
        return $this->fetch();
    }
    /**
     * 修改个人资料
     * @method   personal
     * @DateTime 2017-04-08T10:05:28+0800
     * @return   [type]                   [description]
     */
    public function personal()
    {
        if ($this->request->isAjax()) {
            try {
                $this->save($this->userLibrary->getUser(), [], 'personal');
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            session(null);
            $this->success('个人资料修改', 'index/login');
        }
        return $this->fetch();
    }
    /**
     * 退出
     * @method   logout
     * @DateTime 2017-04-06T16:30:27+0800
     * @return   [type]                   [description]
     */
    public function logout()
    {
        session(null);
        $this->success('退出登录');
    }
    /**
     * 系统框架
     * @method   index
     * @DateTime 2017-03-31T13:36:08+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        $this->view->engine->layout(true);
        $leftmenu = User::instance()->getMenu();
        $this->assign('list', toTree($leftmenu));
        return $this->fetch();
    }
    /**
     * [log description]
     * @method   log
     * @DateTime 2017-04-12T17:33:19+0800
     * @return   [type]                   [description]
     */
    public function log()
    {
        if ($this->request->isAjax()) {
            $list = HandleLog::scope('list')->paginate();
            $this->result($list->toArray(), 1);
        }
        return $this->fetch();
    }
    /**
     * 首页面板
     * @method   main
     * @DateTime 2017-03-31T15:00:46+0800
     * @return   [type]                   [description]
     */
    public function main()
    {

    }
}
