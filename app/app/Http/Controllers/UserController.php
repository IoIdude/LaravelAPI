<?php

namespace App\Http\Controllers;

use App\Mail\Feedback;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function auth(Request $request)
    {
        $code = random_int(1000, 1999);
        $email = $request->email;

        $params = [
            'code' => $code
        ];

        Mail::to($email)->send(new Feedback($params));

        return response(['code' => $code, 'email' => $email]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request) // Сохранение данных
    {
        $user_code = $request->user_code;
        $code = $request->code;

        if ($user_code != $code)
        {
            return response(['error' => 'Коды не совпедают']);
        }

        $db_user = User::where('email', $request->email)->first();
        if ($db_user == null) {
            $user = User::create(['email' => $request->email, 'role_id' => $request->role_id]);
            $user['token'] = $user->createToken('token')->plainTextToken;
            return response($user);
        }

        $db_user['token'] = $db_user->createToken('token')->plainTextToken;

        return response($db_user);
    }

    public function showUsers()
    {
        $users = User::with('role')->get();
        return response(['users' => $users]);
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->id);
        $role = Role::where('name', 'Admin')->first();
        $check_role_count = 2;

        if ($user->role_id == $role->id) {
            $check_role_count = User::where('role_id', $role->id)->get()->count();
        }

        if ($user != null && ($check_role_count - 1) != 0) {
            $user->delete();

            return response(['success' => 'Успешное удаление пользователя']);
        }

        return response(['error' => 'Ошибка удаления пользователя']);
    }

    public function updateUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $check_email = User::where('email', $request->new_email)->first();

        if ($user != null) {
            if ($user->role_id != $request->role_id)
            {
                $user->update(['role_id' => $request->role_id]);
            }

            if ($check_email == null)
            {
                $user->update(['email' => $request->new_email]);
            }

            return response($user);
        }

        return response(['error' => 'Такой пользователь уже существует']);
    }

    public function createUser(Request $request)
    {
        $user_in_db = User::where('email', $request->email)->first();

        if ($user_in_db == null) {
            $user = User::create(['email' => $request->email, 'role_id' => $request->role_id]);

            return response($user);
        }

        return response(['error' => 'Такой пользователь уже существует']);
    }

    public function getUserByEmail(Request $request)
    {
        $user_in_db = User::where('email', $request->email)->first();

        if ($user_in_db != null) {
            return response($user_in_db);
        }

        return response(null);
    }
}
