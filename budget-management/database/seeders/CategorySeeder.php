<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['Logement', 'Loyer, hypothèque, électricité, eau, etc.', 'home', 30.00],
            ['Alimentation', 'Courses, restaurants, cafés, etc.', 'utensils', 20.00],
            ['Transport', 'Carburant, transport en commun, entretien véhicule, etc.', 'car', 15.00],
            ['Santé', 'Assurance santé, médicaments, consultations, etc.', 'heartbeat', 10.00],
            ['Loisirs', 'Sorties, vacances, sport, divertissements, etc.', 'gamepad', 10.00],
            ['Habillement', 'Vêtements, chaussures, accessoires, etc.', 'tshirt', 5.00],
            ['Éducation', 'Frais de scolarité, livres, formation, etc.', 'graduation-cap', 5.00],
            ['Épargne', 'Investissements, épargne retraite, etc.', 'piggy-bank', 5.00],
        ];
        
        foreach ($categories as $category) {
            Category::create([
                'nom' => $category[0],
                'description' => $category[1],
                'icone' => $category[2],
                'pourcentage_defaut' => $category[3],
            ]);
        }
    }
}