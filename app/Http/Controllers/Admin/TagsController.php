<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Wiki;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagsController extends Controller
{
    /**
     * 一覧を表示する.
     *
     * @param Request $request リクエスト
     * @return View ビュー
     */
    public function index(Request $request): View
    {
        $tags = Tag::withCount(['posts', 'images'])->orderBy('color')->orderBy('rank')->get();

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.tags-index', compact('tags', 'sidebar'));
    }
}
