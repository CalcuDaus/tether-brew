@extends('layouts.app')
@section('title', 'Manajemen Cabang')

@section('content')
<div class="card">
    <div class="card-body card-body-no-padding">
        
        <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.1rem; color: var(--text-primary); font-weight: 600;">Daftar Cabang</h3>
            <button type="button" onclick="openBranchModal()" class="btn btn-primary flex-center" style="gap: 0.5rem; white-space: nowrap;">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Tambah Cabang
            </button>
        </div>

        @if($branches->count() > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Cabang</th>
                            <th>No. Telp</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                            <tr>
                                <td><span class="badge" style="background: var(--bg-secondary); color: var(--text-primary);">{{ $branch->code }}</span></td>
                                <td style="font-weight: 600; color: var(--text-primary);">{{ $branch->name }}</td>
                                <td>{{ $branch->phone ?? '-' }}</td>
                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $branch->address ?? '-' }}</td>
                                <td>
                                    @if($branch->is_active)
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Aktif</span>
                                    @else
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                        <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-outline-primary btn-sm flex-center">
                                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Edit
                                        </a>
                                        <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" data-confirm="Yakin ingin menghapus cabang ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm flex-center">
                                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4-custom">
                {{ $branches->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.38-2.92a2 2 0 0 1 2.22 0L13 7"/><path d="M2 7v14"/><path d="M22 7v14"/><path d="M22 21H2"/><path d="M13 7v14"/><path d="M7 21v-5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5"/></svg>
                </div>
                <div class="empty-state-title">Belum Ada Cabang</div>
                <div class="empty-state-text">Mulai tambahkan cabang baru untuk mengelola multi-cabang.</div>
                <button type="button" onclick="openBranchModal()" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Cabang</button>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Cabang -->
<div id="branchModal" class="modal-overlay-animate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card modal-content-animate" style="width: 100%; max-width: 600px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.38-2.92a2 2 0 0 1 2.22 0L13 7"/><path d="M2 7v14"/><path d="M22 7v14"/><path d="M22 21H2"/><path d="M13 7v14"/><path d="M7 21v-5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5"/></svg> 
                Form Tambah Cabang
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('branches.store') }}" method="POST">
                @csrf
                
                <div class="grid-2">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Cabang *</label>
                        <input type="text" class="form-input" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Cabang Pusat" required>
                    </div>
                    <div class="form-group">
                        <label for="code" class="form-label">Kode (URL Slug) *</label>
                        <input type="text" class="form-input" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: pusat" required>
                        <div class="text-xs-muted mt-1" style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 4px;">Unik, huruf kecil tanpa spasi.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">No. Telepon / WhatsApp</label>
                    <input type="text" class="form-input" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-input" id="address" name="address" rows="3" placeholder="Alamat lengkap cabang">{{ old('address') }}</textarea>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 1rem;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="is_active" class="form-label" style="margin: 0; cursor: pointer;">Cabang Aktif</label>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> 
                        Simpan Cabang
                    </button>
                    <button type="button" onclick="closeBranchModal()" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-overlay-animate {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .modal-content-animate {
        transform: translateY(-20px) scale(0.95);
        opacity: 0;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
</style>

<script>
    function openBranchModal() {
        const modal = document.getElementById('branchModal');
        const content = modal.querySelector('.modal-content-animate');
        modal.style.display = 'flex';
        // force reflow
        void modal.offsetWidth;
        modal.style.opacity = '1';
        content.style.transform = 'translateY(0) scale(1)';
        content.style.opacity = '1';
    }

    function closeBranchModal() {
        const modal = document.getElementById('branchModal');
        const content = modal.querySelector('.modal-content-animate');
        modal.style.opacity = '0';
        content.style.transform = 'translateY(-20px) scale(0.95)';
        content.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }

    // Close modal on click outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('branchModal');
        if (event.target == modal) {
            closeBranchModal();
        }
    });
</script>
@endsection
