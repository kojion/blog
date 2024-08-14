<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImagesController extends Controller
{
    /**
     * 画像一覧を返す.
     *
     * @param Request $request リクエスト
     * @return View ビュー
     */
    public function __invoke(Request $request): View
    {
        $query = Image::with(['tags']);
        $tag = null;
        if ($request->query('tag_id') !== null) {
            $query = $query->whereHas('tags', fn(Builder $query) => $query->where('tag_id', $request->query('tag_id')));
            $tag = Tag::whereId($request->query('tag_id'))->firstOrFail();
        }
        $images = $query->orderByDesc('created_at')->paginate(60);
        $tags = Tag::withCount(['posts', 'images'])
            ->orderBy('color')
            ->orderBy('rank')
            ->get()
            ->filter(fn($x) => $x->images_count > 0);
        return view('blog.images', compact('images', 'tag', 'tags'));
    }
}
