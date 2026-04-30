@extends('layouts.app')
@section('title', 'Tambah Artikel')

@section('actions')
    <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg> Form Tambah Artikel</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.artikel.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="title">Judul Artikel *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}" placeholder="Masukkan judul artikel..." required>
                @error('title') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="category">Kategori *</label>
                    <select id="category" name="category" class="form-input form-select" required>
                        <option value="Cerita" {{ old('category') === 'Cerita' ? 'selected' : '' }}>Cerita</option>
                        <option value="Tips" {{ old('category') === 'Tips' ? 'selected' : '' }}>Tips</option>
                        <option value="Insight" {{ old('category') === 'Insight' ? 'selected' : '' }}>Insight</option>
                        <option value="Edukasi" {{ old('category') === 'Edukasi' ? 'selected' : '' }}>Edukasi</option>
                        <option value="Behind The Scenes" {{ old('category') === 'Behind The Scenes' ? 'selected' : '' }}>Behind The Scenes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="read_time">Waktu Baca (menit)</label>
                    <input type="number" id="read_time" name="read_time" class="form-input" value="{{ old('read_time') }}" placeholder="Auto-detect" min="1" max="60">
                    <div class="text-sm-muted mt-2-custom">Kosongkan untuk auto-detect</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="excerpt">Ringkasan / Excerpt *</label>
                <textarea id="excerpt" name="excerpt" class="form-input form-textarea" placeholder="Tulis ringkasan singkat artikel..." rows="3" required>{{ old('excerpt') }}</textarea>
                @error('excerpt') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="cover_image">Cover Image</label>
                <input type="file" id="cover_image" name="cover_image" class="form-input" accept="image/*">
                <div class="text-sm-muted mt-2-custom">Format: JPG, PNG, WebP. Max: 2MB</div>
                @error('cover_image') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konten Artikel *</label>
                <div id="quill-editor" class="quill-editor-container">{!! old('content') !!}</div>
                <input type="hidden" name="content" id="content-input">
                @error('content') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <div class="form-checkbox-group">
                    <input type="checkbox" id="is_published" name="is_published" class="form-checkbox" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                    <label for="is_published" class="form-label form-label-no-margin">Publish Artikel</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submit-btn">💾 Simpan Artikel</button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Tulis konten artikel di sini...',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link', 'image'],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    const form = document.querySelector('form');
    const contentInput = document.getElementById('content-input');

    // Sync Quill content to hidden input helper
    function syncContent() {
        const html = quill.root.innerHTML;
        contentInput.value = (html === '<p><br></p>' || html.trim() === '') ? '' : html;
    }

    // Sync immediately on page load (in case of old content)
    syncContent();

    // Sync on every text change so hidden input is always up to date
    quill.on('text-change', syncContent);

    // Also sync on form submit as a safety net
    form.addEventListener('submit', syncContent);
});
</script>
@endpush
