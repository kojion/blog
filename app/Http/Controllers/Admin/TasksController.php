<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\Wiki;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TasksController extends Controller
{
    /**
     * Task 一覧を表示する.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // タスクグループを取得
        $taskGroups = TaskGroup::query()->orderBy('rank')->get();

        // タスクを全取得
        $tasks = Task::query()->orderByDesc('created_at')->get();

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();
        return view('admin.tasks-index', compact('taskGroups', 'tasks', 'sidebar'));
    }

    /**
     * Task を保存する.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'task_group_id' => 'required',
        ], [
            'name.required' => '名前を入力してください。',
            'task_group_id.required' => 'タスクグループ ID を選択してください。',
        ]);
        Task::create($data);

        // Flash メッセージ作成しリダイレクト
        return redirect('/admin')->with('success', 'ToDo を登録しました。');
    }

    /**
     * Task を更新する.
     *
     * @param Request  $request
     * @param  int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'task_group_id' => 'required',
            'done_at' => ''
        ], [
            'name.required' => '名前を入力してください。',
            'task_group_id.required' => 'タスクグループ ID を選択してください。',
        ]);

        $task = Task::whereId($id)->firstOrFail();
        $task->fill($data);
        $message = "ToDo '${data['name']}' を更新しました。";
        if ($data['done_at'] ?? '' === 'now') {
            $task->done_at = Carbon::now();
            $message = "ToDo '${data['name']}' を完了しました。";
        }
        $task->save();
        return redirect("/admin")->with('success', $message);
    }

    /**
     * Task の削除を行う.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        Task::whereId($id)->firstOrFail()->delete();
        return redirect('/admin/tasks')->with('success', 'ToDo を削除しました。');
    }
}
