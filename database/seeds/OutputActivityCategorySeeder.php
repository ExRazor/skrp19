<?php

use Illuminate\Database\Seeder;

class OutputActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategori = [
            'HKI A',
            'HKI B',
            'Teknologi Tepat Guna & Produk',
            'Buku',
        ];

        $deskripsi = [
            'a) Paten,&#10;b) Paten Sederhana',
            'a) Hak Cipta, &#10;b) Desain Produk Industri, &#10;c) Perlindungan Varietas Tanaman (Sertifikat Perlindungan Varietas Tanaman, Sertifikat Pelepasan Varietas, Sertifikat Pendaftaran Varietas), &#10;d) Desain Tata Letak Sirkuit Terpadu, &#10;e) dll.',
            'Termasuk di antaranya produk terstandarisasi, produk tersertifikasi, karya seni, serta rekayasa sosial',
            'Buku ber-ISBN, Book Chapter'
        ];

        foreach($kategori as $i => $value){
            DB::table('output_activity_categories')->insert([
                'nama'          => $value,
                'deskripsi'     => $deskripsi[$i],
                'created_at'    => now()
            ]);
        }
    }
}
