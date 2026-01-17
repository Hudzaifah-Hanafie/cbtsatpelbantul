<?php

namespace App\Exports;

use App\Models\UserTest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class ResultsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = UserTest::query()->with(['user', 'test'])
            ->whereNotNull('completed_at');

        if ($this->startDate) {
            $query->whereDate('completed_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('completed_at', '<=', $this->endDate);
        }

        return $query->latest('completed_at');
    }

    public function map($userTest): array
    {
        return [
            $userTest->user->name,
            $userTest->user->email,
            $userTest->test->title,
            $userTest->score,
            $userTest->completed_at ? $userTest->completed_at->format('d-m-Y H:i:s') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Peserta',
            'Email',
            'Judul Ujian',
            'Skor',
            'Waktu Selesai',
        ];
    }
}