# CẨM NANG HƯỚNG DẪN HỆ THỐNG TỪ A ĐẾN Z
### Game API Management Panel & Triển Khai VPS

Tài liệu này được biên soạn chi tiết, dễ hiểu dành cho người mới bắt đầu. Giúp bạn hiểu rõ toàn bộ cấu trúc hệ thống, cách thức vận hành và quản trị bảng điều khiển Game API Panel trên VPS.

---

## 📌 PHẦN 1: BỨC TRANH TỔNG THỂ HỆ THỐNG

### 1. Sơ Đồ Hoạt Động (Architecture)

```text
[Trình Duyệt Của Bạn]
        │
        ▼ (Port 8080)
┌─────────────────────────────────────────────────────────────┐
│ VPS Ubuntu 24.04 (IP: 103.206.218.215)                      │
│                                                             │
│  ├── [Nginx Port 8080] ──> Tiếp nhận kết nối Web            │
│  │                              │                           │
│  │                              ▼ (FastCGI Socket)          │
│  ├── [PHP 8.3 FPM] ───────> Xử lý mã nguồn Laravel          │
│  │                              │                           │
│  ├── [MariaDB 10.11] ◄──────────┘ Lưu trữ Users & Lịch sử   │
│  │   (Database: game_api_panel)                             │
│  │                                                          │
│  ├── [Supervisor] ────────> Giám sát Queue Worker 24/7      │
│  │                              │                           │
│  └── [Nginx Port 443] ────> Giữ nguyên Logger cũ (An toàn)  │
└─────────────────────────────────┬───────────────────────────┘
                                  │
                                  ▼ (Gọi API qua HMAC-SHA256)
               [GameServer Chính: 103.206.216.8:8090]
                      (Đá văng tài khoản/nhân vật)
```

### 2. Nguyên Tắc An Toàn Với Server Cũ
- Trên VPS vốn có sẵn hệ thống **Logger** (lắng nghe trên cổng **443** qua tên miền `log.tinhtrongthienha.vn`).
- Chúng ta triển khai Game API Panel trên cổng **8080** và tạo CSDL riêng biệt (`game_api_panel`), nhờ đó **hai hệ thống hoàn toàn độc lập, không xung đột và không làm ảnh hưởng đến dữ liệu cũ của anh bạn**.

---

## 📌 PHẦN 2: THÔNG TIN TRUY CẬP & TÀI KHOẢN

