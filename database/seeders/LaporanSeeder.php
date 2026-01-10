<?php
namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\LaporanComment;
use App\Models\User;
use App\Support\LaporanCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('laporans')) return;

        $reporter = User::role('anggota')->first() ?? User::first();
        $admin = User::role('admin')->first() ?? $reporter;
        if (!$reporter) return;

        Schema::disableForeignKeyConstraints();
        foreach (['laporan_comments','laporan_attachments','laporans'] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        Schema::enableForeignKeyConstraints();

        $templates = [
            ['judul' => 'Perbaikan pendingin aula', 'kategori' => 'Fasilitas', 'deskripsi' => 'Unit pendingin aula rusak dan menyebabkan kegiatan malam kurang nyaman. Mohon penjadwalan servis.'],
            ['judul' => 'Saran materi pelatihan digital', 'kategori' => 'Saran', 'deskripsi' => 'Usulan topik pelatihan pembuatan konten sosial media untuk anggota baru.'],
            ['judul' => 'Keterlambatan laporan keuangan', 'kategori' => 'Keuangan', 'deskripsi' => 'Mohon update laporan kas bulan lalu karena belum dipublikasikan di sistem.'],
            ['judul' => 'Evaluasi kegiatan bakti sosial', 'kategori' => 'Kegiatan', 'deskripsi' => 'Catatan evaluasi acara bakti sosial minggu lalu dan daftar tindak lanjut relawan.'],
            ['judul' => 'Keluhan akses wifi sekretariat', 'kategori' => 'Fasilitas', 'deskripsi' => 'Sinyal wifi sering putus ketika rapat daring, mohon pengecekan router.'],
            ['judul' => 'Saran publikasi acara', 'kategori' => 'Saran', 'deskripsi' => 'Perlu template visual agar pengumuman acara lebih konsisten di media sosial.'],
            ['judul' => 'Permintaan transparansi dana kegiatan', 'kategori' => 'Keuangan', 'deskripsi' => 'Meminta rincian pengeluaran kegiatan olahraga bulan ini untuk dipublikasikan.'],
            ['judul' => 'Kebersihan gudang peralatan', 'kategori' => 'Pengaduan', 'deskripsi' => 'Gudang penyimpanan kurang rapi dan alat banyak tidak tercatat. Mohon inventarisasi.'],
        ];

        $statuses = ['open','in_progress','resolved','rejected'];
        $commentPool = [
            'Terima kasih masukannya, akan kami tindaklanjuti pekan ini.',
            'Sudah diteruskan ke pengurus terkait.',
            'Sedang dijadwalkan dengan vendor.',
            'Mohon info tambahan jika ada foto pendukung.',
        ];

        foreach ($templates as $template) {
            $status = Arr::random($statuses);
            $commentCount = random_int(0, 2);
            $resolvedAt = $status === 'resolved' ? now()->subDays(random_int(1, 5)) : null;
            $rejectedAt = $status === 'rejected' ? now()->subDays(random_int(1, 5)) : null;
            $rejectedReason = $status === 'rejected' ? 'Belum dapat diproses pada periode ini.' : null;

            $laporan = Laporan::create([
                'kode' => LaporanCode::generate(),
                'reporter_id' => $reporter->id,
                'judul' => $template['judul'],
                'kategori' => $template['kategori'],
                'deskripsi' => $template['deskripsi'],
                'status' => $status,
                'resolved_at' => $resolvedAt,
                'rejected_at' => $rejectedAt,
                'rejected_reason' => $rejectedReason,
                'attachments_count' => 0,
                'comments_count' => 0,
            ]);

            for ($i = 0; $i < $commentCount; $i++) {
                LaporanComment::create([
                    'laporan_id' => $laporan->id,
                    'user_id' => $i % 2 === 0 ? $admin?->id : $reporter->id,
                    'body' => Arr::random($commentPool),
                    'is_internal' => false,
                ]);
            }

            if ($commentCount > 0) {
                $laporan->update(['comments_count' => $commentCount]);
            }
        }
    }
}
