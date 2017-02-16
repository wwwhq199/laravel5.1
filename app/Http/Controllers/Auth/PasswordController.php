<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use App\Http\Requests\Request;
//use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

//use Illuminate\Support\Facades\Request;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectPath = '/';
    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    //发送邮件视图
    function getEmail() {
        //echo "123";
        return view('auth.password');
    }
    // 发送邮件操作
    function postEmail(Request $request) {
        $this->validate($request,[
            'email' =>'required',
        ]);
        //first查询第条一满足条件的记录，没有返回null
        $user = User::where('email',$request->email)->first();
        //dump($user);
        if($user) {
            session()->flash('status','重置密码链接已发送到邮箱');
            $token = str_random(30);
            $data = [$user->email,$token,time()];
            DB::insert('insert into password_resets (email, token,created_at) values (?, ?,?)', $data);
           $this->sendEmailConfirmationTo($token,$user);
            return redirect()->back();
        } else {
            session()->flash('warning','您输入的邮箱不正确');
            return redirect()->back();
        }
        //$user = User::all();
        //dump($user->email);

    }
    protected function sendEmailConfirmationTo($token,$user)
    {
        $view = 'emails.password';
        $data = compact('token');
        //$from = 'osword@qq.com';
        //$name = '一剑';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to,$subject) {
            $message->to($to)->subject($subject);
        });
    }
    // 修改密码视图
    function getReset($token) {
        $results = DB::select('select * from password_resets where token = :token', ['token' => $token]);
        return view('auth.reset',['token'=>$token]);
    }
    // 更新密码操作
    function postReset(Request $request) {
        $this->validate($request,[
           'email' => 'required',
            'password' => 'confirmed|min:6|required'
        ]);
        //验证token
        $token = $request->token;
        $email = $request->email;
        $password = $request->password;
        $result = DB::select("select * from password_resets WHERE email = :email AND token = :token",[$email,$token]);
        if($result) {
            $user = User::where('email',$email)->first();
            $user->password = $password;
            $user->save();
            DB::delete("delete from password_restes WHERE token = :token AND email = :email",[$token,$email]);
            session()->flash('success','密码修改成功');
            Auth::login($user);
            return redirect()->route('home');
        } else {
            return redirect()->back()->withErrors('请检查输入的邮箱地址是否正确');
        }
    }


}
