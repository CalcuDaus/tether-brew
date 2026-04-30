@extends('layouts.app')
@section('title', 'Edit Artikel')

@section('actions')
    <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Artikel</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.artikel.update', $artikel) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="title">Judul Artikel *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $artikel->title) }}" placeholder="Masukkan judul artikel..." required>
                @error('title') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="category">Kategori *</label>
                    <select id="category" name="category" class="form-input form-select" required>
                        @php $cat = old('category', $artikel->category); @endphp
                        <option value="Cerita" {{ $cat === 'Cerita' ? 'selected' : '' }}>Cerita</option>
                        <option value="Tips" {{ $cat === 'Tips' ? 'selected' : '' }}>Tips</option>
                        <option value="Insight" {{ $cat === 'Insight' ? 'selected' : '' }}>Insight</option>
                        <option value="Edukasi" {{ $cat === 'Edukasi' ? 'selected' : '' }}>Edukasi</option>
                        <option value="Behind The Scenes" {{ $cat === 'Behind The Scenes' ? 'selected' : '' }}>Behind The Scenes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="read_time">Waktu Baca (menit)</label>
                    <input type="number" id="read_time" name="read_time" class="form-input" value="{{ old('read_time', $artikel->read_time) }}" placeholder="Auto-detect" min="1" max="60">
                    <div class="text-sm-muted mt-2-custom">Kosongkan untuk auto-detect</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="excerpt">Ringkasan / Excerpt *</label>
                <textarea id="excerpt" name="excerpt" class="form-input form-textarea" placeholder="Tulis ringkasan singkat artikel..." rows="3" required>{{ old('excerpt', $artikel->excerpt) }}</textarea>
                @error('excerpt') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="cover_image">Cover Image</label>
                @if($artikel->cover_image)
                    <div class="mb-2-custom">
                        <img src="{{ asset('storage/' . $artikel->cover_image) }}" alt="Cover saat ini" style="max-height: 120px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div class="text-sm-muted mt-2-custom">Cover saat ini. Upload baru untuk mengganti.</div>
                    </div>
                @endif
                <input type="file" id="cover_image" name="cover_image" class="form-input" accept="image/*">
                <div class="text-sm-muted mt-2-custom">Format: JPG, PNG, WebP. Max: 2MB</div>
                @error('cover_image') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konten Artikel *</label>
                <div id="quill-editor" class="quill-editor-container">{!! old('content', $artikel->content) !!}</div>
                <input type="hidden" name="content" id="content-input">
                @error('content') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <div class="form-checkbox-group">
                    <input type="checkbox" id="is_published" name="is_published" class="form-checkbox" value="1" {{ old('is_published', $artikel->is_published) ? 'checked' : '' }}>
                    <label for="is_published" class="form-label form-label-no-margin">Publish Artikel</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submit-btn">💾 Simpan Perubahan</button>
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
