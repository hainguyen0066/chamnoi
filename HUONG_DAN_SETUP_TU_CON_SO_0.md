# 📘 HƯỚNG DẪN TỪ CON SỐ 0: TỪ MÁY TÍNH -> GITHUB -> CÀI ĐẶT VPS TRẮNG TINH

> **Dành cho người mới bắt đầu (Người không rành kỹ thuật vẫn làm được 100%)**  
> Tài liệu này ghi lại toàn bộ những gì đã làm, từ lúc VPS mới mua chưa cài gì cho đến khi website hoạt động hoàn chỉnh.

---

## 🧭 MỤC LỤC & TỔNG QUAN HỆ THỐNG

1. **Hiểu bản chất mô hình hoạt động (Bức tranh tổng thể)**
2. **Phần 1: Kết nối Code với GitHub**
3. **Phần 2: Kết nối Máy tính vào VPS bằng SSH (Không cần gõ mật khẩu)**
4. **Phần 3: Cài đặt VPS từ lúc trắng tinh (Chưa có gì)**
   - Cài Web server (Nginx)
   - Cài PHP 8.3 & các tiện ích mở rộng
   - Cài Composer (quản lý thư viện PHP)
   - Cài và cấu hình Cơ sở dữ liệu (MariaDB/MySQL)
   - Cài Supervisor (chạy tiến trình ngầm 24/7)
5. **Phần 4: Đưa Code lên VPS & Cấu hình Website Laravel**
   - Tạo thư mục & đồng bộ code
   - Cấu hình file `.env`
   - Phân quyền thư mục an toàn
   - Cấu hình Nginx Virtual Host (Port 8080)
   - Mở tường lửa (Firewall UFW)
6. **Phần 5: Cấu hình Queue Worker tự động chạy ngầm 24/7 (Supervisor)**
7. **Phần 6: Giải thích script tự động `./deploy.sh` (1 click là xong)**

---

## 🧩 HIỂU BẢN CHẤT MÔ HÌNH HOẠT ĐỘNG

Hãy tưởng tượng hệ thống gồm 3 thành phần:
```
[Máy tính của bạn]  ---- (git push) ---->  [GitHub: Kho lưu trữ code dự phòng trên mây]
       │
       └----------------- (deploy.sh) ->  [VPS Server: Nơi chạy website thật]
                                                 │
                                                 ├── Nginx (Cửa đón khách port 8080)
                                                 ├── PHP 8.3 + Laravel (Bộ não xử lý)
                                                 ├── MariaDB (Kho dữ liệu tài khoản, đá acc)
                                                 └── Supervisor (Nhân viên chạy ngầm xử lý queue)
```

- **Máy tính của bạn**: Nơi bạn mở VS Code lên viết tính năng, test thử.
- **GitHub**: Giống như Google Drive nhưng chuyên lưu code, có lịch sử từng lần bạn sửa code.
- **VPS Server**: Một cái máy tính đặt ở trung tâm dữ liệu, cắm mạng và điện 24/24 để khách truy cập website mọi lúc.

---

## PHẦN 1: KẾT NỐI CODE VỚI GITHUB

### 1. Git & GitHub là gì?
- **Git** là công cụ trên máy tính giúp ghi nhớ lịch sử sửa code.
- **GitHub** là website lưu trữ kho code đó lên internet.

### 2. Các bước kết nối từ đầu:
Nếu bạn tạo 1 dự án mới toanh trên máy:

**Bước 1: Khởi tạo Git trong thư mục dự án:**
```bash
cd /duong/dan/thu/muc/code
git init
```

**Bước 2: Tạo kho chứa (Repository) trên GitHub:**
- Vào https://github.com -> Bấm **New repository** -> Đặt tên (ví dụ: `ttthapiv2`) -> Bấm **Create**.
- GitHub sẽ cấp cho bạn một đường dẫn, ví dụ: `git@github.com:hainguyen0066/ttthapiv2.git`

**Bước 3: Liên kết thư mục máy tính với GitHub:**
```bash
git remote add origin git@github.com:hainguyen0066/ttthapiv2.git
```

**Bước 4: Lưu code và đẩy lên GitHub:**
```bash
git add .
git commit -m "Khoi tao du an lan dau"
git branch -M main
git push -u origin main
```
*Từ những lần sau, mỗi khi sửa code xong chỉ cần:*
```bash
git add .
git commit -m "Sửa tính năng A"
git push
```

---

## PHẦN 2: KẾT NỐI MÁY TÍNH VÀO VPS BẰNG SSH (KHÔNG CẦN NHẬP PASS)

