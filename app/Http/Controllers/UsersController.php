<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class UsersController extends Controller
{
    function __construct()
    {
        //只有已经登录的用户才能访问编辑页面
        $this->middleware('auth',[
            'only'=>['edit','update','destroy','followers','followings']
        ]);
        //只允许未登录的用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    /**
     * 列出所有的用户
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //$users = User::all();
        $users = User::paginate(10);
        return  view('user.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        //用户注册成功之后应该先激活账号
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
        //dump($user);
        //if($user) {
        //    Auth::login($user);
        //}
        //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        //return redirect()->route('users.show', [$user]);
    }

    protected function sendEmailConfirmationTo($user)
    {

        $view = 'emails.confirm';
        $data = compact('user');
        $from = '794973775@qq.com';
        $name = '一剑';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        //$user = ['email'=>'794973775@qq.com', 'name'=>'伸长脖子的狼', 'uid'=>1, 'activationcode'=>"abfawjfenawknflaawef"];
        $data = compact('user');
        Mail::send($view, $data, function($message) use($data)
        {
            $message->to('794973775@qq.com', 'hello')->subject('欢迎注册我们的网站，请激活您的账号！');
        });
        //Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
        //    $message->from($from, $name)->to($to)->subject($subject);
        //});
    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->first();
        //$user = User::where('activation_token', $token)->find();
        if($user) {

            dump($user);
        } else {
            dump($user);
        }
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::findOrFail($id);
        //查找微博
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(10);
        //var_dump($user);
        return view('user.show',['user'=>$user,'statuses'=>$statuses]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return view('user.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request,[
            'name' => 'required|min:2|max:50',
            'password' => 'confirmed|min:6'
        ]);
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $user->name = $request->name;
        $user->password = $request->password;
        //$user->update([
        //    'name' => $request->name,
        //    'password' => $request->password,
        //]);
        //dump($request);
        $data = array_filter([
            'name' => $request->name,
            'password' => $request->password,
        ]);

        $res = $user->update($data);
        if($res) {
            session()->flash('success','个人资料更新成功');
            return redirect()->route('users.show', $id);
        } else {
            dump($res);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::findOrFail($id);
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success',"用户-{$user->name}-已经被成功删除");
        return redirect()->back();
    }

    public function followings($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('user.show_follow', compact('users', 'title'));
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('user.show_follow', compact('users', 'title'));
    }
}
