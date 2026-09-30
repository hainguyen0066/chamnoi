<x-admin-layout>
    <x-slot name="header">Quản lý Media</x-slot>

    {{-- Upload area --}}
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-semibold text-gray-800 text-base mb-4">Upload ảnh lên CDN</h2>

        <div id="drop-zone"
             class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-indigo-400 transition-colors"
             onclick="document.getElementById('file-input').click()">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            <p class="text-sm text-gray-500">Kéo thả ảnh vào đây hoặc <span class="text-indigo-600 font-medium">click để chọn</span></p>
            <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP — tối đa 5 MB</p>
        </div>

        <input type="file" id="file-input" accept="image/*" multiple class="hidden">

        {{-- Upload progress --}}
        <div id="upload-progress" class="hidden mt-4 space-y-2"></div>
    </div>

    {{-- Toast --}}
    <div id="toast"
         class="fixed bottom-5 right-5 z-50 hidden px-4 py-3 rounded-lg text-sm font-medium shadow-lg transition-all">
    </div>

    {{-- Image grid --}}
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800 text-base">
                Tất cả ảnh
                <span id="file-count" class="ml-2 text-xs font-normal text-gray-400">({{ count($files) }} ảnh)</span>
            </h2>
            <input type="text" id="search-input" placeholder="Tìm theo tên..."
                   class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-48">
        </div>

        <div id="image-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @forelse ($files as $file)
                <div class="image-card relative group rounded-lg overflow-hidden bg-gray-100 aspect-square"
                     data-url="{{ $file['url'] }}"
                     data-path="{{ $file['path'] }}"
                     data-filename="{{ $file['filename'] }}">

                    <img src="{{ $file['url'] }}" alt="{{ $file['filename'] }}"
                         class="w-full h-full object-cover"
                         loading="lazy">

                    {{-- Overlay on hover --}}
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                        <button onclick="copyUrl(this)"
                                data-url="{{ $file['url'] }}"
                                class="w-full py-1 px-2 bg-white text-gray-800 text-xs font-medium rounded hover:bg-indigo-50 truncate">
                            Copy URL
                        </button>
                        <button onclick="deleteFile(this)"
                                data-path="{{ $file['path'] }}"
                                class="w-full py-1 px-2 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">
                            Xoá
                        </button>
                    </div>

                    {{-- Filename label --}}
                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-1.5 py-0.5 text-white text-[10px] truncate">
                        {{ $file['filename'] }}
                    </div>
                </div>
            @empty
                <div id="empty-state" class="col-span-full py-16 text-center text-gray-400 text-sm">
                    Chưa có ảnh nào. Hãy upload ảnh đầu tiên.
                </div>
            @endforelse
        </div>
    </div>

    <script>
    const UPLOAD_URL  = '{{ route('admin.media.store') }}';
    const DELETE_URL  = '{{ route('admin.media.destroy') }}';
    const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

    // ── Upload ───────────────────────────────────────────────────────────────

    document.getElementById('file-input').addEventListener('change', e => {
        uploadFiles(e.target.files);
        e.target.value = '';
    });

    const dropZone = document.getElementById('drop-zone');
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-indigo-500', 'bg-indigo-50'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-indigo-500', 'bg-indigo-50'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        uploadFiles(e.dataTransfer.files);
    });

    async function uploadFiles(fileList) {
        const progress = document.getElementById('upload-progress');
        progress.classList.remove('hidden');

        for (const file of fileList) {
            const row = addProgressRow(file.name);
            const fd  = new FormData();
            fd.append('file', file);
            fd.append('_token', CSRF);

            try {
                const res  = await fetch(UPLOAD_URL, { method: 'POST', body: fd });
                const data = await res.json();

                if (data.success) {
                    row.querySelector('.status').textContent = '✓ Xong';
                    row.querySelector('.status').className = 'status text-green-600';
                    prependImageCard(data.url, data.url.split('/').pop(), data.url.split('/files/')[1] ? 'files/' + data.url.split('/files/')[1] : '');
                    updateCount(1);
                } else {
                    row.querySelector('.status').textContent = '✗ ' + (data.error || 'Lỗi');
                    row.querySelector('.status').className = 'status text-red-600';
                }
            } catch (err) {
                row.querySelector('.status').textContent = '✗ Lỗi kết nối';
                row.querySelector('.status').className = 'status text-red-600';
            }
        }

        setTimeout(() => { progress.innerHTML = ''; progress.classList.add('hidden'); }, 3000);
    }

    function addProgressRow(name) {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 text-sm';
        row.innerHTML = `<span class="truncate flex-1 text-gray-700">${name}</span><span class="status text-gray-400">Đang upload...</span>`;
        document.getElementById('upload-progress').appendChild(row);
        return row;
    }

    function prependImageCard(url, filename, path) {
        const empty = document.getElementById('empty-state');
        if (empty) empty.remove();

        const card = document.createElement('div');
        card.className = 'image-card relative group rounded-lg overflow-hidden bg-gray-100 aspect-square';
        card.dataset.url      = url;
        card.dataset.path     = path;
        card.dataset.filename = filename;
        card.innerHTML = `
            <img src="${url}" alt="${filename}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                <button onclick="copyUrl(this)" data-url="${url}"
                        class="w-full py-1 px-2 bg-white text-gray-800 text-xs font-medium rounded hover:bg-indigo-50 truncate">Copy URL</button>
                <button onclick="deleteFile(this)" data-path="${path}"
                        class="w-full py-1 px-2 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">Xoá</button>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-1.5 py-0.5 text-white text-[10px] truncate">${filename}</div>`;
        document.getElementById('image-grid').prepend(card);
    }

    // ── Copy URL ─────────────────────────────────────────────────────────────

    function copyUrl(btn) {
        navigator.clipboard.writeText(btn.dataset.url).then(() => {
            showToast('Đã copy URL vào clipboard!', 'green');
        });
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    async function deleteFile(btn) {
        if (!confirm('Xoá ảnh này?')) return;
        const path = btn.dataset.path;
        const card = btn.closest('.image-card');

        try {
            const res  = await fetch(DELETE_URL, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ path }),
            });
            const data = await res.json();

            if (data.success) {
                card.remove();
                updateCount(-1);
                showToast('Đã xoá ảnh.', 'red');
            } else {
                showToast('Xoá thất bại.', 'red');
            }
        } catch {
            showToast('Lỗi kết nối.', 'red');
        }
    }

    // ── Search ───────────────────────────────────────────────────────────────

    document.getElementById('search-input').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.image-card').forEach(card => {
            card.style.display = card.dataset.filename.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // ── Helpers ──────────────────────────────────────────────────────────────

    function updateCount(delta) {
        const el   = document.getElementById('file-count');
        const cur  = parseInt(el.textContent.replace(/\D/g, '')) || 0;
        el.textContent = `(${cur + delta} ảnh)`;
    }

    function showToast(msg, color) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-lg text-sm font-medium shadow-lg
            ${color === 'green' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'}`;
        t.classList.remove('hidden');
        clearTimeout(t._timer);
        t._timer = setTimeout(() => t.classList.add('hidden'), 2500);
    }
    </script>
</x-admin-layout>
