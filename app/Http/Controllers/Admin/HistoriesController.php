<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Wiki;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoriesController extends Controller
{
    /**
     * 詳細を表示する.
     *
     * @param Request $request リクエスト
     * @param int $id 履歴 ID
     * @return View ビュー
     */
    public function show(Request $request, int $id): View
    {
        $history = History::whereId($id)->firstOrFail();

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.histories', compact('history', 'sidebar'));
    }
}
