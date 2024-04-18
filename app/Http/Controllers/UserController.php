<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show1(User $user)
    {
        //
    }

    public function show()
    {
        return response()->json([
            'status' => 'success',
            'user' => User::where('id', '=', Auth::id())->first()
        ]);
    }


    /**
     * Quangz: Upload user data, except Avatar*
     */
    public function update(Request $request)
    {

        // Validate dữ liệu từ form
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'birthDate' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            // 'avatar' => xử lí ở phuong thức updateAvatar
        ]);

        // Lấy thông tin từ request
        $userData = $request->only(['name', 'phone', 'address', 'birthDate', 'gender']);

        // Cập nhật thông tin người dùng
        $user = User::find(Auth::user()->id);
        $user->update($userData);
        // dd($user->getChanges());

        return response()->json([
            'status' => 'success',
            'changes' => $user->getChanges(),
        ], 200);
    }

    public function updateAvatar(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        //save new avatar file
        $avatar = $request->file('avatar');
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatarPath = $request->file('avatar')->storeAs('public/avatars', $avatarName);

        //delete old avatar file
        $oldAvatar = $user->avatar;
        if ($oldAvatar) {
            if (Storage::disk('public')->exists('avatars/' . $oldAvatar)) {
                Storage::disk('public')->delete('avatars/' . $oldAvatar);
            }
        }

        $user->update(['avatar' => $avatarName]);

        return response()->json(['success' => 'Image uploaded successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
