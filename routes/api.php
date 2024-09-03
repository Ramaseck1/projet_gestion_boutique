<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);


Route::middleware('auth:api')->group(function () {
    // Route pour mettre à jour la quantité de stock d'un seul article
    Route::patch('v1/articles/stock/{id}', [ArticleController::class, 'updateStock']);
    // Lister tous les articles
    Route::get('v1/articles', [ArticleController::class, 'index']);

    // Route pour mettre à jour la quantité de stock de plusieurs articles
    Route::get('/v1/articles', [ArticleController::class, 'index']);
    // Créer un nouvel article
    Route::post('/v1/articles', [ArticleController::class,'store']);


  // Lister un article à partir de l'ID
  Route::get('/v1/articles/{id}', [ArticleController::class, 'showById']);

  // Lister un article à partir de son libellé
  Route::post('/v1/articles/libelle', [ArticleController::class, 'showByLibelle']);

    // Route pour mettre à jour la quantité de stock de plusieurs articles
    Route::post('v1/articles/multiplestock', [ArticleController::class, 'updateMultipleStocks']);


    //Utilisateur

    Route::post('/v1/users', [UserController::class, 'store']);
    // Lister tous les utilisateurs par rôle
     
    Route::get('/v1/liste', [UserController::class, 'liste']);
    
    // Lister tous les utilisateurs
    Route::get('/v1/users', [UserController::class, 'index']);

    // Désactiver un compte utilisateur

    Route::post('/users/{id}/deactivate', [UserController::class, 'deactivate']);



    //client
    Route::post('/v1/clients', [ClientController::class, 'store']);
    Route::get('/v1/clients', [ClientController::class, 'index']);
    Route::get('/clients', [ClientController::class, 'indexWithAccounts'])->where('comptes', 'oui|non');
    Route::get('/clients/ActiveStatus', [ClientController::class, 'indexActiveStatus'])->where('active', 'oui|non');
    Route::post('/clients/telephone', [ClientController::class, 'searchByTelephone']);
    Route::Get('/v1/clients/{id}/dettes', [ClientController::class, 'listDettes']);
    Route::Get('/v1/clients/{id}/user', [ClientController::class, 'ShowUser']);




});

