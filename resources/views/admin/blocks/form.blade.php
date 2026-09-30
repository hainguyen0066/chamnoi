<x-admin-layout>
    <x-slot name="header">{{ $block->exists ? 'Sửa block: ' . $block->label : 'Tạo block mới' }}</x-slot>

    <form method="POST"
          action="{{ $block->exists ? route('admin.blocks.update', $block) : route('admin.blocks.store') }}">
        @csrf
        @if ($block->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cột trái --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
                            Tên hiển thị (Label) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="label" name="label"
                               value="{{ old('label', $block->label) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('label') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Tên định danh (name) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $block->name) }}" required
                               {{ $block->exists ? 'readonly' : '' }}
                               placeholder="vd: homepage_banner, sidebar_promo"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm font-mono {{ $block->exists ? 'bg-gray-50 text-gray-500' : '' }}">
                        <p class="text-xs text-gray-400 mt-1">Chỉ chứa chữ thường, số, dấu <code>_</code> và <code>-</code>. Không thể thay đổi sau khi tạo.</p>
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung HTML</label>
                        <textarea id="content" name="content">{{ old('content', $block->content) }}</textarea>
                        @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            Dùng nút <strong>Source</strong> (cuối toolbar) để nhập HTML thô, hoặc soạn trực tiếp bằng editor.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Cột phải --}}
            <div class="space-y-4">
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800">Xuất bản</h2>

                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $block->is_active ?? true))
                               class="rounded border-gray-300">
                        Hiển thị trên frontend
                    </label>

                    <button type="submit" class="btn btn-primary btn-block">
                        {{ $block->exists ? 'Cập nhật block' : 'Lưu block' }}
                    </button>

                    @if ($block->exists)
                        <a href="{{ route('admin.blocks.index') }}"
                           class="block text-center text-sm text-gray-500 hover:text-gray-700">← Quay lại</a>
                    @endif
                </div>

                @if ($block->exists)
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-sm">
                        <p class="font-medium text-indigo-800 mb-2">Nhúng vào frontend:</p>
                        <code class="block bg-white border border-indigo-100 rounded px-2 py-1.5 font-mono text-xs text-indigo-700 break-all">
                            &lt;x-block name="{{ $block->name }}" /&gt;
                        </code>
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- CKEditor 5 v43 – cấu hình đầy đủ cho content blocks --}}
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css">
    <style>
        .ck-editor__main .ck-content { padding-left: 30px !important; }
    </style>
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
                    'imageTextAlternative', 'toggleImageCaption', '|', 'resizeImage'
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
            },
            list: {
                properties: { styles: true, startIndex: true, reversed: true }
            }
        })
        .then(ed => {
            ed.editing.view.change(writer => {
                writer.setStyle('min-height', '360px', ed.editing.view.document.getRoot());
            });
        });
    </script>
</x-admin-layout>
