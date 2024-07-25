@extends('cms::backend.layout.app',['title'=>get_post_type('title_crud')])
@section('content')
<div class="row">
<div class="col-lg-12 mb-3">
  <h3 style="font-weight:normal;float:left"><i class="fa {{get_module_info('icon')}}" aria-hidden="true"></i> {{get_post_type('title_crud')}}
</h3>
<div class="pull-right">@if(get_post_type()!='media') <a href="{{admin_url(get_post_type().'/create')}}" class="btn btn-outline-primary btn-sm"> <i class="fa fa-plus" aria-hidden></i> Tambah</a> @endif @if(get_module_info('group')) <a href="{{admin_url(get_post_type().'/group')}}" class="btn btn-outline-dark btn-sm"> <i class="fa fa-tags" aria-hidden></i> Kategori</a> @endif
    {{-- <a href="" class="btn btn-outline-danger btn-sm" ><i class="fa fa-trash" ></i> Sampah</a> --}}
</div>
</div>
<!-- <select name="" id="" style="width:150px;" class="form-control form-control-sm pull-left ">
  <option value="" class="">--Tindakan Centang--</option>
  <option value="" class="">Publikasikan</option>
  <option value="" class="">Masuk Draft</option>
  <option value="" class=""><font color="#fff">Tong Sampah</font></option>
  <option value="" class="">Hapus</option>
</select>&nbsp; -->
{{-- <script>
           function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }

      var val = [];
       $('.post_id:checked').each(function(i){
       val[i] = $(this).val();
      });
       if(val.length > 0){
          alert(val);
         }
}

</script> --}}
<div class="col-lg-12">
<div class="table-responsive">

<table class="display table table-hover table-bordered datat" style="background:#f7f7f7;width:100%">
<thead style="text-transform:uppercase;color:#444">
  <tr>
    {{-- <th style="width:40px;vertical-align: middle">


            <span   data-toggle="dropdown" style="padding-left:4px;cursor: pointer;">
             <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
            </span>
            <div class="dropdown-menu" style="font-size:10px;text-transform: none" >
              <a class="dropdown-item" href="#"><input id="chk" onclick="toggle(this);" type="checkbox"> Centang Semua</a>
              <a class="dropdown-item" href="#">Pindahkan Ke Sampah</a>
              <a class="dropdown-item" href="#">Pendahkan Ke Draft</a>
            </div>
</th> --}}
    <th style="width:10px;vertical-align: middle">NO</th>
    @if(current_module()->form->thumbnail)
    <th style="width:55px;vertical-align: middle" >Gambar</th>
    @endif
    <th style="vertical-align: middle">{{current_module()->datatable->data_title}}</th>
    @if($parent = current_module()->form->post_parent)
    <th style="vertical-align: middle" >{{$parent[0]}}</th>
    @endif
    @if($custom = current_module()->datatable->custom_column)
    <th style="vertical-align: middle">{{$custom}}</th>
    @endif
    <th style="width:60px;vertical-align: middle">Dibuat</th>

    @if(get_post_type()!='media')<th style="width:60px;vertical-align: middle">Diubah</th>@endif
    @if(current_module()->web->detail)
    <th  style="width:30px;vertical-align: middle">Hits</th>
    @endif
    <th style="width:40px;vertical-align: middle">Aksi</th>
  </tr>
</thead>

<tbody style="background:#fff">

</tbody>


</table>
</div>

</div>
</div>
@include('cms::backend.datatable')
@push('styles')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.4.1/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

@endpush
@push('scripts')
<script type="text/javascript" src="{{secure_asset('backend/js/plugins/jquery.dataTables.min.js')}}"></script>
     <script type="text/javascript" src="{{secure_asset('backend/js/plugins/dataTables.bootstrap.min.js')}}"></script>
     <script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.min.js"></script>
     <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
     <script type="text/javascript">$('#sampleTable').DataTable();</script>
@endpush

@endsection
