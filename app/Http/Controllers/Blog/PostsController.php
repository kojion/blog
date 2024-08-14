<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PostsController extends Controller
{
    /**
     * 記事一覧を返す.
     *
     * @param Request $request リクエスト
     * @param int|null $id 記事 ID
     * @return View ビュー
     */
    public function __invoke(Request $request, ?int $id = null): View
    {
        // タグ一覧取得
        $tags = Tag::withCount(['posts'])
            ->orderBy('color')
            ->orderBy('rank')
            ->get()
            ->filter(fn($x) => $x->posts_count > 0);

        // 年月毎の記事数を取得
        $query = Auth::check() ? Post::withTrashed() : Post::query();
        $yearMonths = $query->groupBy(['year', 'month'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get(['year', 'month', DB::raw('COUNT(id) AS count')]);

        if (!is_null($id)) {
            // 個別記事モード
            $query = Auth::check() ? Post::withTrashed() : Post::query();
            $posts = [$query->with(['tags'])->where(compact('id'))->firstOrFail()];
            $data = compact('tags', 'posts', 'yearMonths');
        } elseif (strlen($request->query('query')) || strlen($request->query('tag_id'))) {
            // 検索モード
            $query = Auth::check() ? Post::withTrashed() : Post::query();
            $query = $query->with(['tags']);
            if ($request->query('tag_id') !== null) {
                $query = $query->whereHas('tags', fn(Builder $query) => $query->where('tag_id', $request->query('tag_id')));
            }
            $words = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $request->query('query'));
            if (strlen($words) > 0) {
                foreach (preg_split('/ |　/', $words) as $q) {
                    $query = $query->where('markdown', 'LIKE', "%$q%");
                }
            }
            $posts = $query->orderByDesc('id')->paginate(100);

            // タグの場合はタグ名も返す
            $tagName = $request->has('tag_id') ? Tag::whereId($request->query('tag_id'))->firstOrFail()->name : '';

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
            $data = compact('tags', 'posts', 'yearMonths', 'pages', 'tagName');
        } else {
            // 年月絞り込みモード (デフォルト)
            [$year, $month] = [$request->query('year'), $request->query('month')];

            // 年月の指定がない場合は最終の年月とする
            if ($year == null || $month == null || !is_numeric($year) || !is_numeric($month)) {
                $query = Auth::check() ? Post::withTrashed() : Post::query();
                $post = $query->orderByDesc('year')
                    ->orderByDesc('month')
                    ->firstOr(['year', 'month'], fn() => null);
                list($year, $month) = [$post->year ?? null, $post->month ?? null];
            } else {
                list($year, $month) = [intval($year), intval($month)];
            }
            $query = Auth::check() ? Post::withTrashed() : Post::query();
            $posts = $query->with(['tags'])
                ->where('year', $year)
                ->where('month', $month)
                ->orderByDesc('day')
                ->paginate(31); // 1 ヶ月は 31 日までなので

            // カレンダーに描画する日付を 7 日ごとにチャンク化して生成する
            $firstDate = Carbon::createFromDate($year, $month, 1);
            $date = $firstDate->subDays($firstDate->dayOfWeek);
            $calendarDates = [];
            do {
                $f = fn($x) => Carbon::createFromDate($date->year, $date->month, $date->day + $x);
                $calendarDates[] = collect(range(0, 6))->map($f);
                $date->addDays(7);
            } while (intval($month) === $date->month);

            // データが存在する日
            $existsDates = collect($posts->items())->map(fn($x) => $x['day'])->toArray();

            // 最初の日付
            $query = Auth::check() ? Post::withTrashed() : Post::query();
            $firstDate = $query->orderBy('year')
                ->orderBy('month')
                ->orderBy('day')
                ->firstOrFail(['year', 'month', 'day']);

            // 全件数
            $query = Auth::check() ? Post::withTrashed() : Post::query();
            $count = $query->firstOrFail([DB::raw('COUNT(id) AS count')])->count;

            // 前と次の年月
            $prevYearMonth = Carbon::createFromDate($year, $month - 1, 1);
            $nextYearMonth = Carbon::createFromDate($year, $month + 1, 1);

            $data = compact(
                'tags',
                'posts',
                'year',
                'month',
                'calendarDates',
                'existsDates',
                'firstDate',
                'count',
                'yearMonths',
                'prevYearMonth',
                'nextYearMonth'
            );
        }
        return view('blog.posts', $data);
    }
}
