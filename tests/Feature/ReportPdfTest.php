<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintReportService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed([RolePermissionSeeder::class, MasterDataSeeder::class]);

    // Simulasikan kondisi produksi: remote asset dimatikan (dompdf tidak boleh ambil Google Fonts/CDN).
    config(['dompdf.options.enable_remote' => false]);
});

function pimpinanUser(): User
{
    $user = User::factory()->create(['phone' => '0812']);
    $user->assignRole(UserRole::Pimpinan->value);

    return $user;
}

it('export PDF berhasil tanpa aset remote dan menghasilkan file PDF valid', function () {
    $pimpinan = pimpinanUser();
    $category = Category::first();

    Complaint::factory()->count(5)->create(['category_id' => $category->id]);

    $response = actingAs($pimpinan)->get(route('manager.report.pdf'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');

    // Pastikan payload benar-benar PDF (magic bytes %PDF-).
    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');
});

it('membuat direktori font cache dompdf bila belum ada', function () {
    $fontDir = config('dompdf.options.font_dir');

    if ($fontDir && File::isDirectory($fontDir)) {
        File::deleteDirectory($fontDir);
    }

    $pimpinan = pimpinanUser();
    Complaint::factory()->count(2)->create(['category_id' => Category::first()->id]);

    actingAs($pimpinan)->get(route('manager.report.pdf'))->assertOk();

    expect(File::isDirectory($fontDir))->toBeTrue();
});

it('view PDF tidak memuat aset remote (http/https/cdn)', function () {
    $html = view('pdf.report', [
        'report' => app(ComplaintReportService::class)->summary(null, null),
        'generatedAt' => now(),
        'logo' => 'data:image/png;base64,AAAA',
    ])->render();

    expect($html)
        ->not->toContain('http://')
        ->not->toContain('https://')
        ->not->toContain('fonts.googleapis')
        ->not->toContain('cdn.');
});
