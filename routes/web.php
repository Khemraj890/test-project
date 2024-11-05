<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', [WebController::class,'index']);
// Route::get('/users', [WebController::class, 'users']);
Route::get('/', [WebController::class, 'index']);  // Display the form and DataTable
Route::get('/api/users', [WebController::class, 'getUsers']);  // Fetch paginated users for DataTable
Route::post('/api/users', [WebController::class, 'storeUser']); 
