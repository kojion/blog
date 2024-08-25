<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * ログイン処理を実行する.
     *
     * @param Request $request リクエスト
     * @return Application|RedirectResponse|Redirector|View
     */
    public function __invoke(Request $request): Application|RedirectResponse|Redirector|View
    {
        // 既にログインしていたら管理画面に遷移
        if (Auth::check()) {
            return redirect('/admin');
        }
        $error = '';
        if ($request->isMethod('POST')) {
            $email = $request->input('email');
            $password = $request->input('password');
            $user = User::where('email', $email)->first();
            if ($user && password_verify($password, $user->password)) {
                Auth::login($user);
                return redirect('/admin');
            } else {
                $error = 'メールアドレスかパスワードが間違っています。';
            }
        }
        return view('auth.login', compact('error'));
    }
}
