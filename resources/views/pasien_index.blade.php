@extends('layouts.sbadmin2')

@section('content')
    <div class="card">
        <div class="card-header">
            {{ $judul }}
        </div>
        <div class="card-body">
            <a href="/pasien/create" class="btn btn-primary mb-2">Tambah Pasien</a>
            
            <div class="row mb-2">
                <div class="col">
                    <form method="GET" action="{{ url('pasien') }}">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Cari data pasien"
                                value="{{ request('q') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Nomor HP</th>
                        <th>Jenis kelamin</th>
                        <th>Status</th>
                        <th>alamat</th>
                        <th>Keluhan</th>
                        <th>Di Doktor</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pasien as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->kode_pasien }}</td>
                            <td>{{ $item->nama_pasien }}</td>
                            <td>{{ $item->nomor_hp }}</td>
                            <td>{{ $item->jenis_kelamin}}</td>
                            <td>{{ $item->status}}</td>
                            <td>{{ $item->alamat}}</td>
                            <td>{{ $item->keluhan}}</td>
                            <td>{{ $item->dokter_id}}</td>
                            <td>{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="/pasien/{{ $item->id }}/edit" class="btn btn-primary">Edit</a>
                                <form action="/pasien/{{ $item->id }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">Data tidak ada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $pasien->links() }}
            </div>
        </div>
    </div>
@endsection
