<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;

class ImagesController extends Controller
{
    /**
     * 登録画面を表示する.
     *
     * @param Request $request リクエスト
     * @return View ビュー
     */
    public function create(Request $request): View
    {
        $tags = Tag::query()->orderBy('color')->orderBy('rank')->get();
        return view('admin.images', compact('tags'));
    }

    /**
     * 画像を保存する.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => '',
            'tags' => 'required',
            'file' => ['required', 'mimes:jpeg,png']
        ], [
            'tags.required' => 'タグを選択してください。',
            'file.required' => 'ファイルを選択してください。',
            'file.mimes' => 'JPEG か PNG 画像をアップロードしてください。'
        ]);
        $file = $data['file'];

        /** @var UploadedFile $file */
        $datetime = date('Ymd-His');
        $extension = $file->extension() === 'jpeg' ? 'jpg' : $file->extension();

        // オリジナル画像保存
        $file->storeAs('public/image', "$datetime.$extension");

        // サムネイル画像 max-width が横長画像なら 800, 縦長なら 800 以内になるようにする
        foreach ([1 => '', 2 => '@2x', 3 => '@3x'] as $ratio => $label) {
            $image = Image::make($file);
            $image->orientate();
            $originalWidth = $image->getWidth();
            $originalHeight = $image->getHeight();
            $isLandscape = $originalWidth > $originalHeight;
            $width = $isLandscape ? 800 * $ratio : null;
            $height = $isLandscape ? null : 800 * $ratio;
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path("app/public/thumbnail/$datetime$label.$extension"));
        }

        // ファイル名が無い場合日付で補う
        if (!strlen(trim($data['name']))) {
            $data['name'] = Carbon::now()->format('Y 年 n 月 j 日');
        }

        // DB に保存
        $image = \App\Models\Image::create([
            'name' => $data['name'],
            'image'=> "$datetime.$extension",
            'size' => $file->getSize(),
            'width' => $originalWidth,
            'height' => $originalHeight
        ]);
        $image->tags()->sync($data['tags']);
        return redirect('/admin/images/create')->with('success', '画像を登録しました。');
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
        $image = \App\Models\Image::with(['tags'])->whereKey($id)->firstOrFail();
        $request->session()->flash('_old_input', [
            'name' => $image->name,
            'tags' => $image->tags->map(fn($x) => $x->id)->toArray()
        ]);
        $tags = Tag::query()->orderBy('color')->orderBy('rank')->get();
        return view('admin.images', compact('image', 'tags'));
    }

    /**
     * 画像を更新する (画像自体の更新はしない).
     *
     * @param Request  $request
     * @param  int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => '',
            'tags' => 'required'
        ], [
            'tags.required' => 'タグを選択してください。'
        ]);
        $image = \App\Models\Image::findOrFail($id);

        // ファイル名が無い場合ファイルの作成日付で補う
        if (!strlen(trim($data['name']))) {
            $data['name'] = $image->created_at->format('Y 年 n 月 j 日');
        }

        $image->fill(['name' => $data['name']]);
        $image->save();
        $image->tags()->sync($data['tags']);
        return redirect('/images')->with('success', '画像を更新しました。');
    }

    /**
     * 画像の削除を行う.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $image = \App\Models\Image::findOrFail($id);
        $names = explode('.', $image->image);

        // サムネイル画像の削除
        Storage::disk('local')->delete([
            "public/image/$image->image",
            "public/thumbnail/$image->image",
            "public/thumbnail/$names[0]@2x.$names[1]",
            "public/thumbnail/$names[0]@3x.$names[1]"
        ]);

        // DB レコードの削除
        $image->tags()->detach();
        $image->delete();
        return redirect('/images')->with('success', '画像を削除しました。');
    }
}
