<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Wiki;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WikisController extends Controller
{
    /**
     * 詳細を表示する.
     *
     * @param Request $request リクエスト
     * @param int $id Wiki ID
     * @return View ビュー
     */
    public function show(Request $request, int $id): View
    {
        // Wiki のデータを取得
        $wiki = Wiki::whereId($id)->firstOrFail();

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.wikis-show', compact('wiki', 'sidebar'));
    }

    /**
     * 登録画面を表示する.
     *
     * @param Request $request リクエスト
     * @return View ビュー
     */
    public function create(Request $request): View
    {
        // POST データが渡されてきた場合など old に詰め直す
        if (count($request->input())) {
            $request->session()->flash('_old_input', $request->input());
        }

        // プレビュー用のデータを生成する
        $old = $request->session()->get('_old_input') ?? [];
        $wiki = null;
        if (count($old)) {
            $wiki = new Wiki();
            $wiki->markdown = $old['markdown'] ?? '';
        }

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.wikis-create', compact('wiki', 'sidebar'));
    }

    /**
     * Wiki を保存する.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['markdown' => 'required'], ['markdown.required' => '本文を入力してください。']);
        $wiki = Wiki::create(['markdown' => $data['markdown']]);
        History::create(['wiki_id' => intval($wiki->id), 'markdown' => $wiki->markdown, 'created_at' => Carbon::now()]);
        return redirect("/admin/wikis/$wiki->id")->with('success', 'Wiki を登録しました。');
    }

    /**
     * 更新画面を表示する.
     *
     * @param Request $request リクエスト
     * @param int $id Wiki ID
     * @return View ビュー
     */
    public function edit(Request $request, int $id): View
    {
        // POST データが渡されてきた場合など old に詰め直す
        if (count($request->input())) {
            $request->session()->flash('_old_input', $request->input());

            // プレビュー用のデータを生成する
            $old = $request->session()->get('_old_input') ?? [];
            $wiki = null;
            if (count($old)) {
                $wiki = new Wiki();
                $wiki->markdown = $old['markdown'] ?? '';
            }
        } elseif ($request->isMethod('GET')) {
            $wiki = Wiki::whereId($id)->firstOrFail();
            $request->session()->flash('_old_input', ['markdown' => $wiki->markdown]);
        }

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.wikis-create', compact('wiki', 'sidebar'));
    }

    /**
     * Wiki を更新する.
     *
     * @param Request $request リクエスト
     * @param  int $id Wiki ID
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['markdown' => 'required'], ['markdown.required' => '本文を入力してください。']);
        $wiki = Wiki::whereId($id)->firstOrFail();
        $wiki->markdown = $data['markdown'];
        $wiki->save();
        History::create(['wiki_id' => intval($wiki->id), 'markdown' => $wiki->markdown, 'created_at' => Carbon::now()]);
        return redirect("/admin/wikis/$id")->with('success', 'Wiki を更新しました。');
    }

    /**
     * Wiki の削除を行う.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        Wiki::whereId($id)->firstOrFail()->delete();
        return redirect('/admin')->with('success', 'Wiki を削除しました。');
    }
}
