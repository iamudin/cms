@extends('views::backend.layout.app',['title'=>get_post_type('title_crud')])
@section('content')
<form class="editor-form" action="{{URL::full()}}" method="post" enctype="multipart/form-data">
   @csrf
   <div class="row">
      <div class="col-lg-12">
         <h3 style="font-weight:normal"> <i class="fa {{get_module_info('icon')}}" aria-hidden="true"></i> {{get_post_type('title_crud')}} <a href="{{admin_url(get_post_type())}}" class="btn btn-outline-danger btn-sm pull-right" data-toggle="tooltip" title="Kembali Ke Index Data"> <i class="fa fa-undo" aria-hidden></i> Kembali</a></h3>
         <br>

      </div>
      <div class="col-lg-9">
         <div class="form-group">
            <input data-toggle="tooltip" title="Masukkan {{get_module_info('data_title')}}"  required name="title"  type="text" {{collect(_loop($edit))->count() > 0 ? 'readonly': ''}} value="{{$edit->title ?? ''}}" placeholder="Masukkan {{get_module_info('data_title')}}" class="form-control form-control-lg">

         </div>




            @include('views::backend.list-menu')

      </div>
      <div class="col-lg-3">

         <div class="form-group form-inline">
            <div class="animated-radio-button">
               <label>
               <input {{($edit && $edit->status == 'publish')? 'checked=checked':''}} required type="radio" name="status" value="publish"><small class="label-text">Publikasikan</small>
               </label>
            </div>
            &nbsp;&nbsp;&nbsp;
            <div class="animated-radio-button">
               <label>
               <input {{($edit && $edit->status == 'draft')? 'checked=checked':''}} required type="radio" name="status" value="draft"><small class="label-text">Draft</small>
               </label>
            </div>
         </div>
         <button @if(Auth::user()->level=='admin' || Auth::user()==$edit->author) name="save" value="@if(empty($edit))add @else {{$edit->id}} @endif" type="submit" data-toggle="tooltip" title="Simpan Perubahan" @else type="button"  onclick="alert('Anda bukan pemilik konten ini!')" @endif class="btn btn-md btn-outline-primary w-100 add">SIMPAN</button><br><br>
      </div>
   </div>
</form>

<script type="text/javascript">
   function readFile(input) {
     const allow = ['gif','png','jpeg','jpg','zip','docx','doc','rar','pdf','xlsx','xls'];
     var ext = input.value.replace(/^.*\./, '');
     if(!allow.includes(ext)){
       alert("Format didukung : gif,png,jpeg,jpg,zip,docx,doc,rar,pdf,xlsx,xls");
       input.value='';
     }
   }
function delloop(path){
  var url = "{{admin_url(get_post_type().'/loop')}}";
     $.get( "ajax/test.html", function( data ) {
  $( ".result" ).html( data );
  alert( "Load was performed." );
});
   }
   function exeurl(url){
   $.get("{{url(admin_path().'/unlink?link=')}}"+url, function(data, status){

     notif("File Berhasil Dihapus","success");


       setTimeout(() => {
         location.reload();
 }, "1000")


 });
 }
</script>
@endsection
