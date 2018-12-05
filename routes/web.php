<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (\App\Articles\ArticlesRepository $repository) {
    if (request('q')) {
        $articles = $repository->search(request('q'));
    } else {
        $articles = \App\Article::all();
    }
    return view('welcome', ['articles' => $articles]);
});
