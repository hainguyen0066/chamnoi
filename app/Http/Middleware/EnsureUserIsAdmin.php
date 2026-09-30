<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chặn mọi route /admin/* trừ khi user đã đăng nhập có role=admin.
 * Đăng ký alias 'admin' trong bootstrap/app.php, dùng chung với 'auth'.
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}
