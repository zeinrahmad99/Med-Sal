<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserUpdateRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => 1,
            'users' => $users,
        ]);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();


        return response()->json([
            'status' => 1,
            'user' => $user,
        ]);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        $data = $request->all();

        if (!$data) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        if ($request->has('email') && $user->email !== $request->input('email')) {
            $user->email_verified_at = null;
        }

        $user->update($data);

        return response()->json([
            'status' => 1,
            'data' => $user,
        ]);
    }


    public function delete(int $id)
    {

        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        $user = $user->delete();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        return response()->json([
            'status' => 1,
        ]);
    }
}
