<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run()
    {
        // Données des éléments du pied de page
        $footers = [
            ['label' => 'Accueil', 'order' => 1, 'link' => '/'],
            ['label' => 'Terms', 'order' => 3, 'link' => '/pages/terms'],
            ['label' => 'Policy', 'order' => 4, 'link' => '/pages/privacy-policy'],
            ['label' => 'Contact', 'order' => 5, 'link' => '/contact'],
        ];

        // Insérer les données dans la table footers
        DB::table('footers')->insert($footers);
    }
    }

