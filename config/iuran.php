<?php
return [
    'bank_name' => env('IURAN_BANK_NAME', 'BCA'),
    'bank_account' => env('IURAN_BANK_ACCOUNT', '1234567890'),
    'account_name' => env('IURAN_ACCOUNT_NAME', 'IKMAST Bali'),
    'instructions' => env('IURAN_INSTRUCTIONS', "Transfer sesuai nominal ke rekening di atas, kemudian unggah bukti pembayaran pada halaman tagihan."),
];

