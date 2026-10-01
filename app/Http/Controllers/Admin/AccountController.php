<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Danh sách tài khoản hệ thống (Admin & User).
     */
    public function index(Request $request): View
    {
        $stats = [
            'total'  => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users'  => User::where('role', 'user')->count(),
        ];

        $accounts = User::query()
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->where('role', $request->string('role'));
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->string('q');
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.accounts.index', compact('accounts', 'stats'));
    }

    /**
     * Giao diện tạo mới tài khoản.
     */
    public function create(): View
    {
        return view('admin.accounts.create');
    }

    /**
     * Lưu tài khoản mới (Admin hoặc User).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:admin,user'],
        ], [
            'name.required'      => 'Vui lòng nhập họ tên hoặc tên hiển thị.',
            'name.max'           => 'Họ tên không được vượt quá 100 ký tự.',
            'email.required'     => 'Vui lòng nhập địa chỉ email.',
            'email.email'        => 'Email không đúng định dạng.',
            'email.unique'       => 'Địa chỉ email này đã được sử dụng.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
            'role.required'      => 'Vui lòng chọn quyền hạn (Admin hoặc User).',
            'role.in'            => 'Quyền hạn không hợp lệ.',
        ]);

        $hashedPassword = Hash::make($validated['password']);

        if ($validated['role'] === 'admin') {
            // Tạo tài khoản Admin cho guard admin đăng nhập
            Admin::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $hashedPassword,
            ]);

            // Đồng bộ sang bảng users
            $user = new User([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => $hashedPassword,
            ]);
            $user->forceFill([
                'role'              => 'admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ])->save();

            return redirect()->route('admin.accounts.index')->with('status', "Đã tạo tài khoản Quản trị viên (Admin) [{$validated['email']}] thành công!");
        }

        // Tạo tài khoản User thông thường
        $user = new User([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => $hashedPassword,
        ]);
        $user->forceFill([
            'role'              => 'user',
            'status'            => 'active',
            'email_verified_at' => now(),
        ])->save();

        return redirect()->route('admin.accounts.index')->with('status', "Đã tạo tài khoản Người dùng (User) [{$validated['email']}] thành công!");
    }

    /**
     * Khóa / Mở khóa tài khoản.
     */
    public function toggleStatus(User $account): RedirectResponse
    {
        $currentAdmin = Auth::guard('admin')->user();
        if ($currentAdmin && $currentAdmin->email === $account->email) {
            return back()->with('error', 'Bạn không thể khóa tài khoản của chính mình!');
        }

        $newStatus = $account->status === 'active' ? 'locked' : 'active';
        $account->forceFill(['status' => $newStatus])->save();

        $msg = $newStatus === 'locked' ? 'Đã khóa tài khoản' : 'Đã mở khóa tài khoản';
        return back()->with('status', "{$msg} [{$account->email}] thành công!");
    }

    /**
     * Xóa tài khoản.
     */
    public function destroy(User $account): RedirectResponse
    {
        $currentAdmin = Auth::guard('admin')->user();
        if ($currentAdmin && $currentAdmin->email === $account->email) {
            return back()->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        $email = $account->email;

        // Nếu là admin, xóa cả trong bảng admins
        if ($account->role === 'admin') {
            Admin::where('email', $email)->delete();
        }

        $account->delete();

        return back()->with('status', "Đã xóa tài khoản [{$email}] thành công!");
    }
}
