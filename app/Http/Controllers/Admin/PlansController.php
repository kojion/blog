<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Wiki;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlansController extends Controller
{
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
        $plan = null;
        if (count($old)) {
            $plan = new Plan();
            $plan->date = $old['date'] ?? '';
            $plan->time = $old['time'] ?? '';
            $plan->name = $old['name'] ?? '';
        } else {
            // 初期日付は現在日付とする. GET パラメータで渡ってきていたらそのデータとする
            $request->session()->flash('_old_input', ['date' => $request->query('date', Carbon::now()->format('Y-m-d'))]);
        }

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.plans-create', compact('plan', 'sidebar'));
    }

    /**
     * 予定を保存する.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['date' => 'required', 'name' => 'required', 'time' => ''], [
            'date.required' => '日付を選択してください。',
            'name.required' => '概要を入力してください。'
        ]);
        Plan::create(['name' => $data['name'], 'date' => $data['date'], 'time' => $data['time'] ?: null]);
        return redirect("/admin")->with('success', '予定を登録しました。');
    }

    /**
     * 更新画面を表示する.
     *
     * @param Request $request リクエスト
     * @param int $id 予定 ID
     * @return View ビュー
     */
    public function edit(Request $request, int $id): View
    {
        // POST データが渡されてきた場合など old に詰め直す
        if (count($request->input())) {
            $request->session()->flash('_old_input', $request->input());

            // プレビュー用のデータを生成する
            $old = $request->session()->get('_old_input') ?? [];
            $plan = null;
            if (count($old)) {
                $plan = new Plan();
                $plan->date = $old['date'] ?? '';
                $plan->time = $old['time'] ?? '';
                $plan->name = $old['name'] ?? '';
            }
        } elseif ($request->isMethod('GET')) {
            $plan = Plan::whereId($id)->firstOrFail();
            $request->session()->flash('_old_input', ['name' => $plan->name, 'date' => $plan->date, 'time' => $plan->time]);
        }

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.plans-create', compact('plan', 'sidebar'));
    }

    /**
     * 予定を更新する.
     *
     * @param Request $request リクエスト
     * @param  int $id 予定 ID
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['date' => 'required', 'name' => 'required', 'time' => ''], [
            'date.required' => '日付を選択してください。',
            'name.required' => '概要を入力してください。'
        ]);
        $plan = Plan::whereId($id)->firstOrFail();
        $plan->date = $data['date'];
        $plan->time = $data['time'];
        $plan->name = $data['name'];
        $plan->save();
        return redirect("/admin")->with('success', '予定を更新しました。');
    }

    /**
     * 予定の削除を行う.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        Plan::whereId($id)->firstOrFail()->delete();
        return redirect('/admin')->with('success', '予定を削除しました。');
    }
}
