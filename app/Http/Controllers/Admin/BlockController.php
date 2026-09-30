<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function index(): View
    {
        $blocks = Block::query()->orderBy('label')->paginate(30);

        return view('admin.blocks.index', compact('blocks'));
    }

    public function create(): View
    {
        return view('admin.blocks.form', ['block' => new Block()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Block::query()->create($data);

        return redirect()->route('admin.blocks.index')->with('status', 'Đã tạo block.');
    }

    public function edit(Block $block): View
    {
        return view('admin.blocks.form', compact('block'));
    }

    public function update(Request $request, Block $block): RedirectResponse
    {
        $oldName = $block->name;
        $data = $this->validated($request, $block->id);

        // Xoá cache của tên cũ trước khi đổi tên
        if ($oldName !== $data['name']) {
            $block->clearCache();
        }

        $block->update($data);
        $block->clearCache();

        return redirect()->route('admin.blocks.index')->with('status', 'Đã cập nhật block.');
    }

    public function destroy(Block $block): RedirectResponse
    {
        $block->clearCache();
        $block->delete();

        return redirect()->route('admin.blocks.index')->with('status', 'Đã xoá block.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:content_blocks,name' . ($ignoreId ? ',' . $ignoreId : '');

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100', $uniqueRule, 'regex:/^[a-z0-9_\-]+$/'],
            'label'     => ['required', 'string', 'max:150'],
            'content'   => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
