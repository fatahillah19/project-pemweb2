<?php

use Illuminate\Support\Facades\Route;
use App\Models\Grade;
use App\Models\ReportCard;
use Firefly\FilamentBlog\Http\Controllers\PostController;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    if (! Schema::hasTable('fblog_posts')) {
        return response()->view('home', [
            'featuredBlog' => null,
            'blogs' => collect(),
        ]);
    }

    return app(PostController::class)->index(request());
});

Route::get('/admin/report-cards/{reportCard}/print', function (ReportCard $reportCard) {
    $reportCard->load('student.kelas');

    $grades = Grade::query()
        ->with('subject.guru')
        ->where('id_siswa', $reportCard->id_siswa)
        ->when(
            Schema::hasColumn('nilai', 'tahun_ajaran'),
            fn ($query) => $query->where('tahun_ajaran', $reportCard->tahun_ajaran),
        )
        ->when(
            Schema::hasColumn('nilai', 'semester'),
            fn ($query) => $query->where('semester', $reportCard->semester),
        )
        ->orderBy('id_mapel')
        ->get();

    return view('reports.e-report-print', [
        'reportCard' => $reportCard,
        'grades' => $grades,
    ]);
})->middleware('auth')->name('report-cards.print');
