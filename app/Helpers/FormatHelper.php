<?php
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        if (!is_numeric($angka)) {
            return $angka;
        }
        
        return  number_format($angka, 0, ',', '.');
    }
}