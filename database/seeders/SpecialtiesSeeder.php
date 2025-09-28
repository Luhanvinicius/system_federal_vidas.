<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtiesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Angiologista'            => 30.00,
            'Cardiologista'           => 30.00,
            'Clínico Geral'           => 30.00,
            'Dermatologista'          => 30.00,
            'Endocrinologista'        => 30.00,
            'Gastroenterologista'     => 30.00,
            'Ginecologista'           => 30.00,
            'Mastologista'            => 30.00,
            'Oftalmologista'          => 30.00,
            'Ortopedista'             => 30.00,
            'Otorinolaringologista'   => 30.00,
            'Neurologista'            => 30.00,
            'Nutrólogo'               => 30.00,
            'Pediatra'                => 30.00,
            'Nefrologista'            => 30.00,
            'Urologista'              => 30.00,
            'Psiquiatra'              => 30.00,
            'Pneumologista'           => 30.00,
            'Reumatologista'          => 30.00,
            'Nutricionista online'    => 40.00,
            'Psicólogo online'        => 40.00,
            'Medicina Familiar'       => 30.00,
            'Medicina do trabalho'    => 30.00,
        ];

        foreach ($data as $name => $price) {
            Specialty::updateOrCreate(['name' => $name], ['price' => $price]);
        }
    }
}
