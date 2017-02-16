<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    //值也许为登陆的用户访问登陆页面
    function __construct()
    {
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

    //
    function create() {
        return view('session.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        $cedentails = [
            'email' =>$request->email,
            'password' => $request->password,
        ];
        if(Auth::attempt($cedentails,$request->has('remember'))) {
            dump($cedentails);

            //判断用户账号是否已经激活
            if (Auth::user()->activated) {
                session()->flash('success','欢迎回来');
                //return redirect()->route('users.show',[Auth::user()]);
                //intended方法是返回上一次请求的页面，没有没有则使用默认的地址进行跳转
                return redirect()->intended(route('users.show',[Auth::user()]));
            } else {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }

        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
        return;
    }
  //退出登录
    function destroy() {
        Auth::logout();
        session()->flash('success','您已成功退出登录');
        return redirect('login');
    }
}
