@extends('cms::backend.layout.app',['title'=> $tag? 'Edit Tag':'Tambah Tag'])
@section('content')
<div class="row">
<div class="col-lg-12 mb-3">
  <h3 style="font-weight:normal;float:left"><i class="fa fa-tags" aria-hidden="true"></i> {{ $tag? 'Edit Tag':'Tambah Tag' }}
</h3>
<div class="pull-right">
    @if(Route::has('tag'))
    <a href="{{route('tag')}}" class="btn btn-outline-danger btn-sm"> <i class="fa fa-undo" aria-hidden></i> Batal</a>
    @endif
</div>
</div>
<div class="col-lg-12">
    @if ($tag)
    <div style="border-left:3px solid green" class="alert alert-success"><b>URL : </b><a
            title="Kunjungi URL" data-toggle="tooltip" href="{{ url($tag->url) }}"
            target="_blank"><i><u>{{ url($tag->url) }}</u></i></a> <span
            title="Klik Untuk Menyalin alamat URL Kategori" data-toggle="tooltip"
            class="pointer copy pull-right badge badge-primary" data-copy="{{ url($tag->url) }}"><i
                class="fa fa-copy" aria-hidden></i> <b>Salin</b></span></div>
@endif
@include('cms::backend.layout.error')
        <form autocomplete="off" action="{{ $tag ?  route('tag.update',$tag->id): route('tag.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            @if($tag)
            @method('PUT')
            @endif
            <div class="form-group mt-2 mb-2">
                <label class="mb-0">Nama</label>
                  <input class="form-control form-control-sm " name="name" type="text" placeholder="Masukkan Nama tag" value="{{$tag ? $tag->name : old('name')}}">
            </div>

            <div class="form-group mt-2  mb-2">
                <label class="mb-0">Deskripsi [ <i class="text-danger">Keterangan Singkat tentang Tag ini</i> ]</label>
                  <textarea class="form-control form-control-sm " name="description"  placeholder="Masukkan Keterangan">{{$tag ? $tag->description : old('description')}}</textarea>
            </div>

            <div class="form-group mt-2  mb-2 text-right">
                <button type="submit" class="btn btn-primary btn-sm"> <i class="fa fa-save"></i> Simpan</button>
            </div>
</form>
</div>
</div>
@push('scripts')
@include('cms::backend.layout.js')
@endpush
@endsection
