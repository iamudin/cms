@extends('cms::backend.layout.app',['title'=>'Tampilan'])
@section('content')

<div class="row">
<div class="col-lg-12 mb-4"><h3 style="font-weight:normal;float: left;"> <i class="fa fa-paint-brush"></i> Tampilan </h3>
    <div class="pull-right">

        <a href="{{route('panel.dashboard')}}" class="btn btn-outline-danger btn-sm"> <i class="fa fa-undo" aria-hidden></i> Kembali</a>
    </div>

</div>



<div class="col-lg-2">
  <ul class="list-group mb-3">
    @foreach(config('modules.config.template_info') as $row)
    <li class="list-group-item" style="padding:4px 10px">
      <small>{{ str($row[0])->headline() }}</small><br>
      <h6>{{ $row[1] }}</h6>
    </li>
    @endforeach
  </ul>
  <div class="tile">
    <div class="tile-tile">
        <h6>Template Baru</h6>
    </div>
    <div class="tile-body">
        <form action="" method="post" enctype="multipart/form-data">
            @csrf
            <input type="file" class="template" name="template" style="display: none">
        <button type="button" onclick="$('.template').click()" class="btn btn-primary btn-sm w-100"> <i class="fa fa-file-archive-o"></i> Pilih File .zip</button>
        </form>
    </div>
  </div>
</div>
<div class="col-lg-10">
<iframe  src="{{ url('/') }}?self=1" frameborder="0" class="w-100" style="height: 80vh;border-radius:5px;border:4px solid rgb(48, 48, 48)"></iframe>
</div>
</div>
</div>

@endsection
