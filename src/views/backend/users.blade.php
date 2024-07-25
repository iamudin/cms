@extends('views::backend.layout.app',['title'=>'Pengguna'])
@section('content')
<div class="row">
<div class="col-lg-12"><h3 style="font-weight:normal"> <i class="fa fa-users" aria-hidden="true"></i> Pengguna <a href="javascript:void(0)" onclick="$('#thumb').attr('src','{{thumb('gbr.jpg')}}');$('input[type=text]' ).val('');;$('input[type=email]' ).val('');$('.save').val('add');$('.modtitle').html('Tambah');$('.modal').modal('show')" class="btn btn-outline-primary btn-sm pull-right"> <i class="fa fa-plus" aria-hidden></i> Tambah</a></h3>
  <br>
<table class="table table-hover table-bordered" id="sampleTable">
  <thead  style="background:#f7f7f7">
    <tr>
    <th style="width:15px">No</th>
    <th>Foto</th>
    <th>Nama</th>
    <th>Email</th>
    <th>Username</th>
    <th>Level</th>
    <th>Status</th>

    <th style="width:30px">Aksi</th>
  </tr>
</thead>
  <tbody style="background:#fff;">
@foreach($users as $k=>$row)
<tr>
  <td align="center">{{$k+1}}</td>
  <td style="width:40px"> <img class="img-thumbnail" src="{{thumb($row->photo)}}" height="50" alt=""> </td>
  <td>{{$row->name}}</td>
  <td>{{$row->email}}</td>
  <td><b>{{$row->username}}</b></td>
  <td><b>{{$row->level}}</b></td>
  <td class="sts-{{$k}}">{{$row->status}}</td>

  <td align="center"> <a href="javascript::void(0)" onclick="$('.oldpass').val('{{$row->password}}');$('.oldphoto').val('{{$row->photo}}');$('.photo').removeAttr('required');$('#thumb').attr('src','{{thumb($row->photo)}}');$('.save').val('{{$row->id}}');$('.modtitle').html('Edit');$('.email').val('{{$row->email}}');$('.name').val('{{$row->name}}');$('.level option[value={{$row->level}}]').attr('selected','selected');$('.username').val('{{$row->username}}');if($('.sts-{{$k}}').html() == 'Aktif'){$('.status').attr('checked','checked')}else{$('.status').removeAttr('checked');} $('.modal').modal('show')" title="Edit"><i class="fa fa-edit"></i></a> &nbsp;<a  title="Hapus" onclick="deleteAlert('{{admin_url('users?delete='.$row->id)}}')" href="javascript:void(0)" class="text-danger" ><i class="fa fa-trash"></i></a></td>
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
        <h5 class="modal-title"><span class="modtitle"></span> Pengguna</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <form class="" action="{{URL::full()}}" method="post" enctype="multipart/form-data">
        @csrf
      <div class="modal-body">
        <div class="form-group">
               <center><img class="img-responsive" style="border:none;width:100%" id="thumb" src="{{thumb('user.png')}}" /></center><br>
               <input type="hidden" class="oldphoto" name="oldphoto" value="">
          <label for="">Foto Pengguna</label>
          <input onchange="readURL(this);"  type="file" class="form-control photo" name="photo" value="">
        </div>

          <div class="form-group">
            <label for="">Nama</label>
            <input required type="text" class="form-control name" name="name" placeholder="Masukkan Nama" value="">
          </div>
          <div class="form-group">
            <label for="">Email</label>
            <input required type="email" class="form-control email" name="email" placeholder="Masukkan Email" value="">
          </div>
          <div class="form-group">
            <label for="">Level</label>
            <select name="level" class="form-control level" id="">
              <option value="">--pilih level--</option>
              <option value="operator">Operator</option>
            </select>
          </div>

          <div class="form-group">
            <label  for="">Username</label>
            <input required type="text" class="form-control username" name="username" placeholder="Masukkan Username" value="username">
          </div>
          <div class="form-group">
            <label for="">Password</label>
            <input type="password" class="form-control password" name="password" placeholder="Masukkan Password" value="">
            <input type="hidden" class="oldpass" name="oldpass" value="">
          </div>
          <div class="form-group">
            <label for="">Status Pengguna</label>
            <div class="toggle-flip">
                            <label>
                              <input class="status" type="checkbox" name="status" value="Aktif"><span class="flip-indecator" data-toggle-on="Aktif" data-toggle-off="Nonaktif"></span>
                            </label>
                          </div>

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
<script>
    function readURL(input) {
      const allow = ['gif','png','jpeg','jpg'];
      var ext = input.value.replace(/^.*\./, '');
      if(!allow.includes(ext)){
        alert('Pilih hanya gambar');
        input.value='';
      }else {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#thumb')
                    .attr('src', e.target.result)
                    .width('100%')
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
  }
  </script>
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
