# 📋 NHẬT KÝ CHI TIẾT TỪNG LỆNH (COMMANDS) ĐÃ CHẠY ĐỂ CÀI ĐẶT VPS TỪ A ĐẾN Z

> Tài liệu này tổng hợp **100% tất cả các câu lệnh terminal thực tế** đã được thực thi trên máy cá nhân và VPS `103.206.218.215`, giải thích cặn kẽ tại sao phải chạy lệnh đó, giúp bạn có thể tự tay làm lại trên bất kỳ con VPS nào khác trong tương lai.

---

## 🧭 MỤC LỤC CÁC BƯỚC THỰC HIỆN

1. [Bước 1: Thiết lập SSH Key kết nối VPS không cần gõ mật khẩu](#bước-1-thiết-lập-ssh-key-kết-nối-vps-không-cần-gõ-mật-khẩu)
2. [Bước 2: Khảo sát VPS để không làm hỏng dịch vụ Logger của Sếp](#bước-2-khảo-sát-vps-để-không-làm-hỏng-dịch-vụ-logger-của-sếp)
3. [Bước 3: Cài đặt PHP 8.3, Composer và MariaDB](#bước-3-cài-đặt-php-83-composer-và-mariadb)
4. [Bước 4: Tạo Cơ sở dữ liệu và Cấp quyền User Database](#bước-4-tạo-cơ-sở-dữ-liệu-và-cấp-quyền-user-database)
5. [Bước 5: Đưa mã nguồn Laravel lên thư mục web VPS](#bước-5-đưa-mã-nguồn-laravel-lên-thư-mục-web-vps)
6. [Bước 6: Cấu hình file .env & Khởi tạo Database cho Web](#bước-6-cấu-hình-file-env--khởi-tạo-database-cho-web)
7. [Bước 7: Cấu hình Nginx Web Server chạy cổng 8080](#bước-7-cấu-hình-nginx-web-server-chạy-cổng-8080)
8. [Bước 8: Mở tường lửa (UFW Firewall) cho cổng 8080](#bước-8-mở-tường-lửa-ufw-firewall-cho-cổng-8080)
9. [Bước 9: Cài đặt Supervisor giữ cho Queue Worker chạy vĩnh viễn 24/7](#bước-9-cài-đặt-supervisor-giữ-cho-queue-worker-chạy-vĩnh-viễn-247)
10. [Bước 10: Phân quyền bảo mật & Tối ưu bộ nhớ đệm (Cache)](#bước-10-phân-quyền-bảo-mật--tối-ưu-bộ-nhớ-đệm-cache)
11. [Bước 11: Tạo file deploy.sh để cập nhật web bằng 1 cú click](#bước-11-tạo-file-deploysh-để-cập-nhật-web-bằng-1-cú-click)

---

## BƯỚC 1: THIẾT LẬP SSH KEY KẾT NỐI VPS KHÔNG CẦN GÕ MẬT KHẨU

*(Chạy trên Terminal máy tính của bạn)*

```bash
# 1. Tạo cặp khóa SSH chuẩn Ed25519 (mạnh và an toàn nhất hiện nay)
ssh-keygen -t ed25519 -C "admin-deploy-key"
# (Khi máy hỏi lưu ở đâu, bấm Enter liên tục 3 lần để chọn mặc định)

# 2. Đẩy chìa khóa công khai lên VPS qua cổng 53122
ssh-copy-id -p 53122 root@103.206.218.215
# (Nhập mật khẩu VPS: r75+1!zMnwCN.@9A@pvQLz3m đúng 1 lần duy nhất)

# 3. Kiểm tra xem đã vào thẳng được chưa (không bị đòi mật khẩu nữa)
ssh -p 53122 root@103.206.218.215 "echo 'Kết nối SSH thành công không cần pass!'"
```

---

## BƯỚC 2: KHẢO SÁT VPS ĐỂ KHÔNG LÀM HỎNG DỊCH VỤ LOGGER CỦA SẾP

*(Trước khi cài bất cứ thứ gì, phải kiểm tra các cổng đang chạy trên VPS)*

```bash
# Đăng nhập vào VPS
ssh -p 53122 root@103.206.218.215

# Kiểm tra các cổng mạng đang mở
ss -tulnp
```
*Kết quả khảo sát thực tế:*
- Thấy cổng `443` đang chạy Nginx phục vụ `log.tinhtrongthienha.vn`.
- Thấy thư mục `/var/www/jxlog` và `/var/www/jxlog_view` của sếp.
- **Quyết định an toàn:** Tuyệt đối không đụng vào cổng 80/443 và các thư mục `jxlog`. Website Game API Panel sẽ được đặt tại `/var/www/game-api-panel` và chạy riêng trên cổng `8080`.

---

## BƯỚC 3: CÀI ĐẶT PHP 8.3, COMPOSER VÀ MARIADB

*(Chạy bên trong VPS)*

```bash
# 1. Cập nhật danh sách phần mềm của hệ điều hành Ubuntu
apt update && apt upgrade -y

# 2. Cài thêm các tiện ích mở rộng cần thiết của PHP 8.3 cho Laravel
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl \
php8.3-xml php8.3-zip php8.3-bcmath php8.3-mbstring php8.3-intl

# 3. Cài đặt Composer (trình quản lý gói thư viện PHP)
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Kiểm tra Composer đã nhận chưa
composer -V

# 4. Cài đặt Hệ quản trị cơ sở dữ liệu MariaDB (MySQL mã nguồn mở)
apt install -y mariadb-server
systemctl enable mariadb
systemctl start mariadb

# 5. Cài đặt Supervisor (bộ giám sát chạy ngầm 24/7)
apt install -y supervisor
systemctl enable supervisor
systemctl start supervisor
```

---

## BƯỚC 4: TẠO CƠ SỞ DỮ LIỆU VÀ CẤP QUYỀN USER DATABASE

*(Chạy bên trong VPS)*

```bash
# Mở bảng điều khiển MariaDB bằng quyền root
mariadb
```

Dán lần lượt các câu lệnh SQL sau rồi gõ Enter:

```sql
-- 1. Tạo cơ sở dữ liệu tên là game_api_panel
CREATE DATABASE IF NOT EXISTS game_api_panel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Tạo tài khoản người dùng game_admin với mật khẩu GameApiPass2026!
CREATE USER IF NOT EXISTS 'game_admin'@'127.0.0.1' IDENTIFIED BY 'GameApiPass2026!';

-- 3. Cấp toàn bộ quyền quản lý database game_api_panel cho user này
GRANT ALL PRIVILEGES ON game_api_panel.* TO 'game_admin'@'127.0.0.1';

-- 4. Áp dụng ngay lập tức các quyền vừa cấp
FLUSH PRIVILEGES;

-- 5. Thoát khỏi MariaDB
EXIT;
```

---

## BƯỚC 5: ĐƯA MÃ NGUỒN LARAVEL LÊN THƯ MỤC WEB VPS

### 1. Tạo thư mục trên VPS:
```bash
# Trên VPS:
mkdir -p /var/www/game-api-panel
```

### 2. Đóng gói & Đẩy code từ máy tính lên VPS:
*(Chạy trên Terminal máy tính cá nhân)*
```bash
cd /home/hainguyen/Desktop/APITTH

# Build giao diện CSS/JS trước
npm run build

# Đồng bộ toàn bộ code sang VPS qua rsync (bỏ qua rác và file môi trường riêng)
rsync -avz \
  -e "ssh -p 53122" \
  --exclude='.git/' \
  --exclude='node_modules/' \
  --exclude='.env' \
  --exclude='storage/logs/*.log' \
  ./ root@103.206.218.215:/var/www/game-api-panel/
```

---

## BƯỚC 6: CẤU HÌNH FILE .ENV & KHỞI TẠO DATABASE CHO WEB

*(Chạy bên trong VPS tại thư mục `/var/www/game-api-panel`)*

```bash
cd /var/www/game-api-panel

# Cài đặt toàn bộ thư viện vendor của Laravel (tối ưu tốc độ production)
composer install --no-dev --optimize-autoloader

# Tạo file cấu hình môi trường .env
cp .env.example .env
```

Mở file `.env` bằng lệnh `nano .env` và cấu hình các dòng thông số chuẩn:
```env
APP_NAME="GAME API PANEL"
APP_ENV=production
APP_KEY=base64:755Zt936Hq7kK6s96O7+XbW0FwD1iTqJq/Vb5vH9s=
APP_DEBUG=false
APP_URL=http://103.206.218.215:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=game_api_panel
DB_USERNAME=game_admin
DB_PASSWORD=GameApiPass2026!

QUEUE_CONNECTION=database

GAMESERVER_API_URL=http://103.206.216.8:8090/v2/kick.php
GAMESERVER_STATUS_URL=http://103.206.216.8:8090/v2/kick_status.php
GAMESERVER_SECRET_KEY=cc72b1c30b7bffad27935e8c78e5500b3fc01cf81b40beae
```

Khởi tạo các bảng cơ sở dữ liệu và nạp tài khoản admin mẫu:
```bash
# Tạo các bảng cơ sở dữ liệu & nạp sẵn tài khoản Admin mẫu
php artisan migrate --force --seed
```

---

## BƯỚC 7: CẤU HÌNH NGINX WEB SERVER CHẠY CỔNG 8080

*(Chạy bên trong VPS)*

Tạo file cấu hình trang web:
```bash
cat << 'EOF' > /etc/nginx/sites-available/game-api-panel.conf
server {
    listen 8080;
    server_name _;
    root /var/www/game-api-panel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
```

Kích hoạt trang web và nạp lại Nginx:
```bash
# Tạo liên kết tượng trưng (symlink) sang thư mục sites-enabled
ln -sf /etc/nginx/sites-available/game-api-panel.conf /etc/nginx/sites-enabled/

# Kiểm tra xem cú pháp cấu hình Nginx có chuẩn không
nginx -t

# Nạp lại Nginx để mở cổng 8080 ngay lập tức (không làm gián đoạn web cũ)
systemctl reload nginx
```

---

## BƯỚC 8: MỞ TƯỜNG LỬA (UFW FIREWALL) CHO CỔNG 8080

*(Chạy bên trong VPS)*

```bash
# Mở cổng 8080 để khách từ internet có thể truy cập vào web
ufw allow 8080/tcp

# Kiểm tra trạng thái tường lửa
ufw status
```

---

## BƯỚC 9: CÀI ĐẶT SUPERVISOR GIỮ CHO QUEUE WORKER CHẠY VĨNH VIỄN 24/7

*(Tiến trình này giúp xử lý hàng đợi Kick tài khoản ngầm, không bao giờ bị tắt)*

Tạo file cấu hình Supervisor:
```bash
cat << 'EOF' > /etc/supervisor/conf.d/game-api-worker.conf
[program:game-api-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php8.3 /var/www/game-api-panel/artisan queue:work --tries=3 --sleep=3 --timeout=120
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/game-api-panel/storage/logs/worker.log
stopwaitsecs=3600
EOF
```

Kích hoạt và khởi động Worker:
```bash
# Đọc file cấu hình mới
supervisorctl reread

# Áp dụng cấu hình và tạo tiến trình
supervisorctl update

# Kiểm tra trạng thái tiến trình
supervisorctl status
```
*Kết quả:* Hiện `game-api-worker:game-api-worker_00 RUNNING` là hoàn thành!

---

## BƯỚC 10: PHÂN QUYỀN BẢO MẬT & TỐI ƯU BỘ NHỚ ĐỆM (CACHE)

*(Chạy bên trong VPS)*

```bash
# Trao quyền sở hữu toàn bộ thư mục web cho tài khoản Nginx (www-data)
chown -R www-data:www-data /var/www/game-api-panel

# Cấp quyền ghi vào thư mục storage và cache (tránh lỗi 500 trắng trang)
chmod -R 775 /var/www/game-api-panel/storage /var/www/game-api-panel/bootstrap/cache

# Khóa quyền file .env để không ai có thể xem trộm mật khẩu database
chmod 640 /var/www/game-api-panel/.env

# Tối ưu tốc độ Laravel cho môi trường Production (tăng tốc độ gấp 3 lần)
cd /var/www/game-api-panel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## BƯỚC 11: TẠO FILE `deploy.sh` ĐỂ CẬP NHẬT WEB BẰNG 1 CÚ CLICK

Để từ nay về sau không cần phải gõ lại hàng chục lệnh phức tạp ở trên, chúng ta gom toàn bộ quy trình đẩy code thành 1 file duy nhất `deploy.sh` đặt ngay tại thư mục dự án trên máy tính:

File `deploy.sh`:
```bash
#!/usr/bin/env bash
set -e

MSG=${1:-"Cập nhật hệ thống"}

echo "🚀 [1/4] Build giao diện sản phẩm (Vite)..."
npm run build

echo "📦 [2/4] Đẩy code lên GitHub lưu trữ an toàn..."
git add .
git commit -m "$MSG" || echo "Không có thay đổi mới trong Git."
git push origin main

echo "📤 [3/4] Đồng bộ code trực tiếp lên VPS (Port 53122)..."
rsync -avz --delete \
  -e "ssh -p 53122" \
  --exclude='.git/' \
  --exclude='node_modules/' \
  --exclude='.env' \
  --exclude='storage/logs/*.log' \
  ./ root@103.206.218.215:/var/www/game-api-panel/

echo "⚡ [4/4] Khởi động lại dịch vụ & tối ưu cache trên VPS..."
ssh -p 53122 root@103.206.218.215 "
  cd /var/www/game-api-panel
  php artisan migrate --force
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  supervisorctl restart game-api-worker:*
  chown -R www-data:www-data /var/www/game-api-panel
  chmod -R 775 storage bootstrap/cache
"

echo "✅ HOÀN TẤT TRIỂN KHAI THÀNH CÔNG!"
echo "🌐 Truy cập website tại: http://103.206.218.215:8080"
```

Cấp quyền thực thi cho file:
```bash
chmod +x deploy.sh
```

---

## 🎉 KẾT QUẢ ĐẠT ĐƯỢC:
- **Trang web chạy trực tiếp tại**: [http://103.206.218.215:8080](http://103.206.218.215:8080)
- **Hệ thống Logger của Sếp**: Cổng 443 vẫn chạy 100% nguyên vẹn.
- **Hàng đợi Queue Worker**: Tự động chạy ngầm vĩnh viễn nhờ Supervisor.
- **Cập nhật code hàng ngày**: Chỉ cần chạy `./deploy.sh "nội dung"` là xong trong 10 giây!
