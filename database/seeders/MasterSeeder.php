<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BillingType;
use App\Models\Position;
use App\Models\Stage;
use App\Models\Group;
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

        $groups = [
            [
                'code' => 'U-13',
                'name' => 'UNDER 13',
                'age_min' => 10,
                'age_max' => 13,
                'description' => 'Tim untuk pemain usia di bawah 13 tahun, sebagai bagian awal akademi sepak bola.',
            ],
            [
                'code' => 'U-15',
                'name' => 'UNDER 15',
                'age_min' => 14,
                'age_max' => 15,
                'description' => 'Tim untuk pemain usia di bawah 15 tahun, sering kali menjadi jenjang pembinaan usia dini.',
            ],
            [
                'code' => 'U-16',
                'name' => 'UNDER 16',
                'age_min' => 15,
                'age_max' => 16,
                'description' => 'Tim untuk pemain usia di bawah 16 tahun, digunakan dalam pengembangan awal talenta muda.',
            ],
            [
                'code' => 'U-17',
                'name' => 'UNDER 17',
                'age_min' => 16,
                'age_max' => 17,
                'description' => 'Tim untuk pemain usia di bawah 17 tahun, seperti Piala Dunia U-17',
            ],
            [
                'code' => 'U-19',
                'name' => 'UNDER 19',
                'age_min' => 18,
                'age_max' => 19,
                'description' => 'Tim untuk pemain usia di bawah 19 tahun, digunakan dalam turnamen usia muda tingkat nasional dan internasional.',
            ],
            [
                'code' => 'U-20',
                'name' => 'UNDER 20',
                'age_min' => 19,
                'age_max' => 20,
                'description' => 'Tim untuk pemain usia di bawah 20 tahun, seperti Piala Dunia U-20.',
            ],
            [
                'code' => 'U-21',
                'name' => 'UNDER 21',
                'age_min' => 20,
                'age_max' => 21,
                'description' => 'Tim untuk pemain usia di bawah 21 tahun. Umumnya digunakan untuk kompetisi pemuda tingkat nasional atau internasional.',
            ],
            [
                'code' => 'U-23',
                'name' => 'UNDER 23',
                'age_min' => 21,
                'age_max' => 23,
                'description' => 'Tim untuk pemain usia di bawah 23 tahun. Biasanya digunakan untuk turnamen seperti SEA Games atau Olimpiade.',
            ],
        ];
        foreach ($groups as $key => $value) {
            Group::create($value);
        }

        $stages = [
            [
                'code' => 'TEKNIK',
                'name' => 'Teknik Dasar',
                'description' => 'Latihan kontrol bola, dribbling, passing, shooting.',
                'percentage' => 40,
                'order' => 1,
            ],
            [
                'code' => 'STRATEGY',
                'name' => 'Taktik Tim',
                'description' => 'Pemahaman formasi, strategi bertahan dan menyerang.',
                'percentage' => 30,
                'order' => 2,
            ],
            [
                'code' => 'PHYSICAL',
                'name' => 'Fisik & Mental',
                'description' => 'Latihan fisik, kecepatan, dan membangun karakter atlet.',
                'percentage' => 30,
                'order' => 3,
            ],
        ];
        foreach ($stages as $key => $value) {
            Stage::create($value);
        }

        $billing_types = [
            [
                'code' => 'REGISTRATION',
                'name' => 'Registrasi',
            ],
        ];

        foreach ($billing_types as $key => $value) {
            BillingType::create($value);
        }

        $bank_accounts = [
            [
                'bank_name' => 'Bank Central Asia',
                'account_number' => '012387654567',
                'account_holder_name' => 'Riwan Febrianto',
                'description' => null,
            ],
            [
                'bank_name' => 'Bank Negara Indonesia',
                'account_number' => '076543217654',
                'account_holder_name' => 'Riwan Febrianto',
                'description' => null,
            ],
        ];

        foreach ($bank_accounts as $key => $value) {
            BankAccount::create($value);
        }
    }
}
