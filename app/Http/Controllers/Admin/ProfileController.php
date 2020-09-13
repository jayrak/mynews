<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 以下を追記することでProfiles Modelが扱えるようになる
use App\Profiles;

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
        $profile = new Profiles;
        $form = $request->all();

        foreach ($form as $s) {
            echo $s;
        }

        // データベースに保存する
        $profile->fill($form);
        $profile->save();

        return redirect('admin/profile/create');
    }

    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
        // 検索されたら検索結果を取得する
        $posts = Profiles::where('title', $cond_title)->get();
    } else {
        // それ以外はすべてのprofileを取得する
        $posts = Profiles::all();
    }
    return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }

    public function edit(Request $request)
    {
        // Profiles Modelからデータを取得する
        $profile = Profiles::find($request->id);
        if (empty($profile)) {
        abort(404);    
        }
        return view('admin.profile.edit', ['profile_form' => $profile]);
    }

    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profiles::$rules);
        // Profiles Modelからデータを取得する
        $profile = Profiles::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profile_form = $request->all();
        unset($profile_form['_token']);

        // 該当するデータを上書きして保存する
        $profile->fill($profile_form)->save();

        return redirect('admin/profile');
    }

    public function delete(Request $request)
    {
        // 該当するProfiles Modelを取得
        $profile = Profiles::find($request->id);
        // 削除する
        $profile->delete();
        return redirect('admin/profile/');
    }
}