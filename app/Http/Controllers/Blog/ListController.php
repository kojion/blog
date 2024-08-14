<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ListController extends Controller
{
    /**
     * タイトル一覧を返す.
     *
     * @param Request $request リクエスト
     * @return View ビュー
     */
    public function __invoke(Request $request): View
    {
        $query = Auth::check() ? Post::withTrashed() : Post::query();
        $query = $query->with(['tags']);
        $tag = null;
        if ($request->query('tag_id') !== null) {
            $query = $query->whereHas('tags', fn(Builder $query) => $query->where('tag_id', $request->query('tag_id')));
            $tag = Tag::whereId($request->query('tag_id'))->firstOrFail();
        }
        $posts = $query->orderByDesc('id')->paginate(100);

        $tags = Tag::withCount(['posts', 'images'])
            ->orderBy('color')
            ->orderBy('rank')
            ->get()
            ->filter(fn($x) => $x->posts_count > 0);

        // ページングのためのページリストを生成
        $range = 10;  // ページングに表示する数
        $total = $posts->lastPage();  // 総ページ数
        $current = $posts->currentPage();  // 現在のページ番号

        // 総ページ数よりページングに表示する数の方が多い場合は全体を返す
        if ($range >= $total) {
            $pages = range(1, $total);
        } else {
            // 現在ページをページング範囲の中央 (偶数の場合は中央の左側) に据えるが, その時はみ出す場合は調整する
            $start = $current - floor($range / 2) + (1 - $range % 2);
            if ($current - floor($range / 2) <= 0) {
                $start = 1;
            } elseif ($current + floor($range / 2) >= $total) {
                $start = $total - $range + 1;
            }
            $pages = range($start, $range + $start - 1);
        }
        return view('blog.list', compact('posts', 'pages', 'tag', 'tags'));
    }
}
