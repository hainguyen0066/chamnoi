# Game API Management Panel

Hệ thống quản lý tài trị, xác thực đa yếu tố (2FA) và tích hợp GameServer Kick API.

---

## Tính Năng Chính

1. **Kick Người Chơi (GameServer API v2)**
   - Kick tài khoản hoặc nhân vật theo thời gian thực kết nối trực tiếp GameServer qua HMAC-SHA256 signature.
   - Hỗ trợ xử lý hàng loạt (Batch Kick) theo danh sách dòng:
     - Tự động chạy nền qua Queue Worker hoặc xử lý trực tiếp (đồng bộ) phù hợp cho cả VPS lẫn Shared Hosting.
     - Kiểm tra trạng thái xử lý trực tiếp theo thời gian thực (Live Status Checking).
   - Nhật ký lịch sử Kick chi tiết (`game_kick_logs`), hiển thị mã phản hồi HTTP, payload và trạng thái thành công/thất bại.

2. **Quản Lý Tài Khoản (Admin & User)**
   - Danh sách tài khoản hệ thống với bộ lọc vai trò (Admin / User) và trạng thái (Active / Locked).
   - Thêm tài khoản mới, phân quyền truy cập.
   - Bật / tắt khóa tài khoản nhanh chóng.
   - Xóa tài khoản an toàn với cơ chế chặn tự xóa chính mình.

3. **Bảo Mật 2FA (Two-Factor Authentication)**
   - Tích hợp Google Authenticator (TOTP chuẩn RFC 6238).
   - Hỗ trợ mã khôi phục dự phòng (Recovery Codes) khi mất thiết bị.
   - Middleware và Flow đăng nhập 2 bước bảo vệ toàn diện bảng quản trị.

---

## Cấu Hình Môi Trường (.env)

```env
APP_NAME="GAME API PANEL"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=game_api_panel
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_COOKIE=game_admin_session

# Queue (database hoặc sync tùy theo hosting/vps)
QUEUE_CONNECTION=database

# GameServer Kick API
GAME_KICK_API_URL=http://103.206.216.8:8090/v2/kick.php
GAME_KICK_STATUS_API_URL=http://103.206.216.8:8090/v2/kick_status.php
GAME_KICK_KEY=your_secret_key_here
```

---

## Cài Đặt & Khởi Chạy

```bash
# 1. Cài đặt thư viện
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 2. Khởi tạo cấu hình và Database
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder

# 3. Chạy Queue Worker (nếu dùng hàng đợi)
php artisan queue:work --tries=3
```
