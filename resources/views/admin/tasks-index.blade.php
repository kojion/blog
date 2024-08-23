@extends('admin.layout')

@section('title', 'コジオニルク - ToDo 一覧')

@section('content')
    <div class="row">
        <div class="col-12 col-md-9">
            <table class="table table-sm table-bordered my-4">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell text-center">ID</th>
                        <th class="text-center">種別</th>
                        <th class="text-center">ToDo</th>
                        <th class="text-center">完了日時</th>
                        <th class="text-center">作成日時</th>
                        <th class="text-center">更新日時</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
    @foreach($tasks as $task)
                    <tr class="{{ strlen($task->done_at) ? 'bg-grey300' : '' }}">
                        <td class="px-2 text-center">{{ $task->id }}</td>
                        <td class="px-2 text-center">{{ $task->taskGroup->name }}</td>
                        <td class="px-2 text-right">{{ $task->name }}</td>
                        <td class="px-2 text-right">{{ $task->done_at }}</td>
                        <td class="px-2 text-right">{{ $task->created_at }}</td>
                        <td class="px-2 text-right">{{ $task->updated_at }}</td>
                        <td class="px-2 text-center">
                            <button class="edit-task btn btn-sm btn-primary" data-id="{{ $task->id }}" data-group-id="{{ $task->taskGroup->id }}" data-name="{{ $task->name }}">編集</button>
                            <form class="delete-task-form" method="POST" action="/admin/tasks/{{ $task->id }}">
                                @csrf
                                @method('DELETE')
                                <button class="delete-task btn btn-sm btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
    @endforeach
                </tbody>
            </table>
        </div>
        <div class="post d-none d-md-block col-3 py-3">
            <div id="sidebar" class="card p-2">
                <div id="edit-sidebar">
                    <a href="/admin/wikis/edit/1">編集</a>
                </div>
                {!! $sidebar->html !!}
            </div>
        </div>
    </div>
    <div class="modal fade" id="task-modal" tabindex="-1" role="dialog" aria-labelledby="task-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="task-modal-label">ToDo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-task-form" action="/admin/tasks" method="POST">
                        <div class="form-group">
                            @method('PUT')
                            @csrf
                            <select class="custom-select" name="task_group_id" id="task-group-id">
                                @foreach($taskGroups as $taskGroup)
                                    <option value="{{ $taskGroup->id }}">{{ $taskGroup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="task-modal-name">タスク</label>
                            <input type="text" name="name" class="form-control" id="task-modal-name" placeholder="タスク">
                        </div>
                        <div class="form-group text-right">
                            <input type="submit" class="btn btn-primary" value="更新"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
