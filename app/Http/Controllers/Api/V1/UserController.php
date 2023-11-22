<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Http\Request;
use App\Traits\Api\V1\UserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserUpdateRequest;

class UserController extends Controller
{
    use UserAction;

    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => $users ? 1 : 0,
            'data' => $users,
        ]);
    }


    public function show($id)
    {
        $user = User::firstwhere('id', $id);

        return response()->json([
            'status' => $user ? 1 : 0,
            'data' => $user,
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
            $this->resetEmailVerification($user);
        }

        $user->update($data);

        return response()->json([
            'status' => $user ? 1 : 0,
            'data' => $user,
        ]);
    }

    public function delete(int $id)
    {

        $user = User::firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        $deleted = $user->delete();

        return response()->json([
            'status' => $deleted ? 1 : 0,
        ]);
    }
}
