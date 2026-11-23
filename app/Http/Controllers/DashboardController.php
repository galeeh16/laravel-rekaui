<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Letter statuses shown as filters, keyed by status.
     *
     * @var array<string, array{label: string, variant: string}>
     */
    private const STATUSES = [
        'overdue' => ['label' => 'Terlambat', 'variant' => 'danger'],
        'awaiting' => ['label' => 'Menunggu', 'variant' => 'info'],
        'draft' => ['label' => 'Draf', 'variant' => 'neutral'],
        'sent' => ['label' => 'Terkirim', 'variant' => 'success'],
    ];

    /**
     * Allowed "rows per page" choices.
     *
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [5, 10, 25];

    private const DEFAULT_PER_PAGE = 10;

    /**
     * Show the dashboard with outgoing letters, filtered by status and search term.
     */
    public function __invoke(Request $request): Response
    {
        $letters = $this->sampleLetters();

        $activeStatus = $request->string('status')->toString();
        $activeStatus = array_key_exists($activeStatus, self::STATUSES) ? $activeStatus : null;
        $search = $request->string('q')->trim()->toString();

        $perPage = $request->integer('per_page', self::DEFAULT_PER_PAGE);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : self::DEFAULT_PER_PAGE;

        $filteredLetters = $letters
            ->when($activeStatus, fn (Collection $letters) => $letters->where('status', $activeStatus))
            ->when($search !== '', fn (Collection $letters) => $letters->filter(
                fn (array $letter): bool => Str::contains(
                    implode(' ', [$letter['number'], $letter['subject'], $letter['recipient'], $letter['contact']]),
                    $search,
                    ignoreCase: true,
                )
            ))
            ->values();

        return Inertia::render('Dashboard', [
            'letters' => $this->paginate($filteredLetters, $perPage),
            'statuses' => self::STATUSES,
            'statusCounts' => $letters->countBy('status'),
            'totalLetters' => $letters->count(),
            'overdueRecipients' => $letters->where('status', 'overdue')->pluck('recipient')->unique()->join(', '),
            'activeStatus' => $activeStatus,
            'search' => $search,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'queryWithoutStatus' => array_filter([
                'q' => $search,
                'per_page' => $perPage !== self::DEFAULT_PER_PAGE ? $perPage : null,
            ]),
        ]);
    }

    /**
     * Paginate an in-memory collection, keeping the current query string on page links.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    private function paginate(Collection $items, int $perPage): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        return (new LengthAwarePaginator(
            $items->forPage($currentPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()],
        ))->withQueryString();
    }

    /**
     * Sample outgoing letters (newest first), shown until letters are stored in the database.
     *
     * @return Collection<int, array{number: string, subject: string, date: string, recipient: string, contact: string, due: string, is_late: bool, status: string}>
     */
    private function sampleLetters(): Collection
    {
        return collect([
            ['number' => '015/SK/XI/2026', 'subject' => 'Pengajuan anggaran kegiatan', 'date' => '20 Nov 2026', 'recipient' => 'Koperasi Maju Bersama', 'contact' => 'Sri Wahyuni', 'due' => 'Tenggat 30 hari', 'is_late' => false, 'status' => 'draft'],
            ['number' => '014/SK/XI/2026', 'subject' => 'Undangan rapat anggota tahunan', 'date' => '19 Nov 2026', 'recipient' => 'PT Sinar Mandiri', 'contact' => 'Rina Kartika', 'due' => 'Tenggat 14 hari', 'is_late' => false, 'status' => 'draft'],
            ['number' => '013/SK/XI/2026', 'subject' => 'Penawaran kerja sama', 'date' => '18 Nov 2026', 'recipient' => 'CV Arunika Desain', 'contact' => 'Bagus Pratama', 'due' => 'Tenggat 21 hari', 'is_late' => false, 'status' => 'draft'],
            ['number' => '012/SK/XI/2026', 'subject' => 'Pemberitahuan perubahan alamat', 'date' => '6 Nov 2026', 'recipient' => 'PT Sinar Mandiri', 'contact' => 'Rina Kartika', 'due' => 'Jatuh tempo besok', 'is_late' => false, 'status' => 'awaiting'],
            ['number' => '011/SK/XI/2026', 'subject' => 'Permintaan tanda tangan kontrak', 'date' => '4 Nov 2026', 'recipient' => 'Yayasan Pelita Harapan', 'contact' => 'Anisa Rahma', 'due' => 'Tenggat 5 hari', 'is_late' => false, 'status' => 'awaiting'],
            ['number' => '010/SK/XI/2026', 'subject' => 'Laporan kegiatan bulanan', 'date' => '2 Nov 2026', 'recipient' => 'Bank Nusa Sejahtera', 'contact' => 'Fajar Nugroho', 'due' => 'Dibalas 10 Nov', 'is_late' => false, 'status' => 'sent'],
            ['number' => '009/SK/X/2026', 'subject' => 'Ucapan terima kasih kunjungan', 'date' => '28 Okt 2026', 'recipient' => 'CV Arunika Desain', 'contact' => 'Bagus Pratama', 'due' => 'Dibalas 30 Okt', 'is_late' => false, 'status' => 'sent'],
            ['number' => '008/SK/X/2026', 'subject' => 'Undangan sosialisasi program', 'date' => '24 Okt 2026', 'recipient' => 'Kelurahan Sukamaju', 'contact' => 'Siti Aminah', 'due' => 'Tenggat 3 hari', 'is_late' => false, 'status' => 'awaiting'],
            ['number' => '007/SK/X/2026', 'subject' => 'Pengiriman dokumen legalitas', 'date' => '22 Okt 2026', 'recipient' => 'Notaris Budi Santoso', 'contact' => 'Budi Santoso', 'due' => 'Tenggat 7 hari', 'is_late' => false, 'status' => 'awaiting'],
            ['number' => '006/SK/X/2026', 'subject' => 'Permintaan klarifikasi tagihan', 'date' => '20 Okt 2026', 'recipient' => 'PT Teknologi Andalan', 'contact' => 'Yusuf Hidayat', 'due' => '3 hari terlambat', 'is_late' => true, 'status' => 'overdue'],
            ['number' => '005/SK/X/2026', 'subject' => 'Perpanjangan kontrak sewa', 'date' => '15 Okt 2026', 'recipient' => 'PT Graha Properti', 'contact' => 'Maya Anggraini', 'due' => 'Dibalas 21 Okt', 'is_late' => false, 'status' => 'sent'],
            ['number' => '004/SK/X/2026', 'subject' => 'Konfirmasi jadwal audit', 'date' => '14 Okt 2026', 'recipient' => 'KAP Saraswati & Rekan', 'contact' => 'Dewi Lestari', 'due' => '6 hari terlambat', 'is_late' => true, 'status' => 'overdue'],
            ['number' => '003/SK/X/2026', 'subject' => 'Laporan pertanggungjawaban', 'date' => '10 Okt 2026', 'recipient' => 'Dinas Koperasi Kota', 'contact' => 'Hendra Wijaya', 'due' => 'Dibalas 17 Okt', 'is_late' => false, 'status' => 'sent'],
            ['number' => '002/SK/X/2026', 'subject' => 'Permohonan data laporan tahunan', 'date' => '9 Okt 2026', 'recipient' => 'Dinas Koperasi Kota', 'contact' => 'Hendra Wijaya', 'due' => '11 hari terlambat', 'is_late' => true, 'status' => 'overdue'],
            ['number' => '001/SK/IX/2026', 'subject' => 'Pemberitahuan libur bersama', 'date' => '30 Sep 2026', 'recipient' => 'Bank Nusa Sejahtera', 'contact' => 'Fajar Nugroho', 'due' => 'Dibalas 2 Okt', 'is_late' => false, 'status' => 'sent'],
        ]);
    }
}
