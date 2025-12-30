<?php
namespace App\Support;

use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;

class IuranCode
{
    public static function inv(): string
    {
        $prefix = 'INV-'.now()->format('Ym').'-';
        $last = IuranTagihan::where('kode','like',$prefix.'%')->latest('id')->value('kode');
        $num = 1; if ($last && preg_match('/-(\d{4})$/',$last,$m)) $num = (int)$m[1] + 1;
        return $prefix.str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    public static function pay(): string
    {
        $prefix = 'PAY-'.now()->format('Ym').'-';
        $last = IuranPembayaran::where('kode','like',$prefix.'%')->latest('id')->value('kode');
        $num = 1; if ($last && preg_match('/-(\d{4})$/',$last,$m)) $num = (int)$m[1] + 1;
        return $prefix.str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}

