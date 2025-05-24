<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Stage;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'code' => 'GK',
                'name' => 'Goalkeeper (Penjaga Gawang)',
            ],
            [
                'code' => 'CB',
                'name' => 'Centre Back (Bek tengah)',
            ],
            [
                'code' => 'LB',
                'name' => 'Left Back (Bek kiri)',
            ],
            [
                'code' => 'RB',
                'name' => 'Right Back (Bek kanan)',
            ],
            [
                'code' => 'LWB',
                'name' => 'Left Wing Back (Bek sayap kiri)',
            ],
            [
                'code' => 'RWB',
                'name' => 'Right Wing Back (Bek sayap kanan)',
            ],
            [
                'code' => 'SW',
                'name' => 'Sweeper',
            ],
            [
                'code' => 'CDM',
                'name' => 'Central Defensive Midfielder (Gelandang bertahan)',
            ],
            [
                'code' => 'CM',
                'name' => 'Central Midfielder (Gelandang tengah)',
            ],
            [
                'code' => 'CAM',
                'name' => 'Central Attacking Midfielder (Gelandang serang)',
            ],
            [
                'code' => 'LM',
                'name' => 'Left Midfielder (Gelandang kiri)',
            ],
            [
                'code' => 'RM',
                'name' => 'Right Midfielder (Gelandang kanan)',
            ],
            [
                'code' => 'DM',
                'name' => 'Defensive Midfielder (Gelandang bertahan)',
            ],
            [
                'code' => 'AM',
                'name' => 'Attacking Midfielder (Gelandang serang)',
            ],
            [
                'code' => 'CF',
                'name' => 'Centre Forward (Penyerang tengah)',
            ],
            [
                'code' => 'ST',
                'name' => 'Striker (Penyerang utama)',
            ],
            [
                'code' => 'LW',
                'name' => 'Left Winger (Penyerang sayap kiri)',
            ],
            [
                'code' => 'RW',
                'name' => 'Right Winger (Penyerang sayap kanan)',
            ],
            [
                'code' => 'SS',
                'name' => 'Second Striker (Penyerang bayangan)',
            ],
        ];
        foreach ($positions as $key => $value) {
            Position::create($value);
        }

        $teams = [
            [
                'code' => 'U-13',
                'name' => 'Under 13',
                'description' => 'Tim untuk pemain usia di bawah 13 tahun, sebagai bagian awal akademi sepak bola.',
            ],
            [
                'code' => 'U-15',
                'name' => 'Under 15',
                'description' => 'Tim untuk pemain usia di bawah 15 tahun, sering kali menjadi jenjang pembinaan usia dini.',
            ],
            [
                'code' => 'U-16',
                'name' => 'Under 16',
                'description' => 'Tim untuk pemain usia di bawah 16 tahun, digunakan dalam pengembangan awal talenta muda.',
            ],
            [
                'code' => 'U-17',
                'name' => 'Under 17',
                'description' => 'Tim untuk pemain usia di bawah 17 tahun, seperti Piala Dunia U-17',
            ],
            [
                'code' => 'U-19',
                'name' => 'Under 19',
                'description' => 'Tim untuk pemain usia di bawah 19 tahun, digunakan dalam turnamen usia muda tingkat nasional dan internasional.',
            ],
            [
                'code' => 'U-20',
                'name' => 'Under 20',
                'description' => 'Tim untuk pemain usia di bawah 20 tahun, seperti Piala Dunia U-20.',
            ],
            [
                'code' => 'U-21',
                'name' => 'Under 21',
                'description' => 'Tim untuk pemain usia di bawah 21 tahun. Umumnya digunakan untuk kompetisi pemuda tingkat nasional atau internasional.',
            ],
            [
                'code' => 'U-23',
                'name' => 'Under 23',
                'description' => 'Tim untuk pemain usia di bawah 23 tahun. Biasanya digunakan untuk turnamen seperti SEA Games atau Olimpiade.',
            ],
        ];
        foreach ($teams as $key => $value) {
            Team::create($value);
        }

        $stages = [
            [
                'code' => 'FISIK',
                'name' => 'Fisik',
                'percentage' => 25,
                'order' => 1,
            ],
            [
                'code' => 'TEKNIK',
                'name' => 'Teknik',
                'percentage' => 25,
                'order' => 2,
            ],
            [
                'code' => 'PSIKOLOGI',
                'name' => 'Psikologi',
                'percentage' => 25,
                'order' => 3,
            ],
            [
                'code' => 'EVALUASI',
                'name' => 'Evaluasi',
                'percentage' => 25,
                'order' => 4,
            ],
        ];
        foreach ($stages as $key => $value) {
            Stage::create($value);
        }
    }
}