| Mục | Chi tiết |
| :--- | :--- |
| **Đường dẫn Web** | [http://103.206.218.215:8080/admin/login](http://103.206.218.215:8080/admin/login) |
| **Tài khoản 1** | Email: `admin@volam.local` \| Mật khẩu: `password123` |
| **Tài khoản 2** | Email: `admin@example.com` \| Mật khẩu: `password123` |
| **IP VPS** | `103.206.218.215` (Cổng SSH: `53122`, User: `root`) |
| **Thư mục Web trên VPS** | `/var/www/game-api-panel/` |
| **Database trên VPS** | Tên: `game_api_panel` \| User: `game_admin` |

---

## 📌 PHẦN 3: BA TÍNH NĂNG CHÍNH ĐANG HOẠT ĐỘNG

### 1. Kick Người Chơi (GameServer API v2)
- **Kick đơn lẻ (1 tài khoản)**:
  - Nhập tên ➔ Bấm **Xác Nhận Kick Ngay**.
  - Hệ thống gọi trực tiếp GameServer `103.206.216.8:8090`, có kết quả ngay sau 2 - 3 giây (Thành công / Không online / Lỗi).
- **Kick hàng loạt (Nhiều tài khoản)**:
  - Nhập nhiều dòng (mỗi dòng 1 tên hoặc cách nhau bằng dấu phẩy).
  - Nút bấm tự chuyển thành: **Đưa [N] Tài Khoản Vào Hàng Đợi (Queue VPS)**.
  - Hệ thống đẩy danh sách vào Queue, worker chạy ngầm trên VPS sẽ kick lần lượt, tự giãn cách 2 giây/acc chống rate limit.
  - **Cơ chế tự động dứt điểm trạng thái**: Nếu GameServer cần quét map, hệ thống tự động hỏi lại qua `kick_status.php` để lấy kết quả cuối cùng, **không bao giờ bị kẹt ở "Đang xử lý"**.

### 2. Quản Lý Tài Khoản (Admin & User)
- Thêm tài khoản mới, phân quyền truy cập:
  - **Admin**: Được đăng nhập vào trang quản trị (`/admin`).
  - **User**: Tài khoản thông thường.
- Khóa / Mở khóa tài khoản nhanh chỉ với 1 click.
- Xóa tài khoản an toàn (hệ thống chặn tự xóa chính mình).

### 3. Cài Đặt Bảo Mật 2FA (Google Authenticator)
- Quét mã QR bằng ứng dụng Google Authenticator hoặc Authy trên điện thoại.
- Nhập mã 6 số để kích hoạt.
- Tự động sinh ra 8 mã khôi phục dự phòng (Recovery Codes) dùng khi mất điện thoại.

---

## 📌 PHẦN 4: VÌ SAO GÕ `./deploy.sh` LÀ CODE TỰ ĐỘNG LÊN VPS?

Bạn thắc mắc vì sao chỉ chạy 1 lệnh `./deploy.sh` mà code tự động cập nhật lên VPS mượt mà? Dưới đây là "phép màu" đằng sau:

### 1. Kết Nối Không Cần Nhập Mật Khẩu (SSH Key Authentication)
- Máy tính của bạn có một "chìa khóa số" bí mật (`id_ed25519`).
- Chúng ta đã copy "ổ khóa công khai" (`id_ed25519.pub`) đưa vào file `~/.ssh/authorized_keys` trên VPS.
- Do đó, máy tính của bạn khi kết nối sang VPS sẽ được nhận diện tự động và đăng nhập ngay lập tức mà không cần gõ mật khẩu.

### 2. Công Cụ Đồng Bộ Rsync Thông Minh
- Khác với FTP phải tải lên lại toàn bộ file, lệnh `rsync` chỉ quét và so sánh các file bạn **vừa sửa đổi** rồi đẩy đúng các phần thay đổi đó sang VPS (mất khoảng 1 - 2 giây).
- File nhạy cảm trên VPS như cấu hình Database `.env` được bảo vệ, không bao giờ bị ghi đè.

### 3. Tự Động Kích Hoạt Dịch Vụ Sau Khi Chép Code
Script tự động gửi các lệnh sang VPS:
1. `php artisan migrate --force`: Cập nhật bảng CSDL nếu có migration mới.
2. `php artisan config:clear && php artisan view:clear`: Xóa file cache cũ để nạp giao diện mới.
3. `php artisan queue:restart`: Báo cho Supervisor khởi động lại Worker để áp dụng code mới vào hàng đợi.

---

## 📌 PHẦN 5: CẨM NANG CÁC LỆNH QUẢN TRỊ CẦN BIẾT

### 1. Cách Đăng Nhập Vào VPS Bằng Terminal
Mở terminal trên máy tính và gõ:
```bash
ssh -p 53122 root@103.206.218.215
```
*(Đã cài sẵn SSH Key nên bấm Enter là vào thẳng, không cần nhập mật khẩu).*

### 2. Kiểm Tra Hàng Đợi (Queue Worker)
```bash
# Xem trạng thái worker có đang chạy không:
supervisorctl status

# Khởi động lại worker:
supervisorctl restart game-api-worker:*

# Xem nhật ký xử lý của worker:
tail -f /var/www/game-api-panel/storage/logs/worker.log
```

### 3. Kiểm Tra Web Server (Nginx & PHP-FPM)
```bash
# Kiểm tra Nginx có chạy tốt không:
systemctl status nginx

# Nạp lại cấu hình Nginx sau khi chỉnh sửa:
nginx -t && systemctl reload nginx

# Xem trạng thái PHP:
systemctl status php8.3-fpm
```

### 4. Xem Lịch Sử Lỗi Laravel (Nêu Web Bị Lỗi)
```bash
tail -n 50 /var/www/game-api-panel/storage/logs/laravel.log
```

### 5. Sao Lưu CSDL (Backup Database)
Nếu muốn sao lưu CSDL ra file:
```bash
mysqldump -u game_admin -p'GameApiPass2026!' game_api_panel > ~/backup_game_api_$(date +%F).sql
```

---

## 📌 PHẦN 6: XỬ LÝ SỰ CỐ NHANH (TROUBLESHOOTING)

### Sự cố 1: Web bị báo lỗi 502 Bad Gateway
- **Nguyên nhân**: Dịch vụ PHP-FPM bị tạm dừng.
- **Cách khắc phục**:
  ```bash
  ssh -p 53122 root@103.206.218.215 "systemctl restart php8.3-fpm"
  ```

### Sự cố 2: Kick hàng loạt nhưng không thấy chạy
- **Nguyên nhân**: Worker của Supervisor bị dừng.
- **Cách khắc phục**:
  ```bash
  ssh -p 53122 root@103.206.218.215 "supervisorctl restart game-api-worker:*"
  ```

### Sự cố 3: Quên mật khẩu đăng nhập Admin
- **Cách khắc phục**: Chạy lệnh này trên terminal máy bạn để đặt lại mật khẩu thành `password123`:
  ```bash
  ssh -p 53122 root@103.206.218.215 "cd /var/www/game-api-panel && php artisan tinker --execute=\"
  \$admin = App\Models\Admin::where('email', 'admin@volam.local')->first();
  if (\$admin) {
      \$admin->password = Hash::make('password123');
      \$admin->save();
      echo 'Mật khẩu đã đặt lại thành: password123';
  }
  \""
  ```

---

## 📌 TỔNG KẾT QUY TRÌNH HÀNG NGÀY CHO BẠN

Từ giờ về sau, công việc của bạn chỉ đơn giản là:
1. Mở code ở máy tính, chỉnh sửa tính năng theo ý muốn.
2. Mở terminal, chạy lệnh:
   ```bash
   ./deploy.sh "Mô tả tính năng vừa sửa"
   ```
3. Mở trình duyệt [http://103.206.218.215:8080](http://103.206.218.215:8080) để thấy thành quả ngay lập tức!
