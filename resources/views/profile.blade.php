@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Profil Saya</h3>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama" 
                               id="nama" 
                               class="form-control @error('nama') is-invalid @enderror" 
                               value="{{ old('nama', $user->nama) }}"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $user->username }}" 
                               disabled>
                        <small class="text-muted">Username tidak dapat diubah</small>
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. Handphone</label>
                        <input type="text" 
                               name="no_hp" 
                               id="no_hp" 
                               class="form-control @error('no_hp') is-invalid @enderror" 
                               value="{{ old('no_hp', $user->no_hp) }}"
                               placeholder="081234567890">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $user->role_label }}" 
                               disabled>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Ganti Password</h6>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               placeholder="Masukkan password saat ini">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" 
                               name="new_password" 
                               id="new_password" 
                               class="form-control @error('new_password') is-invalid @enderror" 
                               placeholder="Minimal 6 karakter">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" 
                               name="new_password_confirmation" 
                               id="new_password_confirmation" 
                               class="form-control" 
                               placeholder="Ulangi password baru">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="avatar mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block">
                        <i data-feather="user" style="width: 64px; height: 64px;" class="text-primary"></i>
                    </div>
                </div>
                <h5 class="fw-bold">{{ $user->nama }}</h5>
                <p class="text-muted mb-1">@ {{ $user->username }}</p>
                <span class="badge {{ $user->role_badge }}" style="font-size: 14px;">
                    {{ $user->role_label }}
                </span>
                <hr>
                <div class="text-start">
                    <p class="mb-2">
                        <i data-feather="calendar" style="width: 16px;"></i>
                        Bergabung: {{ $user->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if($user->no_hp)
                        <p class="mb-0">
                            <i data-feather="phone" style="width: 16px;"></i>
                            {{ $user->no_hp }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection