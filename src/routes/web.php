<?php

use Illuminate\Support\Facades\Route;
use Udiko\Cms\Http\Controllers\WebController;
use Udiko\Cms\Http\Controllers\MediaController;
use Udiko\Cms\Http\Controllers\SetupController;

// Route::get('setup', [Udiko\Cms\Http\Controllers\SetupController::class, 'index']);
if (!config('modules.domain')){

$modules = collect(get_module())->where('name','!=','halaman')->where('active', true)->where('public', true);
    foreach($modules as $modul)
     {
            Route::controller(WebController::class)
                ->prefix($modul->name)
                ->middleware(['public'])
                ->group(function () use ($modul) {
                    if($modul->web->index){
                        Route::match(['get', 'post'],'/', 'index');
                    }
                    if ($modul->form->post_parent) {
                    Route::get('/' . $modul->form->post_parent[1] . '/{slug?}', 'post_parent');
                    }
                    if ($modul->web->api) {
                        Route::match(['get', 'post'],'api/{id?}', 'api');
                    }
                    if ($modul->web->detail) {
                        Route::match(['get', 'post'], '/{slug}', 'detail');
                    }
                    if ($modul->web->archive){
                        Route::match(['get', 'post'],'archive/{year?}/{month?}/{date?}', 'archive')->name($modul->name.'.archive');
                    }
                    if ($modul->form->category) {
                        Route::match(['get', 'post'], 'category/{slug}','category');
                    }

                });
    }
    Route::match(['get', 'post'], 'tags/{slug}', [WebController::class, 'tags'])->middleware(['public']);
    Route::match(['get', 'post'], 'author/{user:slug}', [WebController::class, 'author'])->middleware(['public']);
    Route::match(['get', 'post'], 'search/{slug?}', [WebController::class, 'search'])->middleware(['public']);
    Route::match(['get', 'post'], '/{slug}', [WebController::class, 'detail'])
->where('slug', '(?!' . implode('|', array_merge([admin_path(),'search','tags','install'],$modules->pluck('name')
->toArray())) . ')[a-zA-Z0-9-_]+')->middleware(['public']);

Route::match(['get', 'post'],'/', [WebController::class, 'home'])->name('home')->middleware(['public']);


Route::match(['get', 'post'],'install', [SetupController::class, 'index'])->name('install');
Route::match(['get', 'post'],'install/initializing', [SetupController::class, 'initializing'])->name('initializing');

}
Route::match(['get', 'post'],'media/{slug}', [MediaController::class, 'stream_by_id'])->name('stream');
Route::match(['get', 'post'],'download/{slug}', [MediaController::class, 'download_by_id'])->name('download');


