<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StatusesController extends Controller
{
    //

    function __construct()
    {
        $this->middleware('auth',[
            'only' => ['store','destroy']
        ]);
    }

    public function store (Request $request) {
        $this->validate($request,[
            'content' => 'required|max:140'
        ]);

        $user = Auth::user();
        $user->statuses()->create([
            'content' => $request->content
        ]);
        session()->flash('success','发送成功！');
        return redirect()->back();
    }

    public function destroy($id) {
        $status = Status::findOrFail($id);
        $this->authorize('destroy',$status);
        $status->delete();
        session()->flash('success','删除成功！');
        return redirect()->back();
    }
}
