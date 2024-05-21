<?php

namespace App\Http\Requests\Auth\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        /*
         * admin ガードを指定、login_id と password を使って認証
         * 成功したらセッションにユーザ情報(Adminオブジェクト)が保存される
         */
        if (!Auth::guard('admin')->attempt($this->only('login_id', 'password'))) {
            // 失敗した場合は、login_id のバリデーションエラーとして処理
            throw ValidationException::withMessages([
                'login_id' => trans('auth.failed'),
            ]);
        }
    }
}
