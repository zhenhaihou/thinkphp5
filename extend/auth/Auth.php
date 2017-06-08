<?php
namespace Qsnh\think\Auth;

use think\Cookie;
use think\Config;
use think\helper\Hash;

class Auth
{

    /**
     * 当前登录的用户
     * @var object|array
     */
    protected static $user = null;

    /**
     * 基本配置信息
     * @var array
     */
    protected static $_config = null;

    /**
     * 获取配置信息
     * @param $key string 需要获取的键
     * @return void
     */
    protected static function getConfig($key, $default = '')
    {
        if (is_null(self::$_config)) {
            if (!Config::has('auth')) {
                throw new \Exception('no config information.');
            }

            self::$_config = Config::get('auth');
        }

        if (!isset(self::$_config[$key])) {
            return $default;
        }

        return self::$_config[$key];
    }

    /**
     * 检测当前用户是否已经的登录到应用
     * @return boolean
     */
    public static function check()
    {
        if (is_null(self::$user)) {
            $credentials = Cookie::get(self::getCookieName());

            if (is_null($credentials)) {
                return false;
            }

            if (!isset($credentials['id']) || !isset($credentials['sign'])) {
                return false;
            }

            $user = self::find(['id' => $credentials['id']]);

            if ($user === false) {
                return false;
            }

            if (!self::checkUserSign($user, $credentials['sign'])) {
                return false;
            }

            return self::login($user);
        }

        return true;
    }

    /**
     * 获取当前登录用户的ID
     * @return init
     */
    public static function id()
    {
        return self::$user->id;
    }

    /**
     * 获取当前登录用户的信息
     * @return mined array|object
     */
    public static function user()
    {
        return self::$user;
    }

    /**
     * 通过给定$data尝试登录
     * @param $data array 用户提交信息
     * @param $remember boolean 是否记住用户
     * @return mixed
     */
    public static function attempt(array $data, $remember = false)
    {
        $user = self::find($data);

        if ($user === false) {
            return false;
        }

        return self::loginSuccess($user, $remember);
    }

    /**
     * 通过用户模型登录
     * @param think\Model $user
     * @return mixed
     */
    public static function login($user)
    {
        return self::loginSuccess($user);
    }

    /**
     * 通过用户ID登录到应用上
     * @param $id integer 用户ID
     * @return mixed
     */
    public static function loginByUserId($id)
    {
        $user = self::find(['id' => $id]);

        if ($user === false) {
            return false;
        }

        return self::loginSuccess($user);
    }

    /**
     * 用户查找
     * @param $data array 查询条件
     * @return mixed
     */
    protected static function find($data)
    {
        $where = $data;

        if (self::getConfig('is_hash') == true && array_key_exists('password', $where)) {
            unset($where['password']);
        }

        $model = self::getConfig('model');
        $user = $model::get($where);

        if (!$user) {
            return false;
        }

        /** 其他加密方式请直接在传入参数时处理好 */
        if (!self::getConfig('is_hash')) {
            return $user;
        }

        if (!isset($data['password'])) {
        	return $user;
        }

        /** Hash加密需要额外判断 */
        return Hash::check($data['password'], $user->password) ? $user : false;
    }

    /**
     * 登录成功
     * @param $user think\Model 已经登录到应用的用户
     * @return mixed
     */
    protected static function loginSuccess($user, $remember = false)
    {
        /** 保存数据 */
        self::$user = $user;

        /** 保存凭据 */
        $expires = $remember ? 24*30*3600 : 3600;

        Cookie::set(
        	self::getCookieName(),
            [
                'id'   => $user->id,
                'sign' => self::getUserSign($user)
            ],
            $expires
        );

        return true;
    }

    /**
     * 获取用户签名
     * @param $user think\Model 当前登录用户
     * @return string
     */
    protected static function getUserSign($user)
    {
        return Hash::make(self::getBeforeSignStr($user));
    }

    /**
     * 检测用户签名
     * @param $user think\Model 当前等待检测用户
     * @param $sign string 加密后的sign
     * @return boolean
     */
    protected static function checkUserSign($user, $sign)
    {
        return Hash::check(self::getBeforeSignStr($user), $sign);
    }

    /**
     * 获取用户签名未加密之前的字符
     * @param $user think\Model 当前登录用户
     * @return string
     */
    protected static function getBeforeSignStr($user)
    {
        $str = '';
        $str .= $user->id;
        $str .= substr($user->password, 20);

        return $str;
    }

    /**
     * 退出登录
     * @return void
     */
    public static function logout()
    {
    	Cookie::delete(self::getCookieName());

        return true;
    }

    /**
     * 获取CookieName
     * @return string
     */
    protected static function getCookieName()
    {
        return self::getConfig('prefix', 'tp5_qsnh_').'user';
    }
}