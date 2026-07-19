<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;

// お問い合わせフォーム
Route::get('/', [ContactController::class, 'index']);
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');
Route::post('/contacts', [ContactController::class, 'store']);
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contacts.thanks');

Route::middleware('auth')->group(function () {
    // 管理者画面
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy']);

    // エクスポート機能
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');

    // タグCRUD
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::post('/admin/tags', [TagController::class, 'store']);
    Route::put('/admin/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy']);
});

