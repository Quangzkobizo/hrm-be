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
        // Lấy người dùng hiện tại
        $currentUser = Auth::user();

        // Mảng các vai trò thấp hơn
        $lowerRoles = [];

        if ($currentUser['role'] == 'manager') {
            $lowerRoles[] = 'employee';
        } else
        if ($currentUser['role'] == 'admin') {
            $lowerRoles[] = 'manager';
            $lowerRoles[] = 'employee';
        }

        // Lấy danh sách người dùng có vai trò nhỏ hơn của người dùng hiện tại
        $users = User::whereIn('role', $lowerRoles)->get();
        // $users = User::all();

        // Trả về JSON chứa danh sách người dùng
        return response()->json($users);
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
    public function update(Request $request, $updateId)
    {
        //Kiểm tra quyền update
        $loggingUser = User::find(Auth::user()->id);
        $updateUser = User::find($updateId);

        if (!$this->canUpdate($loggingUser, $updateUser)) {
            return response()->json(["message" => "No permission"], 403);
        }

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
        $updateUser->update($userData);

        return response()->json([
            'status' => 'success',
            'changes' => $updateUser->getChanges(),
        ], 200);
    }

    //Phương thức update avatar
    public function updateAvatar(Request $request, $updateId)
    {
        $loggingUser = User::find(Auth::user()->id);
        $updateUser = User::find($updateId);

        if (!$this->canUpdate($loggingUser, $updateUser)) {
            return response()->json(["message" => "No permission"], 403);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        //save new avatar file
        $avatar = $request->file('avatar');
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatarPath = $request->file('avatar')->storeAs('public/avatars', $avatarName);

        //delete old avatar file
        $oldAvatar = $updateUser->avatar;
        if ($oldAvatar) {
            if (Storage::disk('public')->exists('avatars/' . $oldAvatar)) {
                Storage::disk('public')->delete('avatars/' . $oldAvatar);
            }
        }

        $updateUser->update(['avatar' => $avatarName]);

        return response()->json(['success' => 'Image uploaded successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    //Quangz: xem xem người dùng A có quyền chỉnh sửa thông tin người dùng B không
    private function canUpdate(User $A, User $B)
    {
        //Chinh sửa chính mình
        if ($A->id == $B->id) return true;

        //Được chỉnh sửa nếu có vai trò cao hơn
        if ($A->role == 'admin' && ($B->role == 'manager' || $B->role == 'employee')) {
            return true;
        }
        if ($A->role == 'manager' && $B->role == 'employee') {
            return true;
        }

        return false;
    }
}
