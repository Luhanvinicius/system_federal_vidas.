<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Clínica Federal Vidas Parangaba',
                'cep' => '60440-145',
                'address' => 'Av. Paranjana, 1000 - Parangaba',
                'city' => 'Fortaleza',
                'state' => 'CE',
                'latitude' => -3.7745,
                'longitude' => -38.5655,
                'phone' => '(85) 3333-1001',
                'active' => true,
            ],
            [
                'name' => 'Clínica Saúde Montese',
                'cep' => '60410-425',
                'address' => 'Rua dos Médicos, 250 - Montese',
                'city' => 'Fortaleza',
                'state' => 'CE',
                'latitude' => -3.7670,
                'longitude' => -38.5430,
                'phone' => '(85) 3333-1002',
                'active' => true,
            ],
            [
                'name' => 'Clínica Bem Cuidar Antônio Bezerra',
                'cep' => '60360-001',
                'address' => 'Av. Mister Hull, 500 - Antônio Bezerra',
                'city' => 'Fortaleza',
                'state' => 'CE',
                'latitude' => -3.7415,
                'longitude' => -38.5740,
                'phone' => '(85) 3333-1003',
                'active' => true,
            ],
        ];

        foreach ($items as $it) {
            Clinic::updateOrCreate(['name' => $it['name']], $it);
        }
    }
}