Khi mới mua VPS, bạn sẽ có:
- **IP VPS**: `103.206.218.215`
- **SSH Port**: `53122` (cổng SSH riêng để bảo mật)
- **Tài khoản**: `root`
- **Mật khẩu**: `r75+1!zMnwCN.@9A@pvQLz3m`

### 1. Đăng nhập thủ công qua Terminal:
```bash
ssh -p 53122 root@103.206.218.215
```
*Máy sẽ hỏi mật khẩu, dán mật khẩu vào rồi Enter (lưu ý khi gõ pass trên Linux nó sẽ không hiện ký tự gì, cứ paste rồi Enter là được).*

### 2. Cài SSH Key để từ sau KHÔNG BAO GIỜ phải gõ pass nữa:
Trên máy tính của bạn (Terminal máy nhà):
```bash
# 1. Tạo chìa khóa (nếu chưa có, bấm Enter hết mọi câu hỏi)
ssh-keygen -t ed25519

# 2. Gửi chìa khóa công khai lên VPS
ssh-copy-id -p 53122 root@103.206.218.215
# (Nhập mật khẩu VPS 1 lần duy nhất)
```
Từ giờ, bạn chỉ cần gõ `ssh -p 53122 root@103.206.218.215` là máy tự mở khóa vào thẳng VPS mà không hỏi mật khẩu. Điều này giúp các lệnh tự động (như `./deploy.sh`) chạy mượt mà không bị khựng lại đòi pass.

---

## PHẦN 3: CÀI ĐẶT VPS TỪ LÚC TRẮNG TINH (CHƯA CÓ GÌ)

> ⚠️ Đăng nhập vào VPS với quyền `root` trước khi gõ các lệnh dưới đây:
> `ssh -p 53122 root@103.206.218.215`

### 1. Cập nhật hệ thống Ubuntu:
```bash
apt update && apt upgrade -y
```

### 2. Cài đặt Nginx (Web Server đón khách):
```bash
apt install nginx -y
systemctl enable nginx
systemctl start nginx
```

### 3. Cài đặt PHP 8.3 và các tiện ích cần thiết cho Laravel:
Laravel cần PHP và một số module bổ trợ để xử lý database, nén file zip, mã hóa token, gọi API curl...
```bash
# Thêm kho phần mềm PHP mới nhất
apt install software-properties-common ca-certificates lsb-release apt-transport-https -y
add-apt-repository ppa:ondrej/php -y
apt update

# Cài đặt PHP 8.3 FPM và các module
apt install -y php8.3-fpm php8.3-cli php8.3-common php8.3-mysql php8.3-curl \
php8.3-xml php8.3-zip php8.3-bcmath php8.3-mbstring php8.3-tokenizer php8.3-intl
```

### 4. Cài đặt Composer (Công cụ cài thư viện PHP):
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### 5. Cài đặt & Cấu hình Cơ sở dữ liệu (MariaDB / MySQL):
```bash
apt install mariadb-server -y
systemctl enable mariadb
systemctl start mariadb
```

**Tạo database và tài khoản quản trị cho website:**
Vào terminal MariaDB:
```bash
mariadb
```
Chạy các dòng lệnh SQL sau:
```sql
-- 1. Tạo database tên game_api_panel
CREATE DATABASE IF NOT EXISTS game_api_panel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Tạo người dùng game_admin với mật khẩu GameApiPass2026!
CREATE USER IF NOT EXISTS 'game_admin'@'127.0.0.1' IDENTIFIED BY 'GameApiPass2026!';

-- 3. Cấp full quyền cho game_admin vào database này
GRANT ALL PRIVILEGES ON game_api_panel.* TO 'game_admin'@'127.0.0.1';

-- 4. Lưu lại quyền và thoát
FLUSH PRIVILEGES;
EXIT;
```

### 6. Cài Supervisor (Bộ giám sát chạy nền cho Hàng đợi Queue):
```bash
apt install supervisor -y
systemctl enable supervisor
systemctl start supervisor
```

---

## PHẦN 4: ĐƯA CODE LÊN VPS & CẤU HÌNH WEBSITE LARAVEL

### 1. Tạo thư mục chứa web trên VPS:
```bash
mkdir -p /var/www/game-api-panel
```

### 2. Cấu hình file Nginx để web chạy ở Port 8080:
*(Chúng ta dùng Port 8080 để KHÔNG va chạm với dịch vụ logger có sẵn của VPS ở Port 80/443)*

Tạo file cấu hình Nginx:
```bash
nano /etc/nginx/sites-available/game-api-panel.conf
```
Dán nội dung sau vào:
```nginx
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
```
Lưu lại (trong nano bấm `Ctrl + O`, `Enter`, rồi `Ctrl + X`).

