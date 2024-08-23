<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogoutController extends Controller
{
    /**
     * ログアウト処理を実行する.
     *
     * @param Request $request リクエスト
     * @return Application|RedirectResponse|Redirector|View
     */
    public function __invoke(Request $request): Application|RedirectResponse|Redirector|View
    {
        Auth::logout();
        return redirect('/');
    }
}
