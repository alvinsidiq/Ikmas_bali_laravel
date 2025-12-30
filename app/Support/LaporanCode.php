<?php
namespace App\Support;

use App\Models\Laporan;

class LaporanCode
{
    public static function generate(): string
    {
        $prefix = 'LPR-'.now()->format('Ym').'-';
        $last = Laporan::where('kode','like',$prefix.'%')->latest('id')->value('kode');
        $num = 1;
        if ($last && preg_match('/-(\d{4})$/',$last,$m)) $num = intval($m[1]) + 1;
        return $prefix.str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}

