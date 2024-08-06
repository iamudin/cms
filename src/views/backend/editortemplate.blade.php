@extends('cms::backend.layout.app',['title'=>'Edit Tampilan'])
@section('content')
<div class="row">
<div class="col-lg-12 mb-3"><h3 style="font-weight:normal;float: left;"> <i class="fa fa-paint-brush"></i> Edit Tampilan </h3>
    <div class="pull-right">


        <button type="button" onclick="if(confirm('Anda Yakin ?')) $('.editorForm').submit()" class="btn btn-outline-primary btn-sm"> <i class="fa fa-save"></i> Simpan Perubahan</button>
        <a href="{{route('appearance')}}" class="btn btn-outline-danger btn-sm"> <i class="fa fa-undo" aria-hidden></i> Kembali</a>
    </div>

</div>

<div class="col-lg-2">
   <div class="text-right">
     <a href="" > + <i class="fa fa-folder"></i> </a> &nbsp;&nbsp;
    <a href="" > + <i class="fa fa-file"></i> </a>
</div>
@php
    $treeData = [];
    $data = getDirectoryContents(null, $treeData);
    renderTemplateFile($treeData);
    @endphp
</div>


<div class="col-lg-10">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.1/mode/php/php.min.js"></script>

    <form action="{{ url()->full() }}" class="editorForm" method="post">
        @csrf
    <textarea id="editor" name="file_src" >
    {{ $view }}
    </textarea>
</form>

    <script>

        var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
            lineNumbers: true,
            mode: "application/x-httpd-php",
            matchBrackets: true,
            indentUnit: 4,
            indentWithTabs: true,
            theme: "default"
        });
        $('.CodeMirror').attr('style','height:80vh');
    </script>


</div>
</div>

</div>
@include('cms::backend.layout.js')
@endsection
