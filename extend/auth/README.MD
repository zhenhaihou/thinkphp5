Thinkphp5 Auth Manger Library.
=======

## Usage

First, u need configure some information:

### Create DataBase Table:
~~~
Run Sql:

CREATE TABLE IF NOT EXISTS users (
    `id` int(11) unsigned not null PRIMARY KEY AUTO_INCREMENT,
    `username` varchar(32) not null,
    `password` varchar(64) not null
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

Or Create a migrations file and add copy the code to up methods:

$table = $this->table('users');
$table->addColumn('username', 'string', ['limit' => 32])
    ->addColumn('password', 'string', ['limit' => 64])
    ->save();

~~~


### Configure information
1.Create the `auth.php` file in the `application/extra` directory,as follows:
~~~
<?php
return [
    'model'    => 'app\\model\\user',
    /** User password is use Hash Encrypt */
    'is_hash'  => true,
];
~~~


### Methods List
~~~
<?php
use Qsnh\think\Auth\Auth;

/** Check user if login */
Auth::check();

/** login on app by user id */
Auth::loginByUserId($id);

/** login on app by user model */
Auth::login(think\Model $user);

/** login on app by array  */
Auth::attemp(array $credential, $remember = false);

/** Get logged-in user */
Auth::user();

/** get logged-in user id */
Auth::id();

~~~

Thinkphp5 Auth一键集成库
=======

在使用之前，需要配置几项信息：

### 创建数据表：
~~~
运行下面的 Sql:

CREATE TABLE IF NOT EXISTS users (
    `id` int(11) unsigned not null PRIMARY KEY AUTO_INCREMENT,
    `username` varchar(32) not null,
    `password` varchar(64) not null
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

或者创建一个migrations文件将下面的代码复制到的up()里面：

php think migrate:create CreateUsersTable

$table = $this->table('users');
$table->addColumn('username', 'string', ['limit' => 32])
    ->addColumn('password', 'string', ['limit' => 64])
    ->save();

php think migrate:run

~~~

### 配置信息
1.在`application/extra`目录下面创建`auth.php`文件，填入下面内容：
~~~
<?php
return [
    /** 数据表关联模型 */
    'model'    => 'app\\model\\user',

    /** 如果密码使用hash加密的话 */
    'is_hash'  => true,
];
~~~

### 方法列表
~~~
<?php
use Qsnh\think\Auth\Auth;

/** 检测用户是否登录 */
Auth::check();

/** 通过ID登录 */
Auth::loginByUserId($id);

/** 通过已有的 User (extends think\Model) 登录 */
Auth::login(think\Model $user);

/** 通过一些条件登录，$remember=true => 会话长期保存  */
Auth::attemp(array $credential, $remember = false);

/** 获取已经的登录的用户，返回 User (extends think\Model) */
Auth::user();

/** 获取当前登录用户的ID */
Auth::id();

~~~

