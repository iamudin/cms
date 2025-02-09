@extends('cms::backend.layout.app', ['title' => get_post_type('title_crud')])
@section('content')
    <form class="editor-form" action="{{ route(get_post_type() . '.update', $post->id) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-12">
                <h3 style="font-weight:normal">
                    <i class="fa {{ $module->icon }}" aria-hidden="true"></i> {{ get_post_type('title_crud') }}
                    <a href="{{ route(get_post_type()) }}" class="btn btn-outline-danger btn-sm pull-right"
                        data-toggle="tooltip" title="Kembali Ke Index Data"> <i class="fa fa-undo" aria-hidden></i>
                        Kembali</a>
                </h3>
                <br>
                @if (!empty($post && $module->web->detail && $post->title && $post->status=='publish') && $module->public)
                    <div style="border-left:3px solid green" class="alert alert-success"><b>URL : </b><a
                            title="Kunjungi URL" data-toggle="tooltip" href="{{ url($post->url) }}"
                            target="_blank"><i><u>{{ str(url($post->url))->limit('160','...') }}</u></i></a> <span
                            title="Klik Untuk Menyalin alamat URL {{ $module->title }}" data-toggle="tooltip"
                            class="pointer copy pull-right badge badge-primary" data-copy="{{ url($post->url) }}"><i
                                class="fa fa-copy" aria-hidden></i> <b>Salin</b></span></div>
                @endif
                @include('cms::backend.layout.error')
            </div>
            <div class="col-lg-9">
                <div class="form-group">
                    <input data-toggle="tooltip" title="Masukkan {{ $module->datatable->data_title }}" required
                        name="title" type="text" value="{{ $post->title ?? '' }}"
                        placeholder="Masukkan {{ $module->datatable->data_title }}" class="form-control form-control-lg">

                </div>

                @if ($module->form->editor)
                    <div class="form-group">
                        @if ($post->mime == 'html')
                            <textarea style="height:70vh" class="form-control text-white bg-dark" name="content" placeholder="Masukkan Script HTML">{{ get_custom_view($post->id) }}</textarea>
                        @else
                            <textarea name="content" placeholder="Keterangan..." id="editor">{{ $post->content ?? '' }}</textarea>
                        @endif
                    </div>
                @endif

                @if ($pp = $module->form->post_parent)
                    <?php
                    if (isset($pp[1])) {
                        if (isset($pp[2]) && $pp[2] != 'all') {
                            // echo "<script>alert('oi');</script>";
                            $par = Udiko\Cms\Models\Post::withwherehas('category', function ($q) {
                                $q->where('slug', $pp[2]);
                            })
                                ->whereType($pp[1])
                                ->whereStatus('publish')
                                ->select('id', 'title')
                                ->get();
                        } else {
                            $par = Udiko\Cms\Models\Post::whereType($pp[1])
                                ->whereStatus('publish')
                                ->select('id', 'title')
                                ->get();
                        }
                    }
                    ?>
                    <h6>{{ $pp[0] }}</h6>
                    <select @if (isset($pp[3]) && $pp[3] == 'required') required @endif class="form-control form-control-sm"
                        name="parent_id">
                        <option value="">--pilih--</option>

                        @foreach ($par as $row)
                            <option @if ($post && $post->parent_id == $row->id) selected @endif value="{{ $row->id }}">
                                {{ $row->title }}</option>
                        @endforeach

                    </select>

                @endif

                @if ($module->form->custom_field)
                    @include('cms::backend.posts.custom_field.form')

                @endif
                @if ($module->form->looping_data)
                @include('cms::backend.posts.looping_data.form')
                @endif


                @if (get_module_info('looping') || (get_post_type() == 'halaman' && $post->mime_type == 'html'))
                    <br>
                    <h6 style="border-bottom:1px dashed #000;font-weight:normal"><b>{{ get_module_info('looping') }}</b>
                        <span class="text-muted pull-right">{{ get_module_info('looping_for') }}</span> </h6>

                    @if (get_module_info('post_type') != 'menu')
                        @if (get_module_info('post_type') == 'lasyanan')
                            @include('admin.hasil-skm')
                        @else
                            @include('cms::backend.looping-data')
                        @endif
                    @else
                        @include('cms::backend.list-menu')
                    @endif

                @endif
            </div>
            <div class="col-lg-3">
                @if ($module->form->thumbnail)
                    <div class="card">
                        <p class="card-header"> <i class="fa fa-image" aria-hidden></i> Gambar</p>

                        <img class="img-responsive" style="border:none" id="thumb" src="{{ $post->thumbnail }}"/>
                        <input onchange="readURL(this);" accept="image/png,image/jpeg" type="file" class="form-control-file form-control-sm"
                            name="media" value="">
                        @if ($module->web->index && $module->web->detail)
                            <span style="padding:10px">
                                <textarea placeholder="Keterangan Gambar" type="text" class="form-control form-control-sm"
                                    name="media_description">{{ $post->media_description ?? '' }}</textarea>
                            </span>
                        @endif

                    </div>

                @endif
                @if (get_post_type() == 'halaman')
                    <small>Jenis Halaman {!! help('Default (Halaman standar), Custom HTML (Source Code HTML dinamis / static / copy paster sumber lain)') !!}</small>
                    <select class="form-control form-control-sm" name="mime">
                        <option value="">Default</option>
                        <option value="html" {{ $post && $post->mime == 'html' ? 'selected' : '' }}>Custom HTML</option>
                    </select>
                    
                @endif
                @if ($module->web->detail || $modname = $module->name=='banner')
                    <small>Pengalihan URL {!! help('Opsi Jika Ingin Mengalihkan Konten Ini ke suatu halaman web atau url') !!} </small>
                    <input type="text" class="form-control form-control-sm" name="redirect_to"
                        placeholder="URL dimulai https:// atau http://" value="{{ $post->redirect_to ?? '' }}">
                    @if(!isset($modname))
                    <small for="">Deskripsi {!! help('Opsi deskripsi singkat tentang konten yang dapat ditelusuri oleh mesin pencarian') !!} </small>
                    <textarea placeholder="Tulis Deskripsi" type="text" class="form-control form-control-sm" name="description">{{ $post->description ?? '' }}</textarea>
                    <small for="">Kata Kunci {!! help('Kata kunci tentang konten yang dapat ditelusuri oleh mesin pencarian') !!}</small>
                    <input placeholder="Keyword1,Keyword2,Keyword3" type="text" class="form-control form-control-sm"
                        name="keyword" value="{{ $post->keyword ?? '' }}">
                        @endif
                @if ($module->form->tag)

                        <small for="">Tags {!! help('Penanda untuk memudahkan pencarian topik') !!}</small>
                        <select name="tags[]" id="select2" class="form-control form-control-sm form-control-select" multiple id="">
                            @foreach($tags as $row)
                            <option  {{ $post?->tags()->find($row->id) ? 'selected' : '' }} value="{{  $row->id}}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                       
                @else
                @endif

                @endif
                @if ($module->form->category)
                    <small for="">Kategori {{ $module->title }} </small><br>
                    <select class="form-control form-control-sm" name="category_id">
                        <option value=""> --pilih-- </option>
                        @foreach ($category as $row)
                            <option value="{{ $row->id }}"
                                {{ $row->id == $post->category_id ? 'selected=selected' : '' }}>{{ $row->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="text-right"><small class="text-primary"><a href="{{ route($post->type . '.category') }}"> <i
                                    class="fa fa-plus" aria-hidden></i> Tambah Baru</a></small></div>
                @else
                @endif
                @if ($module->web->sortable && $module->web->index)
                <small for="">Urutan {!! help('Urutan konten yang akan ditampilkan') !!}</small>
                <select class="form-control form-control-sm" name="sort">
                    @php $count = query()->whereType(get_post_type())->count();@endphp
                    @for ($i=1; $i<=$count; $i++)
                        <option value="{{ $i }}"  {{ $post->sort ==$i ? 'selected=selected' : '' }}>{{ $i }}
                        </option>
                    @endfor
                </select>
                <div class="mb-2"></div>

                @else 
                <div class="mb-2"></div>

                @endif

                @if ($module->web->detail)

                    <div @if (!$module->web->detail) ) style="margin-top:10px" @endif class="animated-checkbox">
                        <label>
                            <input type="checkbox" {{ $post && $post->allow_comment == 'Y' ? 'checked=checked' : '' }}
                                name="allow_comment" value="Y"><span class="label-text"><small>Izinkan Komentar
                                    {!! help('Jika dicentang, maka pengunjung bisa mengirim komentar pada postingan ini') !!}</small></span>
                        </label>
                    </div>
                @endif
                @if ($module->form->thumbnail)
                    <div class="animated-checkbox">
                        <label>
                            <input {{ $post && $post->pinned == 'Y' ? 'checked=checked' : '' }} type="checkbox"
                                name="pinned" value="Y"><span class="label-text"><small>Sematkan
                                    {!! help('Jika dicentang, maka postingan ini akan menjadi prioritas dihalaman jika dikondisikan pada template ') !!}</small></span>
                        </label>
                    </div>
                @endif
                <div class="form-group form-inline">
                    <div class="animated-radio-button">
                        <label>
                            <input {{ $post && $post->status == 'publish' ? 'checked=checked' : '' }} required
                                type="radio" name="status" value="publish"><small
                                class="label-text">Publikasikan</small>
                        </label>
                    </div>
                    &nbsp;&nbsp;&nbsp;
                    <div class="animated-radio-button">
                        <label>
                            <input {{ $post && $post->status == 'draft' ? 'checked=checked' : '' }} required type="radio"
                                name="status" value="draft"><small class="label-text">Draft</small>
                        </label>
                    </div>
                </div>
                <button type="submit" data-toggle="tooltip" title="Simpan Perubahan"  class="btn btn-md btn-outline-primary w-100 add">SIMPAN</button><br><br>
            </>
        </div>
    </form>
    @if ($post->mime != 'html' && $module->form->editor)
        @push('styles')
            <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
        @endpush
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

        @endpush
        @include('cms::backend.layout.summernote')
    @endif
    @include('cms::backend.layout.js')

@endsection
