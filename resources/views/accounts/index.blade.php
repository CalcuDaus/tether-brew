@extends('layouts.app')
@section('title', 'Kelola Akun')

@section('actions')
    <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Akun</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($accounts->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td>#{{ $account->id }}</td>
                                <td class="text-primary-semi">
                                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg> {{ $account->name }}
                                </td>
                                <td>{{ $account->email }}</td>
                                <td>
                                    @if($account->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $account->whatsapp) }}" target="_blank" class="text-xs-green">
                                            {{ $account->whatsapp }}
                                        </a>
                                    @else
                                        <span class="text-sm-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $account->role === 'owner' ? 'active' : ($account->role === 'admin' ? 'draft' : 'inactive') }}">
                                        {{ ucfirst($account->role) }}
                                    </span>
                                </td>
                                <td class="text-sm-muted">{{ $account->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="flex-gap-2">
                                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</a>
                                        @if(auth()->id() !== $account->id)
                                        <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Yakin hapus akun ini? Menghapus rider akan memutus tugasnya dari gerobak.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4-custom">
                {{ $accounts->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Akun</div>
                <div class="empty-state-text">Mulai tambahkan akun untuk mengelola pengguna</div>
                <a href="{{ route('accounts.create') }}" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Akun</a>
            </div>
        @endif
    </div>
</div>
@endsection
