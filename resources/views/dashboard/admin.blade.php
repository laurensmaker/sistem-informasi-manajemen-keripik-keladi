@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Dashboard</h3>
    <div>
        <span class="badge bg-{{ auth()->user()->role == 'owner' ? 'danger' : 'info' }}" style="font-size: 14px;">
            {{ auth()->user()->role_label }}
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <!-- Welcome Message -->
<div class="card bg-white border-0 rounded-10 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold">
            <i data-feather="home"></i> Selamat Datang, {{ auth()->user()->nama }}!
        </h5>
        <p class="text-muted mb-0">
            Anda login sebagai <strong>{{ auth()->user()->role_label }}</strong>.
            @if(auth()->user()->isOwner())
                Anda memiliki akses sebagai owner.
            @else
                Anda memiliki akses terbatas sesuai role penjual.
            @endif
        </p>
    </div>
</div>
    {{-- <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total User</h6>
                        <h3 class="fw-bold">{{ $totalUsers }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-10">
                        <i data-feather="users" class="text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- @if (auth()->user()->level == 'owner')
        
        <div class="col-md-3 mb-4">
            <div class="card bg-white border-0 rounded-10 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Penjual</h6>
                            <h3 class="fw-bold">{{ $totalPenjual }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-10">
                            <i data-feather="user" class="text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Owner</h6>
                        <h3 class="fw-bold">{{ $totalOwner }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-10">
                        <i data-feather="user-check" class="text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
   
</div>



@endsection