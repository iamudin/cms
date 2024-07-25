@extends('cms::backend.layout.app',['title'=>get_post_type('title_crud')])
@section('content')
<div class="row">
<div class="col-lg-12 mb-3">
  <h3 style="font-weight:normal;float:left"><i class="fa {{get_module_info('icon')}}" aria-hidden="true"></i> {{get_post_type('title_crud')}}
</h3>
<div class="pull-right">
    @if(Route::has(get_post_type().'.category'))
    <a href="{{route(get_post_type().'.category')}}" class="btn btn-outline-danger btn-sm"> <i class="fa fa-undo" aria-hidden></i> Batal</a>
    @endif
</div>
</div>
<div class="col-lg-12">
    @if ($category && current_module()->public)
    <div style="border-left:3px solid green" class="alert alert-success"><b>URL : </b><a
            title="Kunjungi URL" data-toggle="tooltip" href="{{ url($category->url) }}"
            target="_blank"><i><u>{{ url($category->url) }}</u></i></a> <span
            title="Klik Untuk Menyalin alamat URL Kategori" data-toggle="tooltip"
            class="pointer copy pull-right badge badge-primary" data-copy="{{ url($category->url) }}"><i
                class="fa fa-copy" aria-hidden></i> <b>Salin</b></span></div>
@endif
@include('cms::backend.layout.error')
        <form action="{{ $category ?  route(get_post_type().'.category.update',$category->id): route(get_post_type().'.category.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            @if($category)
            @method('PUT')
            @endif
            <div class="form-group mt-2 mb-2">
                <label class="mb-0">Nama</label>
                  <input class="form-control form-control-sm " name="name" type="text" placeholder="Masukkan Nama Kategori" value="{{$category ? $category->name : old('name')}}">
            </div>
            <div class="form-group mt-2  mb-2">
                <label class="mb-0">Keterangan</label>
                  <textarea class="form-control " name="description" placeholder="Masukkan Keterangan">{{$category ? $category->description : old('description')}}</textarea>
            </div>
            <div class="form-group mt-2  mb-2">
                <label class="mb-0">Urutan</label>
                  <input class="form-control form-control-sm " name="sort" type="text" placeholder="Masukkan Nama Kategori" value="{{$category ? $category->sort : old('sort')}}">
            </div>
            <div class="form-group mt-2  mb-2">
                <label class="mb-0">Icon</label>
                @if($category && $category->icon && media_exists($category,$category->icon))
                <br><img src="{{ url($category->icon) }}" style="height: 70px" class="img-thumbnail"> <a href="javascript:void(0)" onclick="media_destroy('{{ $category->icon }}')" class="btn-danger btn-sm"> <i class="fa fa-trash text-white"></i> </a>
                @else
                  <input accept="image/png,image/jpeg"  class=" form-control-sm form-control-file " name="icon"  type="file" value="{{$category?->icon}}">
                @endif
            </div>
            <div class="form-group mt-2  mb-2">
                <label class="mb-0">Status</label><br>
                @foreach(['publish','draft'] as $row)
                  <input name="status"  type="radio" value="{{$row}}" {{ $category && $category->status==$row ? 'checked':'' }}> {{ str($row)->headline() }} &nbsp; &nbsp;
                  @endforeach
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
