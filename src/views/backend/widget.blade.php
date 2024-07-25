@extends('admin.layout.app',['title'=>get_module_info('title_crud')])
@section('content')
<div class="row">
<div class="col-lg-12"><h3 style="font-weight:normal">{{get_module_info('title_crud')}}</h3>
  <br>
</div>
</div>
<style media="screen">
<?php for($i=1; $i<100; $i++):?>
.w-py-{{$i}} {padding-top:{{$i}}px;padding-bottom:{{$i}}px;}
.w-px-{{$i}} {padding-left:{{$i}}px;padding-right:{{$i}}px;}
.w-pl-{{$i}} {padding-left:{{$i}}px;}
.w-pt-{{$i}} {padding-top:{{$i}}px;}
.w-pr-{{$i}} {padding-right:{{$i}}px;}
.w-pb-{{$i}} {padding-bottom:{{$i}}px;}
  .w-my-{{$i}} {margin-top:{{$i}}px;margin-bottom:{{$i}}px;}
  .w-mx-{{$i}} {margin-left:{{$i}}px;margin-right:{{$i}}px;}
  .w-ml-{{$i}} {margin-left:{{$i}}px;}
  .w-mt-{{$i}} {margin-top:{{$i}}px;}
  .w-mr-{{$i}} {margin-right:{{$i}}px;}
  .w-mb-{{$i}} {margin-bottom:{{$i}}px;}
  <?php endfor;?>
</style>
@include(widget_manager())
@endsection
