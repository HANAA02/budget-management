<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = [
            ['Logement', 'Dépenses liées au logement', 'home', 30],
            ['Alimentation', 'Courses et restaurants', 'utensils', 20],
            ['Transport', 'Voiture, transports en commun', 'car', 15],
            ['Santé', 'Frais médicaux, pharmacie', 'heartbeat', 10],
            ['Loisirs', 'Sorties, voyages, divertissements', 'gamepad', 10],
            ['Habillement', 'Vêtements et accessoires', 'tshirt', 5],
            ['Éducation', 'Frais de scolarité, livres', 'graduation-cap', 5],
            ['Épargne', 'Économies et investissements', 'piggy-bank', 5],
        ];
        
        $category = $this->faker->randomElement($categories);
        
        return [
            'nom' => $category[0],
            'description' => $category[1],
            'icone' => $category[2],
            'pourcentage_defaut' => $category[3],
        ];
    }
}