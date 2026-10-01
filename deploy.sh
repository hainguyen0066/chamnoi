#!/bin/bash
# Script tự động triển khai mã nguồn lên VPS 103.206.218.215:8080
set -e

VPS_IP="103.206.218.215"
VPS_PORT="53122"
VPS_PATH="/var/www/game-api-panel"

echo "=============================================="
echo "🚀 BẮT ĐẦU TRIỂN KHAI LÊN VPS ($VPS_IP)"
echo "=============================================="

# 1. Build assets frontend nếu có thay đổi CSS/JS
echo "📦 [1/4] Biên dịch assets (Vite)..."
npm run build

# 2. Push lên GitHub (nếu có commit mới)
echo "🐙 [2/4] Kiểm tra và đẩy code lên GitHub..."
if [ -n "$(git status --porcelain)" ]; then
    COMMIT_MSG="${1:-update: $(date '+%Y-%m-%d %H:%M:%S')}"
    git add .
    git commit -m "$COMMIT_MSG"
    git push origin main
else
    echo "  -> Git working tree sạch, bỏ qua commit."
fi

# 3. Đồng bộ mã nguồn sang VPS qua SSH
echo "🔄 [3/4] Đồng bộ code sang VPS qua Rsync..."
rsync -avz \
  -e "ssh -p $VPS_PORT -o StrictHostKeyChecking=no" \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='.env' \
  --exclude='.phpunit.result.cache' \
  --exclude='storage/logs/*.log' \
  ./ root@$VPS_IP:$VPS_PATH/

# 4. Kích hoạt thay đổi trên VPS (Migrate, Clear Cache, Restart Queue Worker)
echo "⚡ [4/4] Áp dụng cấu hình và khởi động lại Queue Worker..."
ssh -p $VPS_PORT -o StrictHostKeyChecking=no root@$VPS_IP "
  cd $VPS_PATH
  php artisan migrate --force
  php artisan config:clear
  php artisan cache:clear
  php artisan view:clear
  php artisan queue:restart
  chown -R www-data:www-data storage bootstrap/cache
"

echo "=============================================="
echo "✅ TRIỂN KHAI HOÀN TẤT THÀNH CÔNG!"
echo "👉 Truy cập: http://$VPS_IP:8080/admin/game-kicks"
echo "=============================================="
