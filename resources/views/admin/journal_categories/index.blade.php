@extends('layouts.app')

@section('title', 'Kelola Kategori Jurnal')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Kategori Jurnal</h3>
        <button onclick="openModal('add')" class="btn btn-primary btn-sm flex-center" style="gap: 0.5rem;">
            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
            </svg> Tambah Kategori
        </button>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                        <th style="padding: 12px; font-weight: 600;">Nama Kategori</th>
                        <th style="padding: 12px; font-weight: 600;">Jumlah Jurnal</th>
                        <th style="padding: 12px; font-weight: 600; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='var(--bg-card-hover)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px;">{{ $category->name }}</td>
                        <td style="padding: 12px;">{{ $category->journals_count ?? $category->journals()->count() }}</td>
                        <td style="padding: 12px; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                            <button onclick="openModal('edit', {{ $category->id }}, '{{ $category->name }}')" class="btn btn-secondary btn-sm" title="Edit">
                                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('admin.journal_categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 24px; text-align: center; color: var(--text-muted);">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="categoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 400px; margin: 20px;">
        <div class="card-header">
            <h3 class="card-title" id="modalTitle">Tambah Kategori</h3>
        </div>
        <div class="card-body">
            <form id="categoryForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="form-group mb-4">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="categoryName" class="form-input" placeholder="Contoh: BAHAN BAKU" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(mode, id = null, name = '') {
        const modal = document.getElementById('categoryModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('categoryForm');
        const nameInput = document.getElementById('categoryName');
        const methodField = document.getElementById('methodField');

        if (mode === 'add') {
            title.innerText = 'Tambah Kategori';
            form.action = "{{ route('admin.journal_categories.store') }}";
            nameInput.value = '';
            methodField.innerHTML = '';
        } else {
            title.innerText = 'Edit Kategori';
            form.action = `/journal-categories/${id}`;
            nameInput.value = name;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        }

        modal.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    // Close modal on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('categoryModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endpush
@endsection
