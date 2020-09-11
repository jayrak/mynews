<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 以下を追記することでNews Modelが扱えるようになる
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
        $news = new Profiles;
        $form = $request->all();

        foreach ($form as $s) {
            echo $s;
        }

        // データベースに保存する
        $news->fill($form);
        $news->save();

        return redirect('admin/profile/create');
    }

    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
        // 検索されたら検索結果を取得する
        $posts = Profiles::where('title', $cond_title)->get();
    } else {
        // それ以外はすべてのニュースを取得する
        $posts = Profiles::all();
    }
    return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }

    public function edit(Request $request)
    {
        // News Modelからデータを取得する
        $news = Profiles::find($request->id);
        if (empty($news)) {
        abort(404);    
        }
        return view('admin.profile.edit', ['news_form' => $news]);
    }

    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profiles::$rules);
        // News Modelからデータを取得する
        $news = Profiles::find($request->id);
        // 送信されてきたフォームデータを格納する
        $news_form = $request->all();
        unset($news_form['_token']);

        // 該当するデータを上書きして保存する
        $news->fill($news_form)->save();

        return redirect('admin/profile');
    }

    public function delete(Request $request)
    {
        // 該当するNews Modelを取得
        $news = Profiles::find($request->id);
        // 削除する
        $news->delete();
        return redirect('admin/profile/');
    }
}