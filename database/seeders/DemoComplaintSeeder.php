<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\User;
use App\Services\ComplaintService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DemoComplaintSeeder extends Seeder
{
    /**
     * Kasus pengaduan realistis berbahasa Indonesia.
     * Tiap entri: [kategori, subjek, deskripsi].
     *
     * @var array<int, array{0:string,1:string,2:string}>
     */
    private array $cases = [
        ['ATM/CDM/CRM', 'Kartu ATM tertelan di mesin', 'Kartu ATM saya tertelan saat tarik tunai di ATM Indralaya dan saldo sudah berkurang namun uang tidak keluar.'],
        ['Mobile Banking', 'Gagal login mobile banking', 'Aplikasi mobile banking selalu menampilkan pesan error saat saya mencoba login sejak kemarin sore.'],
        ['Mutasi Rekening', 'Transaksi tidak muncul di mutasi', 'Transfer masuk dari rekan kerja belum tercatat di mutasi rekening padahal sudah lebih dari 3 jam.'],
        ['Cek Saldo', 'Saldo tidak sesuai setelah transfer', 'Setelah transfer ke rekening lain, saldo berkurang dua kali lipat dari nominal yang saya kirim.'],
        ['Internet Banking', 'Token internet banking tidak terkirim', 'Saya tidak menerima kode OTP untuk otorisasi transaksi di internet banking.'],
        ['EDC', 'Transaksi EDC terpotong dua kali', 'Pembayaran di merchant menggunakan kartu debit terpotong dua kali untuk satu transaksi.'],
        ['SMS Banking', 'Notifikasi SMS banking tidak masuk', 'Notifikasi SMS banking untuk transaksi masuk dan keluar sudah tidak saya terima selama seminggu.'],
        ['BSB Lur', 'Saldo BSB Lur tidak bertambah', 'Top up saldo BSB Lur sudah berhasil didebet tetapi saldo di aplikasi tidak bertambah.'],
        ['Mobile Banking', 'Transfer antar bank pending', 'Transfer antar bank melalui mobile banking berstatus pending sejak dua hari lalu.'],
        ['ATM/CDM/CRM', 'Setoran tunai di CDM tidak masuk', 'Saya setor tunai di mesin CDM sebesar Rp2.000.000 namun saldo belum bertambah.'],
        ['Cek Saldo', 'Saldo berbeda di ATM dan mobile banking', 'Saldo yang tampil di ATM berbeda dengan yang ada di aplikasi mobile banking.'],
        ['Internet Banking', 'Lupa user ID internet banking', 'Saya lupa user ID internet banking dan tidak bisa melakukan reset secara mandiri.'],
        ['Mutasi Rekening', 'Minta cetak mutasi 6 bulan', 'Mohon bantuan cetak mutasi rekening 6 bulan terakhir untuk keperluan pengajuan kredit.'],
        ['EDC', 'Struk EDC tidak keluar', 'Transaksi di EDC berhasil tetapi struk tidak tercetak sehingga saya ragu transaksi berhasil.'],
        ['Mobile Banking', 'Aplikasi sering keluar sendiri', 'Aplikasi mobile banking sering menutup sendiri ketika membuka menu transfer.'],
        ['ATM/CDM/CRM', 'Mesin ATM selalu offline', 'ATM di dekat kampus Unsri Indralaya selalu offline sejak beberapa hari terakhir.'],
        ['SMS Banking', 'Biaya SMS banking terlalu sering', 'Saya dikenai biaya SMS banking berulang padahal tidak melakukan transaksi.'],
        ['BSB Lur', 'Gagal daftar BSB Lur', 'Saya gagal mendaftar layanan BSB Lur karena verifikasi data selalu ditolak.'],
        ['Cek Saldo', 'Tidak bisa cek saldo via ATM', 'Setiap cek saldo di ATM muncul pesan transaksi tidak dapat diproses.'],
        ['Internet Banking', 'Transaksi internet banking ditolak terus', 'Semua transaksi di internet banking saya selalu ditolak dengan kode error 57.'],
        ['Mutasi Rekening', 'Mutasi menampilkan transaksi asing', 'Ada beberapa transaksi yang tidak saya kenali muncul di mutasi rekening saya.'],
        ['Mobile Banking', 'Reset PIN mobile banking', 'Saya ingin melakukan reset PIN mobile banking karena lupa PIN transaksi.'],
        ['EDC', 'EDC menolak kartu debit', 'Kartu debit saya ditolak di beberapa EDC merchant padahal saldo mencukupi.'],
        ['ATM/CDM/CRM', 'Uang tertelan saat tarik tunai', 'Saat tarik tunai Rp1.000.000, mesin menarik kembali uang dan saldo terpotong.'],
        ['Lainnya', 'Permintaan perubahan nomor HP', 'Mohon bantuan untuk mengubah nomor HP yang terdaftar karena sudah tidak aktif.'],
        ['SMS Banking', 'Format notifikasi SMS tidak jelas', 'Format SMS notifikasi transaksi tidak menampilkan nominal secara lengkap.'],
        ['Mobile Banking', 'Fitur QRIS tidak berfungsi', 'Pembayaran QRIS melalui mobile banking gagal terus dengan pesan koneksi gagal.'],
        ['Internet Banking', 'Saldo tertahan setelah gagal transfer', 'Transfer gagal namun saldo saya tertahan dan belum dikembalikan.'],
        ['Cek Saldo', 'Saldo nol setelah pemeliharaan sistem', 'Setelah ada pemeliharaan sistem, saldo saya tampil nol di seluruh kanal.'],
        ['BSB Lur', 'Riwayat transaksi BSB Lur hilang', 'Riwayat transaksi di aplikasi BSB Lur tiba-tiba hilang semua.'],
        ['Mutasi Rekening', 'Permintaan rekening koran bermaterai', 'Saya membutuhkan rekening koran bermaterai untuk keperluan pengurusan visa.'],
        ['ATM/CDM/CRM', 'PIN ATM terblokir', 'PIN ATM saya terblokir setelah salah memasukkan PIN tiga kali.'],
        ['EDC', 'Pembayaran EDC gagal namun terdebet', 'Pembayaran di EDC gagal tetapi rekening saya tetap terdebet.'],
        ['Mobile Banking', 'Verifikasi wajah gagal terus', 'Verifikasi wajah di aplikasi mobile banking selalu gagal saat aktivasi perangkat baru.'],
        ['Lainnya', 'Keluhan layanan teller lama', 'Antrean layanan teller di cabang Indralaya sangat lama hingga lebih dari satu jam.'],
        ['Internet Banking', 'Tidak bisa menambah rekening tujuan', 'Saya tidak dapat menambahkan rekening tujuan baru di internet banking.'],
        ['SMS Banking', 'SMS banking tidak bisa dinonaktifkan', 'Saya ingin menonaktifkan SMS banking tetapi permintaan selalu gagal.'],
        ['Cek Saldo', 'Saldo mengambang beberapa jam', 'Saldo saya sempat mengambang dan tidak bisa digunakan selama beberapa jam.'],
    ];

    public function run(): void
    {
        /** @var ComplaintService $service */
        $service = app(ComplaintService::class);

        $nasabah = User::role(UserRole::Nasabah->value)->get();

        if ($nasabah->isEmpty()) {
            return;
        }

        $categories = Category::all()->keyBy('name');
        $departments = Department::all();
        $officer = User::role(UserRole::Cs->value)->first();

        $statuses = [
            ComplaintStatus::Baru,
            ComplaintStatus::Divalidasi,
            ComplaintStatus::Diproses,
            ComplaintStatus::Dieskalasi,
            ComplaintStatus::Selesai,
            ComplaintStatus::Ditolak,
        ];

        foreach ($this->cases as $index => [$categoryName, $subject, $description]) {
            $category = $categories[$categoryName] ?? $categories->first();
            $owner = $nasabah[$index % $nasabah->count()];
            $createdAt = Carbon::now()->subDays(random_int(0, 150))->subHours(random_int(0, 23));
            $targetStatus = $statuses[$index % count($statuses)];

            $complaint = Complaint::create([
                'ticket_number' => 'PGD-'.$createdAt->format('Ymd').'-'.Str::padLeft((string) ($index + 1), 4, '0'),
                'user_id' => $owner->id,
                'category_id' => $category->id,
                'reporter_name' => $owner->name,
                'reporter_email' => $owner->email,
                'reporter_phone' => $owner->phone,
                'account_type' => fake()->randomElement(AccountType::cases())->value,
                'subject' => $subject,
                'description' => $description,
                'status' => ComplaintStatus::Baru,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $service->recordHistory($complaint, $owner, ComplaintStatus::Baru, 'Pengaduan dibuat oleh nasabah.');

            $this->progressTo($service, $complaint, $officer, $targetStatus, $departments, $createdAt);
        }
    }

    /**
     * Jalankan transisi status bertahap hingga mencapai status target
     * dan catat riwayat tindak lanjut untuk tiap tahap.
     *
     * @param  Collection<int, Department>  $departments
     */
    private function progressTo(
        ComplaintService $service,
        Complaint $complaint,
        ?User $officer,
        ComplaintStatus $target,
        $departments,
        Carbon $createdAt,
    ): void {
        $priority = fake()->randomElement(ComplaintPriority::cases());
        $cursor = $createdAt->copy();

        if ($target === ComplaintStatus::Baru) {
            return;
        }

        if ($target === ComplaintStatus::Ditolak) {
            $cursor->addHours(random_int(1, 12));
            $complaint->update([
                'priority' => $priority,
                'status' => ComplaintStatus::Ditolak,
                'rejection_reason' => 'Data pengaduan tidak lengkap, nasabah diminta melengkapi bukti transaksi.',
                'updated_at' => $cursor,
            ]);
            $history = $service->recordHistory($complaint, $officer, ComplaintStatus::Ditolak, 'Tiket ditolak. Alasan: Data pengaduan tidak lengkap, nasabah diminta melengkapi bukti transaksi.');
            $history->forceFill(['created_at' => $cursor, 'updated_at' => $cursor])->save();

            return;
        }

        $followUps = [
            ComplaintStatus::Divalidasi->value => 'Pengaduan tervalidasi, prioritas ditetapkan dan akan ditindaklanjuti.',
            ComplaintStatus::Diproses->value => 'Tiket sedang diproses oleh petugas terkait.',
            ComplaintStatus::Dieskalasi->value => 'Tiket dieskalasi ke departemen terkait untuk penanganan lanjutan.',
            ComplaintStatus::Selesai->value => 'Permasalahan telah diselesaikan dan dikonfirmasi ke nasabah.',
        ];

        $flow = [
            ComplaintStatus::Divalidasi,
            ComplaintStatus::Diproses,
            ComplaintStatus::Dieskalasi,
            ComplaintStatus::Selesai,
        ];

        $complaint->update(['priority' => $priority]);

        foreach ($flow as $step) {
            $cursor->addHours(random_int(2, 36));
            $departmentId = null;

            if ($step === ComplaintStatus::Dieskalasi) {
                $departmentId = $departments->random()->id;
                $complaint->update(['department_id' => $departmentId]);
            }

            $complaint->update(['status' => $step, 'updated_at' => $cursor]);

            $history = $service->recordHistory(
                $complaint,
                $officer,
                $step,
                $followUps[$step->value],
                $departmentId,
            );
            $history->forceFill(['created_at' => $cursor, 'updated_at' => $cursor])->save();

            if ($step === $target) {
                break;
            }
        }
    }
}
