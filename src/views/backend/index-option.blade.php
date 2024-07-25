@extends('admin.layout.app',['title'=>get_module_info('title_crud')])
@section('content')
<div class="row">
<div class="col-lg-12"><h3 style="font-weight:normal"><i class="fa {{get_module_info('icon')}}" aria-hidden="true"></i> {{get_module_info('title_crud')}} <a href="{{admin_url(get_post_type().'/create')}}" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-save" aria-hidden></i> Simpan</a></h3>
<br>
@foreach(get_module_info('is_option') as $row)
<small for="">{{$row[0]}}</small>
<input  type="{{$row[2]}}" value="" class="form-control form-control-sm" name="" placeholder="Entri {{$row[0]}} ">
@endforeach
</div>
</div>

@endsection
