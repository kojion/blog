<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostsController extends Controller
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
        } elseif (!$request->session()->has('_old_input')) {
            // 初期表示時は最後の記事の次の日付とする
            $last = Post::query()
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->orderByDesc('day')
                ->firstOrFail();
            $lastDate = Carbon::createFromDate($last->year, $last->month, $last->day);
            $nextDate = $lastDate->addDay();
            $request->session()->flash('_old_input', ['date' => $nextDate->format('Y-m-d')]);
        }
        return view('admin.posts', $this->makeData($request));
    }

    /**
     * 記事を保存する.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        [$year, $month, $day] = explode('-', $request->input('date') ?? 'x-x-x');
        $exists = function (string $attribute, string $value, callable $fail) use ($year, $month, $day) {
            if (Post::withTrashed()->where(compact('year', 'month', 'day'))->count() > 0) {
                $fail('その日付はすでに登録されています。');
            }
        };
        $data = $request->validate([
            'date' => ['required', $exists],
            'name' => 'required',
            'tags' => 'required',
            'markdown' => 'required',
            'enabled' => 'required'
        ], [
            'date.required' => '日付を選択してください。',
            'name.required' => '記事名を入力してください。',
            'tags.required' => 'タグを選択してください。',
            'markdown.required' => '本文を入力してください。',
            'enabled.required' => '有効状態を入力してください。',
        ]);
        $post = Post::create([
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'name' => $data['name'],
            'markdown' => $data['markdown']
        ]);
        $post->tags()->sync($data['tags']);

        // 非公開状態の場合論理削除して自画面遷移
        $message = "${data['date']} の記事「${data['name']}」を投稿しました。";
        if ($data['enabled'] === 'false') {
            $post->delete();
            return redirect('/admin/posts/edit/' . $post->id)->with('success', $message);
        }

        // Flash メッセージ作成しリダイレクト
        return redirect('/')->with('success', $message);
    }

    /**
     * 更新画面を表示する.
     *
     * @param Request $request リクエスト
     * @param int $id 記事 ID
     * @return View ビュー
     */
    public function edit(Request $request, int $id): View
    {
        // POST データが渡されてきた場合など old に詰め直す
        if (count($request->input())) {
            $request->session()->flash('_old_input', $request->input());
        } elseif (!$request->session()->has('_old_input')) {
            $post = Post::withTrashed()->whereKey($id)->firstOrFail();
            $f = fn(int $n): string => ($n > 9 ? '' : '0') . $n;
            $request->session()->flash('_old_input', [
                'date' => $post->year . '-' . $f($post->month) . '-' . $f($post->day),
                'name' => $post->name,
                'tags' => $post->tags->map(fn($x) => $x->id)->toArray(),
                'markdown' => $post->markdown
            ]);
        }
        return view('admin.posts', $this->makeData($request));
    }

    /**
     * 記事を更新する.
     *
     * @param Request  $request
     * @param  int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        [$year, $month, $day] = explode('-', $request->input('date'));
        $exists = function (string $attribute, string $value, callable $fail) use ($year, $month, $day, $id) {
            if (Post::withTrashed()->where(compact('year', 'month', 'day'))->where('id', '!=', $id)->count() > 0) {
                $fail('その日付はすでに登録されています。');
            }
        };
        $data = $request->validate([
            'date' => ['required', $exists],
            'name' => 'required',
            'tags' => 'required',
            'markdown' => 'required',
            'enabled' => 'required'
        ], [
            'date.required' => '日付を選択してください。',
            'name.required' => '記事名を入力してください。',
            'tags.required' => 'タグを選択してください。',
            'markdown.required' => '本文を入力してください。',
            'enabled.required' => '有効状態を入力してください。'
        ]);

        $post = Post::withTrashed()->find($id);
        $post->fill(['year' => $year, 'month' => $month, 'day' => $day, 'name' => $data['name'], 'markdown' => $data['markdown']]);
        $post->save();
        $data['enabled'] === 'true' ? $post->restore() : $post->delete();
        $post->tags()->sync($data['tags']);

        // 非公開状態の場合論理削除して自画面遷移
        $message = "${data['date']} の記事「${data['name']}」を更新しました。";
        if ($data['enabled'] === 'false') {
            $post->delete();
            return redirect('/admin/posts/edit/' . $post->id)->with('success', $message);
        }
        return redirect("/posts/$post->id")->with('success', $message);
    }

    /**
     * 登録・更新画面初期表示用のデータを生成する.
     *
     * @param Request $request リクエスト
     * @return array データ
     */
    private function makeData(Request $request): array
    {
        $data = [];
        $old = $request->session()->get('_old_input') ?? [];

        // プレビュー用のデータを生成する
        if (count($old)) {
            $data['post'] = new Post();
            if (strlen($old['date'])) {
                [$year, $month, $day] = explode('-', $old['date']);
                $data['post']->fill(['year' => $year, 'month' => $month, 'day' => $day]);
            }
            $data['post']->name = $old['name'] ?? '';
            $data['post']->markdown = $old['markdown'] ?? '';
            if (count($old['tags'] ?? [])) {
                $data['post']->tags = Tag::query()->whereIn('id', $old['tags'])->get();
            }
        }

        // 選択肢の表示用のデータを取得する
        $data['tags'] = Tag::query()->orderBy('color')->orderBy('rank')->get();
        $data['images'] = Image::query()->orderByDesc('created_at')->limit(4)->get();
        return $data;
    }
}
