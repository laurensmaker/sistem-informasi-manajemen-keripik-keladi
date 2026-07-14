@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Transaksi Penjualan</h3>
    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="formPenjualan" action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">No. Transaksi</label>
                        <input type="text" name="no_transaksi" class="form-control" value="{{ $noTransaksi }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Pembeli <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control" placeholder="Masukkan nama pembeli" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">No. HP Pembeli</label>
                        <input type="text" name="no_hp_pembeli" id="no_hp_pembeli" class="form-control" placeholder="Masukkan no. HP">
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="fw-bold mb-3">Detail Pesanan</h5>

            <div id="items-container">
                <div class="row item-row mb-3" data-index="0">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Jenis Keripik <span class="text-danger">*</span></label>
                            <select name="items[0][jenis_keripik_id]" class="form-select produk-select" required>
                                <option value="">Pilih Produk</option>
                                @foreach($jenisKeripik as $item)
                                    @php
                                        $stok = $item->stokKeripik->first();
                                    @endphp
                                    <option value="{{ $item->id }}" 
                                            data-harga="{{ $item->harga_jual }}"
                                            data-stok="{{ $stok->jumlah_stok ?? 0 }}"
                                            data-nama="{{ $item->nama_jenis }}">
                                        {{ $item->nama_jenis }} - 
                                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }} 
                                        (Stok: {{ $stok->jumlah_stok ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="text" class="form-control harga-produk" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][jumlah]" class="form-control jumlah-produk" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control subtotal-produk" readonly>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">
                                <i data-feather="trash-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="button" id="add-item" class="btn btn-success btn-sm">
                        <i data-feather="plus"></i> Tambah Produk
                    </button>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Harga</th>
                            <td class="text-end">
                                <h5 class="fw-bold text-primary" id="total-harga">Rp 0</h5>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                        <i data-feather="save"></i> Proses Transaksi
                    </button>
                    <button type="reset" class="btn btn-warning btn-lg" id="btnReset">
                        <i data-feather="refresh-ccw"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============================================
    // PASTIKAN SEMUA ELEMENT SUDAH LOAD
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Ready - Inisialisasi Form Penjualan');
        
        // Inisialisasi feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // ============================================
        // VARIABEL GLOBAL
        // ============================================
        let itemIndex = 1;
        const container = document.getElementById('items-container');
        const totalHargaEl = document.getElementById('total-harga');
        const form = document.getElementById('formPenjualan');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnAddItem = document.getElementById('add-item');

        // ============================================
        // FUNGSI FORMAT RUPIAH
        // ============================================
        function formatRupiah(angka) {
            if (isNaN(angka) || angka === 0) return '0';
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // ============================================
        // FUNGSI HITUNG SUBTOTAL PER ROW
        // ============================================
        function calculateSubtotal(row) {
            const hargaInput = row.querySelector('.harga-produk');
            const jumlahInput = row.querySelector('.jumlah-produk');
            const subtotalInput = row.querySelector('.subtotal-produk');
            
            const hargaText = hargaInput.value.replace(/[^0-9]/g, '');
            const harga = parseInt(hargaText) || 0;
            const jumlah = parseInt(jumlahInput.value) || 0;
            const subtotal = harga * jumlah;
            
            subtotalInput.value = 'Rp ' + formatRupiah(subtotal);
            calculateTotal();
        }

        // ============================================
        // FUNGSI HITUNG TOTAL SEMUA
        // ============================================
        function calculateTotal() {
            let total = 0;
            const subtotalInputs = document.querySelectorAll('.subtotal-produk');
            
            subtotalInputs.forEach(function(el) {
                const val = el.value.replace(/[^0-9]/g, '');
                total += parseInt(val) || 0;
            });
            
            totalHargaEl.textContent = 'Rp ' + formatRupiah(total);
        }

        // ============================================
        // FUNGSI TAMBAH ROW PRODUK
        // ============================================
        function addItemRow() {
            console.log('Tambah item ke-' + itemIndex);
            
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);
            
            // Reset semua input di row baru
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach(function(el) {
                el.value = '';
            });
            
            // Reset select
            const select = newRow.querySelector('.produk-select');
            select.value = '';
            
            // Set nilai default
            const hargaInput = newRow.querySelector('.harga-produk');
            const jumlahInput = newRow.querySelector('.jumlah-produk');
            const subtotalInput = newRow.querySelector('.subtotal-produk');
            const removeBtn = newRow.querySelector('.remove-item');
            
            hargaInput.value = '';
            subtotalInput.value = '';
            jumlahInput.value = 1;
            jumlahInput.disabled = false;
            jumlahInput.dataset.maxStok = 0;
            jumlahInput.removeAttribute('max');
            
            // Tampilkan tombol hapus
            removeBtn.style.display = 'inline-block';
            
            // Update name attributes
            const allElements = newRow.querySelectorAll('[name^="items[0]"]');
            allElements.forEach(function(el) {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace('0', itemIndex));
                }
            });
            
            // Update data-index
            newRow.dataset.index = itemIndex;
            
            // Hapus class is-invalid jika ada
            newRow.querySelectorAll('.is-invalid').forEach(function(el) {
                el.classList.remove('is-invalid');
            });
            
            container.appendChild(newRow);
            itemIndex++;
            calculateTotal();
            
            console.log('Item berhasil ditambahkan. Total item: ' + document.querySelectorAll('.item-row').length);
        }

        // ============================================
        // EVENT: CHANGE PRODUK SELECT
        // ============================================
        container.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('produk-select')) {
                console.log('Produk dipilih:', e.target.value);
                
                const select = e.target;
                const selectedOption = select.options[select.selectedIndex];
                const harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;
                const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
                const row = select.closest('.item-row');
                
                const hargaInput = row.querySelector('.harga-produk');
                const jumlahInput = row.querySelector('.jumlah-produk');
                
                hargaInput.value = 'Rp ' + formatRupiah(harga);
                jumlahInput.setAttribute('max', stok);
                jumlahInput.dataset.maxStok = stok;
                
                if (stok === 0) {
                    jumlahInput.value = 0;
                    jumlahInput.disabled = true;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Habis!',
                        text: 'Stok produk ini habis, tidak bisa dipesan.'
                    });
                } else {
                    jumlahInput.disabled = false;
                    if (parseInt(jumlahInput.value) === 0 || !jumlahInput.value) {
                        jumlahInput.value = 1;
                    }
                }
                
                calculateSubtotal(row);
            }
        });

        // ============================================
        // EVENT: INPUT JUMLAH
        // ============================================
        container.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('jumlah-produk')) {
                const input = e.target;
                const row = input.closest('.item-row');
                const maxStok = parseInt(input.dataset.maxStok) || 0;
                let jumlah = parseInt(input.value) || 0;
                
                if (jumlah > maxStok && maxStok > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Tidak Cukup!',
                        text: 'Stok tersedia: ' + maxStok
                    });
                    input.value = maxStok;
                    jumlah = maxStok;
                }
                
                if (jumlah < 0) {
                    input.value = 0;
                    jumlah = 0;
                }
                
                calculateSubtotal(row);
            }
        });

        // ============================================
        // EVENT: TAMBAH ITEM (BUTTON)
        // ============================================
        if (btnAddItem) {
            btnAddItem.addEventListener('click', function(e) {
                e.preventDefault();
                addItemRow();
            });
        } else {
            console.error('Tombol "Tambah Produk" tidak ditemukan!');
        }

        // ============================================
        // EVENT: HAPUS ITEM
        // ============================================
        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-item');
            if (removeBtn) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    const row = removeBtn.closest('.item-row');
                    row.remove();
                    calculateTotal();
                    console.log('Item dihapus. Sisa item: ' + document.querySelectorAll('.item-row').length);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Minimal harus ada 1 item!'
                    });
                }
            }
        });

        // ============================================
        // EVENT: SUBMIT FORM
        // ============================================
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form disubmit');

                let valid = true;
                let errorMessage = '';
                
                // Validasi produk
                const produkSelects = document.querySelectorAll('.produk-select');
                produkSelects.forEach(function(el) {
                    if (!el.value) {
                        valid = false;
                        el.classList.add('is-invalid');
                        errorMessage = 'Silakan pilih produk untuk semua item!';
                    } else {
                        el.classList.remove('is-invalid');
                    }
                });

                // Validasi jumlah
                const jumlahInputs = document.querySelectorAll('.jumlah-produk');
                jumlahInputs.forEach(function(el) {
                    const jumlah = parseInt(el.value) || 0;
                    if (jumlah <= 0) {
                        valid = false;
                        el.classList.add('is-invalid');
                        errorMessage = 'Jumlah harus lebih dari 0!';
                    } else {
                        el.classList.remove('is-invalid');
                    }
                });

                // Validasi nama pembeli
                const namaPembeli = document.getElementById('nama_pembeli');
                if (!namaPembeli.value.trim()) {
                    valid = false;
                    namaPembeli.classList.add('is-invalid');
                    errorMessage = 'Nama pembeli wajib diisi!';
                } else {
                    namaPembeli.classList.remove('is-invalid');
                }

                if (!valid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: errorMessage
                    });
                    return;
                }

                // Disable button
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

                const formData = new FormData(this);

                // Tambahkan CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                fetch("{{ route('penjualan.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: true
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i data-feather="save"></i> Proses Transaksi';
                        feather.replace();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan!'
                        });
                    }
                })
                .catch(function(error) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i data-feather="save"></i> Proses Transaksi';
                    feather.replace();
                    
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan pada server!'
                    });
                });
            });
        } else {
            console.error('Form tidak ditemukan!');
        }

        // ============================================
        // EVENT: RESET FORM
        // ============================================
        const btnReset = document.getElementById('btnReset');
        if (btnReset) {
            btnReset.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Form direset');
                
                // Reset form
                form.reset();
                
                // Reset item rows
                const rows = container.querySelectorAll('.item-row');
                rows.forEach(function(row, index) {
                    if (index > 0) {
                        row.remove();
                    }
                });
                
                // Reset row pertama
                const firstRow = container.querySelector('.item-row');
                if (firstRow) {
                    firstRow.querySelector('.produk-select').value = '';
                    firstRow.querySelector('.harga-produk').value = '';
                    firstRow.querySelector('.jumlah-produk').value = 1;
                    firstRow.querySelector('.subtotal-produk').value = '';
                    firstRow.querySelector('.remove-item').style.display = 'none';
                    firstRow.dataset.index = '0';
                    
                    // Reset name attributes
                    const allElements = firstRow.querySelectorAll('[name^="items[0]"]');
                    allElements.forEach(function(el) {
                        const name = el.getAttribute('name');
                        if (name) {
                            el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[0]'));
                        }
                    });
                }
                
                // Reset index
                itemIndex = 1;
                
                // Hitung ulang total
                calculateTotal();
                
                // Trigger change untuk row pertama
                const firstSelect = document.querySelector('.produk-select');
                if (firstSelect) {
                    firstSelect.dispatchEvent(new Event('change'));
                }
                
                Swal.fire({
                    icon: 'info',
                    title: 'Form Direset',
                    text: 'Semua data telah direset!',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // ============================================
        // TRIGGER AWAL UNTUK ROW PERTAMA
        // ============================================
        const firstSelect = document.querySelector('.produk-select');
        if (firstSelect) {
            console.log('Trigger change untuk row pertama');
            firstSelect.dispatchEvent(new Event('change'));
        }

        console.log('Inisialisasi selesai. Total item: ' + document.querySelectorAll('.item-row').length);
    });
</script>
@endpush