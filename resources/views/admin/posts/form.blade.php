<x-admin-layout>
    <x-slot name="header">{{ $post->exists ? 'Sửa bài viết' : 'Viết bài mới' }}</x-slot>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
        @csrf
        @if ($post->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cột trái: nội dung chính --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (bỏ trống sẽ tự sinh)</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                        <textarea id="excerpt" name="excerpt" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- AI hỗ trợ viết bài --}}
                    <div class="flex items-center gap-2 p-3 bg-indigo-50 border border-indigo-100 rounded-md">
                        <input type="text" id="ai-topic" placeholder="Nhập chủ đề, ví dụ: Sự kiện đua top server mới..."
                               class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                        <button type="button" id="btn-ai-generate" class="btn btn-primary whitespace-nowrap">
                            ✨ AI gợi ý nội dung
                        </button>
                    </div>
                    <p id="ai-status" class="text-sm text-gray-500 hidden"></p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                        <textarea id="content" name="content">{{ old('content', $post->content) }}</textarea>
                        @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- SEO --}}
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800">SEO</h2>
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta title</label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta description</label>
                        <input type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', $post->meta_description) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta keywords</label>
                        <input type="text" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                </div>
            </div>

            {{-- Cột phải: xuất bản, danh mục, thumbnail --}}
            <div class="space-y-4">
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800">Xuất bản</h2>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="draft" @selected(old('status', $post->status ?? 'draft') === 'draft')>Nháp</option>
                            <option value="published" @selected(old('status', $post->status) === 'published')>Đăng ngay</option>
                        </select>
                    </div>
                    <div>
                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Thời điểm đăng (tuỳ chọn)</label>
                        <input type="datetime-local" id="published_at" name="published_at"
                               value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured))
                               class="rounded border-gray-300">
                        Bài viết nổi bật
                    </label>

                    <button type="submit" class="btn btn-primary btn-block">
                        {{ $post->exists ? 'Cập nhật bài viết' : 'Lưu bài viết' }}
                    </button>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="font-semibold text-gray-800 mb-3">Danh mục *</h2>
                    @error('category_ids') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror
                    <div class="space-y-2 max-h-56 overflow-y-auto">
                        @php $selectedCategories = old('category_ids', $post->categories->pluck('id')->all()); @endphp
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                       @checked(in_array($category->id, $selectedCategories))
                                       class="rounded border-gray-300">
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="font-semibold text-gray-800 mb-3">Ảnh thumbnail</h2>
                    @if ($post->thumbnail)
                        <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : Storage::disk('public')->url($post->thumbnail) }}" alt=""
                             class="w-full rounded-md mb-3">
                    @endif
                    <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp"
                           class="w-full text-sm text-gray-600">
                    <p class="text-xs text-gray-400 mt-1">JPG/PNG/WebP, tối đa 2MB.</p>
                    @error('thumbnail') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </form>

    {{-- CKEditor 5 v43 – GPL, đầy đủ plugin, không cần license key trả phí --}}
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css">
    {{-- Vùng soạn thảo dùng cùng kiểu chữ + độ rộng cột với trang tin tức (xem file CSS) để soạn sao hiện vậy. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,700;1,400;1,700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/post-content-editor.css') }}?v={{ @filemtime(public_path('css/post-content-editor.css')) }}">
    <script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/"
        }
    }
    </script>
    <script type="module">
        import {
            ClassicEditor, Essentials, Autoformat, TextTransformation, PasteFromOffice,
            Paragraph, Heading,
            Bold, Italic, Underline, Strikethrough, Code, Subscript, Superscript, RemoveFormat,
            Alignment,
            FontSize, FontFamily, FontColor, FontBackgroundColor,
            HorizontalLine,
            Image, ImageCaption, ImageResize, ImageStyle, ImageToolbar, ImageUpload, AutoImage,
            FileRepository,
            Indent, IndentBlock,
            Link, AutoLink,
            List, ListProperties, TodoList,
            MediaEmbed,
            BlockQuote,
            Table, TableToolbar, TableCaption, TableCellProperties, TableColumnResize, TableProperties,
            CodeBlock,
            FindAndReplace,
            SourceEditing,
            SpecialCharacters, SpecialCharactersEssentials
        } from 'ckeditor5';

        class ImageUploadAdapter {
            constructor(loader) { this.loader = loader; }

            upload() {
                return this.loader.file.then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('file', file);
                    data.append('_token', document.querySelector('meta[name=csrf-token]').content);

                    fetch('{{ route('admin.posts.upload-image') }}', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: data
                    })
                    .then(async r => {
                        const json = await r.json();
                        if (!r.ok) throw new Error(json.error || json.message || 'Upload thất bại');
                        return json;
                    })
                    .then(d => resolve({ default: d.location }))
                    .catch(err => reject(err.message || 'Upload thất bại'));
                }));
            }

            abort() {}
        }

        function ImageUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter =
                loader => new ImageUploadAdapter(loader);
        }

        let editor;

        ClassicEditor.create(document.querySelector('#content'), {
            licenseKey: 'GPL',
            extraPlugins: [ImageUploadAdapterPlugin],
            plugins: [
                Essentials, Autoformat, TextTransformation, PasteFromOffice,
                Paragraph, Heading,
                Bold, Italic, Underline, Strikethrough, Code, Subscript, Superscript, RemoveFormat,
                Alignment,
                FontSize, FontFamily, FontColor, FontBackgroundColor,
                HorizontalLine,
                Image, ImageCaption, ImageResize, ImageStyle, ImageToolbar, ImageUpload, AutoImage,
                FileRepository,
                Indent, IndentBlock,
                Link, AutoLink,
                List, ListProperties, TodoList,
                MediaEmbed,
                BlockQuote,
                Table, TableToolbar, TableCaption, TableCellProperties, TableColumnResize, TableProperties,
                CodeBlock,
                FindAndReplace,
                SourceEditing,
                SpecialCharacters, SpecialCharactersEssentials
            ],
            toolbar: {
                items: [
                    'findAndReplace', '|',
                    'heading', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                    'bold', 'italic', 'underline', 'strikethrough', 'code',
                    'subscript', 'superscript', 'removeFormat', '|',
                    'alignment', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'link', 'uploadImage', 'mediaEmbed',
                    'insertTable', 'blockQuote', 'codeBlock',
                    'horizontalLine', 'specialCharacters', '|',
                    'undo', 'redo', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            image: {
                toolbar: [
                    'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                    'imageTextAlternative', 'toggleImageCaption', '|',
                    'resizeImage'
                ],
                resizeOptions: [
                    { name: 'resizeImage:original', value: null, label: 'Original' },
                    { name: 'resizeImage:25', value: '25', label: '25%' },
                    { name: 'resizeImage:50', value: '50', label: '50%' },
                    { name: 'resizeImage:75', value: '75', label: '75%' }
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells',
                    'tableProperties', 'tableCellProperties'
                ]
            },
            list: {
                properties: { styles: true, startIndex: true, reversed: true }
            },
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ]
            }
        })
        .then(ed => {
            editor = ed;
            ed.editing.view.change(writer => {
                writer.setStyle('min-height', '480px', ed.editing.view.document.getRoot());
            });
        });

        // Nút AI gợi ý nội dung: gọi endpoint Claude, đổ HTML vào editor.
        document.getElementById('btn-ai-generate').addEventListener('click', function () {
            const topic = document.getElementById('ai-topic').value.trim()
                || document.getElementById('title').value.trim();
            const statusEl = document.getElementById('ai-status');
            const btn = this;

            if (!topic) {
                alert('Nhập chủ đề hoặc tiêu đề bài viết trước.');
                return;
            }

            btn.disabled = true;
            statusEl.textContent = 'Đang nhờ AI viết nội dung, chờ chút...';
            statusEl.classList.remove('hidden', 'text-red-600');

            fetch('{{ route('admin.posts.ai-generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ topic: topic })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Có lỗi xảy ra.');
                return data;
            })
            .then(data => {
                editor.setData(data.content);
                statusEl.textContent = 'Xong! Hãy đọc lại và chỉnh sửa trước khi đăng.';
            })
            .catch(err => {
                statusEl.textContent = err.message;
                statusEl.classList.add('text-red-600');
            })
            .finally(() => { btn.disabled = false; });
        });
    </script>
</x-admin-layout>
