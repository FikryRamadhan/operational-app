<?php

namespace App\MyClass;

use Carbon\Carbon;

class MyClass
{

    public static function konversiBulanKeRomawi($bulan)
    {
        switch ($bulan) {
            case '01':
                $angkaRomawi = 'I';
                break;
            case '02':
                $angkaRomawi = 'II';
                break;
            case '03':
                $angkaRomawi = 'III';
                break;
            case '04':
                $angkaRomawi = 'IV';
                break;
            case '05':
                $angkaRomawi = 'V';
                break;
            case '06':
                $angkaRomawi = 'VI';
                break;
            case '07':
                $angkaRomawi = 'VII';
                break;
            case '08':
                $angkaRomawi = 'VIII';
                break;
            case '09':
                $angkaRomawi = 'IX';
                break;
            case '10':
                $angkaRomawi = 'X';
                break;
            case '11':
                $angkaRomawi = 'XI';
                break;
            case '12':
                $angkaRomawi = 'XII';
                break;
        }
        return $angkaRomawi;
    }

    public static function formatTanggal($tanggal)
    {
        return Carbon::parse($tanggal)->format('d');
    }

    public static function formatBulan($bulan)
    {
        return Carbon::parse($bulan)->format('m');
    }

    public static function formatTahun($tahun)
    {
        return Carbon::parse($tahun)->format('Y');
    }

    // Format NO Transaksi
    public static function formatNoTransaksi(array $data)
    {
        $tanggal = $data["tanggal"];
        $tahun = self::formatTahun($tanggal);
        $bulan = self::formatBulan($tanggal);
        $konversi  = self::konversiBulanKeRomawi($bulan);

        $kodeBarang = $data["kodeBarang"];
        $perusahaan = $data["perusahaan"];
        $urutan = $data["urutan"];

        return $urutan."/".$kodeBarang."/".$perusahaan."/".$konversi."/".$tahun;
    }
}
