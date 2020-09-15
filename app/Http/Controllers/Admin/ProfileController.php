<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 以下を追記することでProfiles Modelが扱えるようになる
use App\Profiles;
use App\Profhistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function add()
    {
        return view('admin.profile.create');
    }

    public function create(Request $request)
    {
    
        // Varidationを行う
        $this->validate($request, Profiles::$rules);
        $profiles = new Profiles;
        $form = $request->all();

        // データベースに保存する
        $profiles->fill($form);
        $profiles->save();

        return redirect('admin/profile/create');
    }

    public function index(Request $request)
    {
        $cond_name = $request->cond_name;
        if ($cond_name != '') {
        // 検索されたら検索結果を取得する
        $posts = Profiles::where('name', $cond_name)->get();
    } else {
        // それ以外はすべてのprofileを取得する
        $posts = Profiles::all();
    }
    return view('admin.profile.index', ['posts' => $posts, 'cond_name' => $cond_name]);
  }

    public function edit(Request $request)
    {
        // Profiles Modelからデータを取得する
        $profiles = Profiles::find($request->id);
        if (empty($profiles)) {
        abort(404);    
        }
        return view('admin.profile.edit', ['profiles_form' => $profiles]);
    }

    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profiles::$rules);
        // Profiles Modelからデータを取得する
        $profiles = Profiles::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profiles_form = $request->all();
        unset($profiles_form['_token']);

        // 該当するデータを上書きして保存する
        $profiles->fill($profiles_form)->save();

        // 以下を追記
        $profhistory = new Profhistory;
        $profhistory->profiles_id = $profiles->id;
        $profhistory->edited_at = Carbon::now();
        $profhistory->save();

        return redirect('admin/profile/');
    }

    public function delete(Request $request)
    {
        // 該当するProfiles Modelを取得
        $profiles = Profiles::find($request->id);
        // 削除する
        $profiles->delete();
        return redirect('admin/profile/');
    }
}