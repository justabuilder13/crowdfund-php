<?php

// Liste de toutes les routes de l'application.
// Format: Route::méthode('/url', 'Contrôleur@action');

use App\Routes\Route;

// Accueil (vue statique)
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

// Projets (CRUD complet)
Route::get('/projects', 'ProjectController@index');
Route::get('/project/show', 'ProjectController@show');
Route::get('/project/create', 'ProjectController@create');
Route::post('/project/create', 'ProjectController@store');
Route::get('/project/edit', 'ProjectController@edit');
Route::post('/project/edit', 'ProjectController@update');
Route::post('/project/delete', 'ProjectController@delete');

// Récompenses
Route::get('/reward/create', 'RewardController@create');
Route::post('/reward/create', 'RewardController@store');
Route::post('/reward/delete', 'RewardController@delete');

// Contributions
Route::get('/pledge/create', 'PledgeController@create');
Route::post('/pledge/create', 'PledgeController@store');

// Commentaires
Route::post('/comment/create', 'CommentController@store');

// Utilisateurs
Route::get('/user/show', 'UserController@show');

Route::dispatch();
