<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Reports\StoreDamageReportRequest;
use App\Actions\Reports\CreateDamageReportAction;

$passed = 0;
$failed = 0;

$it = function (string $description, callable $test) use (&$passed, &$failed) {
    echo "Testing: {$description} ... ";
    try {
        $result = $test();
        if ($result !== false) {
            echo "\033[32mPASSED\033[0m\n";
            $passed++;
        } else {
            echo "\033[31mFAILED (returned false)\033[0m\n";
            $failed++;
        }
    } catch (\Throwable $e) {
        echo "\033[31mFAILED with exception: " . $e->getMessage() . "\033[0m\n";
        echo "  In: " . $e->getFile() . ":" . $e->getLine() . "\n";
        $failed++;
    }
};

echo "\n======================================================\n";
echo " EDUINFRA TRACKER - FASE 2 VERIFIKASI PBI-04 (SCRUM-38)\n";
echo "======================================================\n\n";

// 1. Routes Verification
$it('Route reports.index, reports.create, dan reports.store terdaftar', function () {
    $routes = collect(Route::getRoutes()->getRoutesByName());
    return $routes->has('reports.index') &&
           $routes->has('reports.create') &&
           $routes->has('reports.store');
});

// 2. Database Pre-condition
$school = School::active()->first();
$category = FacilityCategory::where('is_enabled', true)->first();

$it('Master Data Domain 1 (School & FacilityCategory) tersedia untuk form', function () use ($school, $category) {
    return $school !== null && $category !== null;
});

// 3. Controller create() view
$it('DamageReportController@create mengembalikan view dengan data schools & categories', function () {
    $controller = app(\App\Http\Controllers\DamageReportController::class);
    $view = $controller->create();
    return $view->getName() === 'reports.create' &&
           isset($view->getData()['schools']) &&
           isset($view->getData()['categories']);
});

// 4. FormRequest Validation (Negative Test: Empty Payload)
$it('StoreDamageReportRequest menolak payload kosong dengan 5 pesan error spesifik', function () {
    $request = new StoreDamageReportRequest();
    $validator = Validator::make([], $request->rules(), $request->messages());
    
    $fails = $validator->fails();
    $errors = $validator->errors();
    
    return $fails &&
           $errors->has('school_id') &&
           $errors->has('category_id') &&
           $errors->has('title') &&
           $errors->has('description') &&
           $errors->has('severity_level');
});

// 5. FormRequest Validation (Negative Test: Description < 10 chars)
$it('StoreDamageReportRequest menolak deskripsi kurang dari 10 karakter', function () use ($school, $category) {
    $data = [
        'school_id' => $school->id,
        'category_id' => $category->id,
        'title' => 'Judul Valid',
        'description' => 'Pendek',
        'severity_level' => 3,
    ];
    $request = new StoreDamageReportRequest();
    $validator = Validator::make($data, $request->rules(), $request->messages());
    return $validator->fails() && $validator->errors()->has('description');
});

// 6. FormRequest Validation (Negative Test: Invalid severity level > 5)
$it('StoreDamageReportRequest menolak severity_level di luar skala 1-5', function () use ($school, $category) {
    $data = [
        'school_id' => $school->id,
        'category_id' => $category->id,
        'title' => 'Judul Valid',
        'description' => 'Deskripsi valid lebih dari 10 karakter panjangnya.',
        'severity_level' => 99,
    ];
    $request = new StoreDamageReportRequest();
    $validator = Validator::make($data, $request->rules(), $request->messages());
    return $validator->fails() && $validator->errors()->has('severity_level');
});

// 7. FormRequest Validation (Negative Test: Non-existent school_id)
$it('StoreDamageReportRequest menolak school_id yang tidak terdaftar di database', function () use ($category) {
    $data = [
        'school_id' => 99999999,
        'category_id' => $category->id,
        'title' => 'Judul Valid',
        'description' => 'Deskripsi valid lebih dari 10 karakter panjangnya.',
        'severity_level' => 3,
    ];
    $request = new StoreDamageReportRequest();
    $validator = Validator::make($data, $request->rules(), $request->messages());
    return $validator->fails() && $validator->errors()->has('school_id');
});

// 8. Positive Test: CreateDamageReportAction Execution
$createdReport = null;
$it('CreateDamageReportAction berhasil menyimpan laporan dengan format nomor tiket RPT-YYYY-XXXX', function () use ($school, $category, &$createdReport) {
    $action = app(CreateDamageReportAction::class);
    $data = [
        'school_id' => $school->id,
        'category_id' => $category->id,
        'title' => 'Pengujian Otomatis Fase 2 PBI-04',
        'description' => 'Kerusakan atap laboratorium komputer bocor parah pasca angin ribut dan cuaca ekstrem.',
        'severity_level' => 5,
    ];

    $createdReport = $action->execute($data);

    return $createdReport instanceof DamageReport &&
           preg_match('/^RPT-\d{4}-\w+$/', $createdReport->ticket_num) === 1 &&
           $createdReport->status === 'pending' &&
           $createdReport->severity_level === 5 &&
           $createdReport->school_id === $school->id &&
           $createdReport->category_id === $category->id;
});

// 9. Controller index() with search and KPI
$it('DamageReportController@index menampilkan data laporan yang baru dibuat beserta KPI', function () use ($createdReport) {
    $controller = app(\App\Http\Controllers\DamageReportController::class);
    $request = Request::create('/reports', 'GET', ['q' => 'Pengujian Otomatis Fase 2']);
    $view = $controller->index($request);

    $reports = $view->getData()['reports'];
    $kpi = $view->getData()['kpi'];

    return $reports->total() >= 1 &&
           $kpi['total'] >= 1 &&
           $kpi['pending'] >= 1 &&
           $kpi['urgent'] >= 1;
});

// 10. Severity Label & Status Label Accessor on created record
$it('Accessor severity_label dan status_label berfungsi akurat pada model', function () use ($createdReport) {
    return $createdReport->severity_label === 'Level 5 – Kritis' &&
           $createdReport->status_label === 'Pending Visitasi Lapangan';
});

// Cleanup test record
if ($createdReport && $createdReport->exists) {
    $createdReport->delete();
    echo "\nTest cleanup: Laporan uji coba ({$createdReport->ticket_num}) berhasil dibersihkan dari database.\n";
}

echo "\n------------------------------------------------------\n";
echo "HASIL AKHIR: {$passed} BERHASIL | {$failed} GAGAL\n";
echo "------------------------------------------------------\n\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
