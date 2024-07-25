<?php
$config['web_type'] = 'Sekolah';
$config['option'] = array(
    ['Nama Sekolah','text'],
    ['Status Negeri','text','required'],
    ['Foto Gedung','file']
);
add_module([
            'position' => 3,
            'name' => 'itsa',
            'title' => 'ITSA',
            'description' => 'Menu Untuk Mengelola ITSA',
            'parent' => false,
            'icon' => 'fa-check',
            'route' => ['index','create','show','update','delete'],
            'datatable'=>[
                'custom_column' => false,
                'data_title' => 'Judul',
            ],
            'form'=>[
                'unique_title' => false,
                'post_parent' => ['Aplikasi','aplikasi'],
                'thumbnail' => false,
                'editor' => false,
                'category' => false,
                'tag' => false,
                'looping_name'=>'Lampiran',
                'looping_data' => array(
                    ['Temuan','text'],
                    ['Path','text'],
                    ['Perbaikan','file']

                ),
                'custom_field' => array(
                    ['Tanggal Pelaksanaan','date'],
                    ['Pelaksana','text'],
                    ['Penanggung Jawab','text'],
                ),
            ],
            'web'=>[
                'api' => true,
                'archive' => true,
                'index' => true,
                'detail' => true,
                'history' => true,
                'auto_query' => true,
                'sortable'=>false,
            ],
            'public' => true,
            'cache' => false,
            'active' => true,
]);
add_module([
            'position' => 3,
            'name' => 'opd',
            'title' => 'OPD',
            'description' => 'Menu Untuk Mengelola OPD',
            'parent' => false,
            'icon' => 'fa-building',
            'route' => ['index','create','show','update','delete'],
            'datatable'=>[
                'custom_column' => false,
                'data_title' => 'Nama OPD',
            ],
            'form'=>[
                'unique_title' => false,
                'post_parent' => false,
                'thumbnail' => false,
                'editor' => false,
                'category' => true,
                'tag' => false,
                'looping_name'=>'Arsip',
                'looping_data' => false,
                'custom_field' => false,
            ],
            'web'=>[
                'api' => true,
                'archive' => true,
                'index' => true,
                'detail' => true,
                'history' => true,
                'auto_query' => true,
                'sortable'=>false,
            ],
            'public' => false,
            'cache' => false,
            'active' => true,
]);
add_module([
            'position' => 3,
            'name' => 'aplikasi',
            'title' => 'Aplikasi',
            'description' => 'Menu Untuk Mengelola APlikasi',
            'parent' => false,
            'icon' => 'fa-adn',
            'route' => ['index','create','show','update','delete'],
            'datatable'=>[
                'custom_column' => 'Status Aplikasi',
                'data_title' => 'Nama Aplikasi',
            ],
            'form'=>[
                'unique_title' => false,
                'post_parent' => ['OPD','opd'],
                'thumbnail' => true,
                'editor' => false,
                'category' => true,
                'tag' => false,
                'looping_name'=>'Arsip',
                'looping_data' => false,
                'custom_field' => array(
                    ['Info :', 'break'],
                    ['Kegunaan', 'textarea'],
                    ['URL', 'text'],
                    ['Platform', ['Web','Mobile']],
                    ['File Dokumentasi', 'file'],
                    ['Link Dokumentasi', 'text'],
                    ['Tanggal Rilis', 'date'],
                    ['Status Aplikasi', ['Aktif','Nonaktif']],

                    ['Server / Hosting :', 'break'],
                    ['IP Server', 'text'],
                    ['Status Server', 'text'],
                    ['Nama Penyedia', 'text'],

                    ['Sumber Kode :', 'break'],
                    ['Repository', 'text'],
                    ['Visibilitas', ['Private','Public']],
                    ['Bahasa', 'text'],
                    ['DBMS', 'text'],

                    ['Pengembang :', 'break'],
                    ['Nama', 'text'],
                    ['Email', 'text'],
                    ['WA', 'number'],


                    )
            ],
            'web'=>[
                'api' => true,
                'archive' => true,
                'index' => true,
                'detail' => true,
                'history' => true,
                'auto_query' => true,
                'sortable'=>false,
            ],
            'public' => true,
            'cache' => false,
            'active' => true,
]);
use_module([
    'agenda'=>['active'=>false],
    'dokumentasi'=>['active'=>false],
    'sambutan'=>['active'=>false],
    'pegawai'=>['active'=>false],
    'document'=>['active'=>false],
    'unit-kerja'=>['active'=>false],
    'pengumuman'=>['active'=>false],
    'berita'=>['active'=>false],
    'aplikasi'=>['active'=>true],
    'opd'=>['active'=>true],
    'itsa'=>['active'=>true],
]);