Kích hoạt file cấu hình và tải lại Nginx:
```bash
ln -s /etc/nginx/sites-available/game-api-panel.conf /etc/nginx/sites-enabled/
nginx -t          # Kiểm tra cấu hình có lỗi cú pháp không
systemctl reload nginx
```

### 3. Mở tường lửa cho Port 8080:
```bash
ufw allow 8080/tcp
```

### 4. Đưa code từ máy lên VPS & Cài thư viện:
Từ máy tính của bạn, dùng `rsync` hoặc chạy `./deploy.sh` để đẩy code lên.

Sau đó trên VPS, vào thư mục web:
```bash
cd /var/www/game-api-panel

# Cài đặt các thư viện Laravel
composer install --no-dev --optimize-autoloader

# Cấu hình file .env
cp .env.example .env
nano .env
```
Trong file `.env`, chỉnh các dòng:
```env
APP_NAME="GAME API PANEL"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://103.206.218.215:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=game_api_panel
DB_USERNAME=game_admin
DB_PASSWORD=GameApiPass2026!

QUEUE_CONNECTION=database
```

Tạo khóa bảo mật và chạy cơ sở dữ liệu:
```bash
php artisan key:generate
php artisan migrate --force --seed
```

### 5. Phân quyền thư mục (Cực kỳ quan trọng!):
Nếu không phân quyền, web sẽ báo lỗi 500 trắng trang vì không có quyền ghi log hoặc cache:
```bash
chown -R www-data:www-data /var/www/game-api-panel
chmod -R 775 /var/www/game-api-panel/storage /var/www/game-api-panel/bootstrap/cache
chmod 640 /var/www/game-api-panel/.env
```

---

## PHẦN 5: CẤU HÌNH QUEUE WORKER TỰ ĐỘNG CHẠY 24/7 (SUPERVISOR)

Khi bạn bấm "Đá tài khoản hàng loạt", Laravel không làm treo trình duyệt mà đẩy vào Hàng đợi (Queue). Cần có 1 tiến trình chạy ngầm 24/7 để gắp từng lệnh ra gọi API GameServer.

Tạo file cấu hình cho Supervisor trên VPS:
```bash
nano /etc/supervisor/conf.d/game-api-worker.conf
```
Dán nội dung sau vào:
```ini
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
```
Lưu lại, sau đó kích hoạt:
```bash
supervisorctl reread
supervisorctl update
supervisorctl status
```
*Kết quả hiện `game-api-worker:game-api-worker_00 RUNNING` là thành công rực rỡ! Tiến trình này sẽ sống mãi mãi, kể cả VPS có bị khởi động lại.*

---

## PHẦN 6: GIẢI THÍCH SCRIPT TỰ ĐỘNG `./deploy.sh`

Mỗi lần bạn sửa code trên máy tính, bạn **không cần phải gõ lại hàng chục lệnh ở trên**. File [deploy.sh](file:///home/hainguyen/Desktop/APITTH/deploy.sh) đã tự động hóa 100% các bước:

```bash
#!/usr/bin/env bash
set -e

# 1. Đóng gói giao diện (CSS / JS) bằng Vite trên máy tính
npm run build

# 2. Đẩy code lên GitHub để lưu trữ an toàn
git add .
git commit -m "$1"
git push origin main

# 3. Đồng bộ code siêu tốc lên VPS qua cổng 53122
# (Bỏ qua các file rác, file .env riêng của máy nhà để không làm hỏng VPS)
rsync -avz --delete \
  -e "ssh -p 53122" \
  --exclude='.git/' \
  --exclude='node_modules/' \
  --exclude='.env' \
  --exclude='storage/logs/*.log' \
  ./ root@103.206.218.215:/var/www/game-api-panel/

# 4. Chạy các lệnh bảo trì trên VPS tự động:
# - Cập nhật database nếu có bảng mới
# - Xóa cache cũ để nhận code mới
# - Khởi động lại Queue Worker để nạp logic mới
# - Phân quyền chuẩn xác cho Nginx đọc ghi
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
```

---

## 🚀 TÓM TẮT 1 CÂU DỄ NHỚ:

- **Khi làm việc hàng ngày**: Bạn chỉ cần gõ đúng 1 dòng lệnh duy nhất ở Terminal máy tính:
  ```bash
  ./deploy.sh "Nội dung vừa sửa"
  ```
  Nó sẽ tự làm tất cả: build giao diện -> push lên GitHub -> rsync lên VPS -> xóa cache -> reset worker -> trang web nhận code mới ngay tức khắc!
- **Link truy cập web quản trị**: [http://103.206.218.215:8080](http://103.206.218.215:8080)
- **Tài khoản mặc định**: `admin@volam.local` / `password123`
