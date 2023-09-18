<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\UserToken;
use App\Http\Requests\SendPasswordResetEmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;
// use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class AuthController extends Controller
{
    // // Laravelに標準搭載されているパスワード再設定機能を使うための記述
    // use SendsPasswordResetEmails;
    // use SendsPasswordResetEmails, ResetsPasswords;

    // ユーザー登録
    public function register(Request $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_type_id' => 1
        ]);
        $json = [
            'data' => $user
        ];
        return response()->json( $json, Response::HTTP_OK);
    }

    // ログイン
    public function login(LoginRequest $request) {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::whereEmail($request->email)->first();
            $user->tokens()->delete();
            $token = $user->createToken("login:user{$user->id}")->plainTextToken;
            //ログインが成功した場合はトークンを返す
            return response()->json(['token' => $token], Response::HTTP_OK);
        }
        return response()->json([
            'message' => 'メールアドレスもしくはパスワードが間違っています。'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // ログアウト
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json('Logout successful.', 200);
    }

    // パスワード再設定メールを送信
    public function sendPasswordResetEmail(SendPasswordResetEmailRequest $request)
    {
        // 送信されたメールアドレスのユーザーを取得
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found.']);
        }
        // トークンを生成
        $token = Password::broker()->createToken($user);
        // トークンの有効期限を設定
        $now = Carbon::now();
        $expire_at = $now->addHour(1)->toDateTimeString();
        // トークンをDBに保存
        UserToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expire_at' => $expire_at,
        ]);
        // 実際にメールを送信する処理
        $user->sendPasswordResetNotification($token);
        // レスポンスを返す
        return new JsonResponse([
            'token' => $token,
            'mail_sent' => true,
        ]);
    }

    // 実際にトークンの検証を行って、結果を返す関数
    private function doVerifyTokenAndEmail($token, $email)
    {
        // 送信されたトークンがDBに存在するか確認
        $dbToken = UserToken::where('token', $token)->first();
        if (!$dbToken) {
            return ['success' => false, 'message' => 'The token is invalid.'];
        }
        // 送信されたメールアドレスからユーザーを取得
        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found.'];
        }
        // 現在の時刻がトークンの有効期限を過ぎていないかを確認
        $now = Carbon::now();
        if ($now->gt($dbToken->expire_at)) {
            return ['success' => false, 'message' => 'The token has expired.'];
        }
        // トークンのユーザーIDとメールアドレスのユーザーIDが一致するか確認
        if ($dbToken->user_id != $user->id) {
            return ['success' => false, 'message' => 'The token is invalid.'];
        }
        // 検証が成功した場合
        return ['success' => true];
    }


    // パスワード再設定画面でトークンとメールアドレスを検証する処理
    public function verifyTokenAndEmail(Request $request)
    {
        // トークンとメールアドレスの検証を行う
        $result = $this->doVerifyTokenAndEmail($request->token, $request->email);
        if (!$result['success']) {
            return new JsonResponse(['message' => $result['message']]);
        }
        // 検証が成功した場合
        return new JsonResponse([
            'token' => $request->token,
            'verified' => true,
        ]);
    }

    // パスワードの変更
    public function updatePassword(UpdatePasswordRequest $request)
    {
        // トークンとメールアドレスの検証を行う
        $result = $this->doVerifyTokenAndEmail($request->token, $request->email);
        if (!$result['success']) {
            return new JsonResponse(['message' => $result['message']]);
        }
        // 検証が成功した場合はパスワードを変更する
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        // パスワードを変更したらトークンは削除する
        UserToken::where('token', $request->token)->delete();
        // レスポンスを返す
        return new JsonResponse([
            'message' => 'Password updated.'
        ]);
    }
}

