<?php

namespace App\Services;

class QrisService
{
    /**
     * CRC16-CCITT-FALSE
     * QRIS Official Standard
     */
    public static function crc16Qris(string $input): string
    {
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($input); $i++) {

            $crc ^= (ord($input[$i]) << 8);

            for ($j = 0; $j < 8; $j++) {

                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }


    /**
     * Convert QRIS Static → QRIS Dynamic
     * dengan menambahkan nominal
     */
    public static function makeDynamicQR(
        string $qris,
        $nominal
    ): string {

        // -----------------------------------------
        // 1. Cari TAG 63 (CRC lama)
        // -----------------------------------------
        $crcTagPos = strrpos($qris, '6304');

        if ($crcTagPos === false) {
            throw new \Exception("Tag CRC '6304' tidak ditemukan");
        }

        // Hapus CRC lama
        $base = substr($qris, 0, $crcTagPos);


        // -----------------------------------------
        // 2. Static → Dynamic
        // 010211 → 010212
        // -----------------------------------------
        if (str_starts_with($base, '000201010211')) {

            $base =
                '000201010212' .
                substr($base, strlen('000201010211'));
        }


        // -----------------------------------------
        // 3. Buat TAG 54 (Nominal)
        // -----------------------------------------
        $nominalStr = (string) $nominal;

        $tag54 =
            '54' .
            str_pad(strlen($nominalStr), 2, '0', STR_PAD_LEFT) .
            $nominalStr;


        // -----------------------------------------
        // 4. Hapus TAG 54 lama jika ada
        // -----------------------------------------
        $base = preg_replace(
            '/54\d{2}\d+/',
            '',
            $base
        );


        // -----------------------------------------
        // 5. Sisipkan TAG 54 sebelum TAG 58
        // -----------------------------------------
        $idx58 = strpos($base, '5802');

        if ($idx58 === false) {
            throw new \Exception(
                "Tag 58 (Country Code) tidak ditemukan"
            );
        }

        $base =
            substr($base, 0, $idx58) .
            $tag54 .
            substr($base, $idx58);


        // -----------------------------------------
        // 6. Tambahkan placeholder CRC
        // -----------------------------------------
        $build = $base . '6304';


        // -----------------------------------------
        // 7. Hitung CRC final
        // -----------------------------------------
        $crc = self::crc16Qris($build);


        return $build . $crc;
    }
}