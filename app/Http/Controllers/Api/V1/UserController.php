<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Http\Request;
use App\Traits\Api\V1\UserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserUpdateRequest;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use UserAction;

    public function index()
    {
        try {
            Gate::authorize('isSuperAdmin');
            $users = User::all();

            return response()->json([
                'status' => 1,
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 10,
            ]);
        }
    }

    public function show($id)
    {
        try {
            $user = User::firstwhere('id', $id);
            $this->authorize('show', $user);
            return response()->json([
                'status' => 1,
                'data' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::firstWhere('id', $id);
            $this->authorize('show', $user);
            $data = $request->all();
            if ($request->has('email') && $user->email !== $request->input('email')) {
                $this->resetEmailVerification($user);
            }

            $user->update($data);

            return response()->json([
                'status' => 1,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
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
