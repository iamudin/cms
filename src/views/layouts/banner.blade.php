@if($data)
<div style="padding:10px;">
<small>Banner {{ $banner }}</small>
</div>
@else
<div style="width:100%;border:2px dashed #222;vertical-align:center;text-align:center;background:#f5f5f5;margin:10px 0">
<h6 style="padding:50px 0;color:#bbb">Pasang Banner {{ $banner }} Disini</h6>
</div>
@endif
