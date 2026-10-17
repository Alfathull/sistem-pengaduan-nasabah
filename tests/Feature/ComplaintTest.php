<?php

use App\Enums\AccountType;
use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\User;
use App\Notifications\ComplaintStatusUpdated;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    foreach (UserRole::values() as $role) {
        Role::findOrCreate($role, 'web');
    }

    $this->category = Category::create(['name' => 'Mobile Banking', 'is_active' => true]);
    $this->department = Department::create(['name' => 'E-Channel', 'is_active' => true]);
});

function makeUser(UserRole $role): User
{
    $user = User::factory()->create([
        'account_type' => $role === UserRole::Nasabah ? AccountType::Tabungan->value : null,
        'phone' => '081200000000',
    ]);
    $user->assignRole($role->value);

    return $user;
}

it('nasabah dapat membuat pengaduan dan mendapat nomor tiket', function () {
    $nasabah = makeUser(UserRole::Nasabah);

    $component = Livewire\Livewire::actingAs($nasabah)
        ->test('pages::customer.complaints.create')
        ->set('account_type', AccountType::Tabungan->value)
        ->set('category_id', $this->category->id)
        ->set('subject', 'Mobile banking error')
        ->set('description', 'Aplikasi tidak bisa dibuka sejak kemarin.')
        ->call('store');

    $component->assertHasNoErrors();

    $complaint = Complaint::first();

    expect($complaint)->not->toBeNull()
        ->and($complaint->ticket_number)->toMatch('/^PGD-\d{8}-\d{4}$/')
        ->and($complaint->status)->toBe(ComplaintStatus::Baru)
        ->and($complaint->user_id)->toBe($nasabah->id);

    expect($complaint->histories()->count())->toBe(1);
});

it('nasabah tidak bisa melihat pengaduan milik nasabah lain', function () {
    $nasabahA = makeUser(UserRole::Nasabah);
    $nasabahB = makeUser(UserRole::Nasabah);

    $complaint = Complaint::factory()->create([
        'user_id' => $nasabahB->id,
        'category_id' => $this->category->id,
    ]);

    actingAs($nasabahA)
        ->get(route('customer.complaints.show', $complaint))
        ->assertForbidden();
});

it('nasabah bisa melihat pengaduan miliknya sendiri', function () {
    $nasabah = makeUser(UserRole::Nasabah);

    $complaint = Complaint::factory()->create([
        'user_id' => $nasabah->id,
        'category_id' => $this->category->id,
    ]);

    actingAs($nasabah)
        ->get(route('customer.complaints.show', $complaint))
        ->assertOk()
        ->assertSee($complaint->ticket_number);
});

it('cs dapat mengubah status dan eskalasi tercatat di riwayat', function () {
    Notification::fake();

    $cs = makeUser(UserRole::Cs);
    $nasabah = makeUser(UserRole::Nasabah);

    $complaint = Complaint::factory()->create([
        'user_id' => $nasabah->id,
        'category_id' => $this->category->id,
        'status' => ComplaintStatus::Diproses->value,
    ]);

    Livewire\Livewire::actingAs($cs)
        ->test('pages::staff.complaints.show', ['complaint' => $complaint])
        ->set('status', ComplaintStatus::Dieskalasi->value)
        ->set('department_id', $this->department->id)
        ->set('follow_up', 'Dieskalasi ke E-Channel untuk penanganan.')
        ->set('notify', true)
        ->call('save')
        ->assertHasNoErrors();

    $complaint->refresh();

    expect($complaint->status)->toBe(ComplaintStatus::Dieskalasi)
        ->and($complaint->department_id)->toBe($this->department->id);

    $history = $complaint->histories()->where('status', ComplaintStatus::Dieskalasi->value)->first();

    expect($history)->not->toBeNull()
        ->and($history->department_id)->toBe($this->department->id)
        ->and($history->follow_up)->toContain('Dieskalasi');

    Notification::assertSentTo($nasabah, ComplaintStatusUpdated::class);
});

it('cs menolak tiket wajib menyertakan alasan', function () {
    $cs = makeUser(UserRole::Cs);
    $nasabah = makeUser(UserRole::Nasabah);

    $complaint = Complaint::factory()->create([
        'user_id' => $nasabah->id,
        'category_id' => $this->category->id,
        'status' => ComplaintStatus::Baru->value,
    ]);

    Livewire\Livewire::actingAs($cs)
        ->test('pages::staff.complaints.show', ['complaint' => $complaint])
        ->set('status', ComplaintStatus::Ditolak->value)
        ->set('rejection_reason', '')
        ->call('save')
        ->assertHasErrors('rejection_reason');
});

it('pimpinan tidak bisa mengubah tiket', function () {
    $pimpinan = makeUser(UserRole::Pimpinan);
    $nasabah = makeUser(UserRole::Nasabah);

    $complaint = Complaint::factory()->create([
        'user_id' => $nasabah->id,
        'category_id' => $this->category->id,
    ]);

    // Pimpinan tidak memiliki akses ke halaman pengelolaan CS.
    actingAs($pimpinan)
        ->get(route('staff.complaints.show', $complaint))
        ->assertForbidden();

    expect($pimpinan->can('manage', $complaint))->toBeFalse();
});

it('nasabah tidak bisa mengakses area cs', function () {
    $nasabah = makeUser(UserRole::Nasabah);

    actingAs($nasabah)
        ->get(route('staff.complaints.index'))
        ->assertForbidden();
});

it('export laporan pdf berhasil untuk pimpinan', function () {
    $pimpinan = makeUser(UserRole::Pimpinan);
    $nasabah = makeUser(UserRole::Nasabah);

    Complaint::factory()->count(3)->create([
        'user_id' => $nasabah->id,
        'category_id' => $this->category->id,
    ]);

    $response = actingAs($pimpinan)->get(route('manager.report.pdf'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
