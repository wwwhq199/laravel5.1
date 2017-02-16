<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test','TestController@index');

//使用name的命名路由
get('/', 'StaticPagesController@home')->name('home');
get('/help', 'StaticPagesController@help')->name('help');
get('/about', 'StaticPagesController@about')->name('about');
//用户注册
get('signup', 'UsersController@create')->name('signup');

get('sendmail',function(){
    $data = ['email'=>'794973775@qq.com', 'name'=>'伸长脖子的狼', 'uid'=>1, 'activationcode'=>"abfawjfenawknflaawef"];
    Mail::send('activemail', $data, function($message) use($data)
    {
        $message->to($data['email'], $data['name'])->subject('欢迎注册我们的网站，请激活您的账号！');
    });
});

resource('users','UsersController');
//等同于
//get('/users','UsersController@index');
//get('/users/{id}','UsersController@show');
//get('/users/create','UsersController@create');
//post('/users','UsersController@store');
//
//get('/users', 'UsersController@index')->name('users.index');
//get('/users/{id}', 'UsersController@show')->name('users.show');
//get('/users/create', 'UsersController@create')->name('users.create');
//post('/users', 'UsersController@store')->name('users.store');
//get('/users/{id}/edit', 'UsersController@edit')->name('users.edit');
//patch('/users/{id}', 'UsersController@update')->name('users.update');
//delete('/users/{id}', 'UsersController@destroy')->name('users.destroy');

//获取用户的登陆的页面
get('login','SessionController@create')->name('login');
//提交用户的登陆数据
post('login','SessionController@store')->name('login');
//退出登录
delete('logout','SessionController@destroy')->name('logout');
//用户激活账号
get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');
//密码重置
get('password/email', 'Auth\PasswordController@getEmail')->name('password.reset');
post('password/email', 'Auth\PasswordController@postEmail')->name('password.reset');
get('password/reset/{token}', 'Auth\PasswordController@getReset')->name('password.edit');
post('password/reset', 'Auth\PasswordController@postReset')->name('password.update');

Route::get('mail/send','MailController@send');

//微博的创建和删除
resource('statuses','StatusesController',['only'=>['store','destroy']]);
// 我关注的人和关注我的人
get('/users/{id}/followings', 'UsersController@followings')->name('users.followings');
get('/users/{id}/followers', 'UsersController@followers')->name('users.followers');

post('/users/followers/{id}', 'FollowersController@store')->name('followers.store');
delete('/users/followers/{id}', 'FollowersController@destroy')->name('followers.destroy');