@extends('views::backend.layout.app',['title'=>get_post_type('title_crud')])
@section('content')
<div class="row">
<div class="col-lg-12"><h3 style="font-weight:normal"> <i class="fa fa-tags" aria-hidden="true"></i> {{get_post_type('title_crud')}} <div class="pull-right">@if(is_admin())<a href="javascript:void(0)" onclick="$('input[type=text]' ).val('');$('textarea').val('');$('.save').val('add');$('.modtitle').html('Tambah');$('.modal').modal('show')" class="btn btn-outline-primary btn-sm "> <i class="fa fa-plus" aria-hidden></i> Tambah</a>@endif
 <a href="{{admin_url(get_post_type())}}" class="btn btn-outline-danger btn-sm" data-toggle="tooltip" title="Kembali Ke Index Data"> <i class="fa fa-undo" aria-hidden></i> Kembali</a></div></h3>
  <br>
<table class="table table-hover table-bordered dtclient" id="sampleTable">
<thead>
  <tr style="background:#f7f7f7">
    <th width="2%">No</th>
    <th>Urutan</th>
    <th>Nama</th>
    <th>Keterangan</th>
    <th>Jumlah Konten</th>
    <th>Status</th>
    <th width="18%">Link</th>
    @if(is_admin())<th width="40px">Aksi</th>@endif
  </tr>
</thead>
<tbody style="background:#fff">

@foreach($data as $k=>$row)
<tr>
  <td class="text-center">{{$k+1}}</td>
  <td class="text-center">{{$row->sort}}</td>
  <td >{{$row->name}}</td>
  <td>{{$row->description ?? '__'}}</td>
  <td class="text-center">{{$row->post->count()}}</td>
  <td class="text-center" title="Klik untuk mengganti status"><a href="@if(Auth::user()->level=='admin'){{URL::current().'?id='.$row->id.'&status='.$row->status}}@else # @endif">{!!$row->status =='1' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-warning">Draft</span>'!!}</a></td>
  <td class="text-center"> <a title="Lihat Isi Kategori di tampilan web" href="{{url($row->url)}}" target="_blank" class="btn btn-sm btn-outline-success"> Kunjungi </a>  <span title="Salin URL" data-copy="{{url($row->url)}}" target="_blank" class="btn btn-sm btn-outline-info pointer copy"> <i class="fa fa-copy" aria-hidden></i> </span> </td>
  @if(is_admin())<td class="text-center"><a href="javascript::void(0)" onclick="$('.save').val('{{$row->id}}');$('.modtitle').html('Edit');$('.group_name').val('{{$row->name}}');$('.sort').val('{{$row->sort}}');$('.group_url').val('{{$row->url}}');$('.group_description').val('{{$row->description}}');$('.modal').modal('show')" title="Edit"><i class="fa fa-edit"></i></a> &nbsp;<a  title="Hapus" @if($row->post->count()==0)onclick="deleteAlert('{{admin_url(get_post_type().'/group/delete/'.$row->id)}}')" @else onclick="alert('Kategori Tidak Bisa Dihapus, Memiliki Konten Yang terkait')" @endif href="javascript:void(0)" class="text-danger" ><i class="fa fa-trash"></i></a></td>@endif
</tr>
@endforeach
</tbody>

</table>
</div>
</div>
<div class="modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="modtitle"></span> {{get_module_info('title_crud')}}</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <form class="" action="{{URL::full()}}" method="post">
        @csrf
      <div class="modal-body">
          <div class="form-group">
            <label for="">Nama Kategori</label>
            <input type="text" class="form-control group_name" name="name" placeholder="Masukkan Nama Kategori" value="">
          </div>
          <div class="form-group">
            <label for="">Urutan</label>
            <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="number" class="form-control sort" name="sort" placeholder="Masukkan Nama Kategori" value="">
          </div>
          <div class="form-group">
            <label for="">Group Url</label>
            <input type="text" class="form-control group_url" name="" placeholder="(Otomatis)" value="" disabled>
          </div>
          <div class="form-group">
            <label for="">Keterangan</label>
            <textarea type="text" class="form-control group_description" name="description" placeholder="Masukkan Keterangan Kategori"></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary save" type="submit" name="save" value="">Simpan</button>
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
      </div>
    </form>

    </div>
  </div>
</div>
@push('scripts')
<script type="text/javascript" src="{{secure_asset('backend/js/plugins/jquery.dataTables.min.js')}}"></script>
     <script type="text/javascript" src="{{secure_asset('backend/js/plugins/dataTables.bootstrap.min.js')}}"></script>
     <script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.min.js"></script>
     <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
     <script type="text/javascript">$('#sampleTable').DataTable();</script>
@endpush
@endsection
