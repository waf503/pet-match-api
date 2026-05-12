<?php
namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'nombre' => 'Perro', 'nombre_plural' => 'Perros', 'icono' => '🐕', 'orden' => 1,
                'breeds' => [
                    ['nombre' => 'Labrador Retriever',   'popular' => true],
                    ['nombre' => 'Golden Retriever',      'popular' => true],
                    ['nombre' => 'Pastor Alemán',         'popular' => true],
                    ['nombre' => 'Bulldog Francés',       'popular' => true],
                    ['nombre' => 'Poodle',                'popular' => true],
                    ['nombre' => 'Beagle',                'popular' => true],
                    ['nombre' => 'Rottweiler',            'popular' => true],
                    ['nombre' => 'Chihuahua',             'popular' => true],
                    ['nombre' => 'Yorkshire Terrier',     'popular' => true],
                    ['nombre' => 'Husky Siberiano',       'popular' => true],
                    ['nombre' => 'Border Collie',         'popular' => true],
                    ['nombre' => 'Doberman',              'popular' => false],
                    ['nombre' => 'Shih Tzu',              'popular' => false],
                    ['nombre' => 'Maltés',                'popular' => false],
                    ['nombre' => 'Schnauzer',             'popular' => false],
                    ['nombre' => 'Dachshund',             'popular' => false],
                    ['nombre' => 'Boxer',                 'popular' => false],
                    ['nombre' => 'Cocker Spaniel',        'popular' => false],
                    ['nombre' => 'Pomerania',             'popular' => false],
                    ['nombre' => 'American Pit Bull',     'popular' => false],
                    ['nombre' => 'American Bully',        'popular' => false],
                    ['nombre' => 'Samoyedo',              'popular' => false],
                    ['nombre' => 'Akita Inu',             'popular' => false],
                    ['nombre' => 'Shiba Inu',             'popular' => false],
                    ['nombre' => 'Gran Danés',            'popular' => false],
                    ['nombre' => 'San Bernardo',          'popular' => false],
                    ['nombre' => 'Bulldog Inglés',        'popular' => false],
                    ['nombre' => 'Dálmata',               'popular' => false],
                    ['nombre' => 'Bichón Frisé',          'popular' => false],
                    ['nombre' => 'Shar Pei',              'popular' => false],
                    ['nombre' => 'Setter Irlandés',       'popular' => false],
                    ['nombre' => 'Weimaraner',            'popular' => false],
                    ['nombre' => 'Mastín',                'popular' => false],
                    ['nombre' => 'Basset Hound',          'popular' => false],
                    ['nombre' => 'Chow Chow',             'popular' => false],
                    ['nombre' => 'Pug',                   'popular' => false],
                    ['nombre' => 'Jack Russell Terrier',  'popular' => false],
                    ['nombre' => 'Pitbull Terrier',       'popular' => false],
                    ['nombre' => 'Mestizo',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Gato', 'nombre_plural' => 'Gatos', 'icono' => '🐱', 'orden' => 2,
                'breeds' => [
                    ['nombre' => 'Persa',                 'popular' => true],
                    ['nombre' => 'Siamés',                'popular' => true],
                    ['nombre' => 'Maine Coon',            'popular' => true],
                    ['nombre' => 'Ragdoll',               'popular' => true],
                    ['nombre' => 'Bengalí',               'popular' => true],
                    ['nombre' => 'British Shorthair',     'popular' => true],
                    ['nombre' => 'Scottish Fold',         'popular' => true],
                    ['nombre' => 'Sphynx',                'popular' => false],
                    ['nombre' => 'Abisinio',              'popular' => false],
                    ['nombre' => 'Burmés',                'popular' => false],
                    ['nombre' => 'Birmano',               'popular' => false],
                    ['nombre' => 'American Shorthair',    'popular' => false],
                    ['nombre' => 'Noruego de Bosque',     'popular' => false],
                    ['nombre' => 'Angora Turco',          'popular' => false],
                    ['nombre' => 'Devon Rex',             'popular' => false],
                    ['nombre' => 'Cornish Rex',           'popular' => false],
                    ['nombre' => 'Himalayo',              'popular' => false],
                    ['nombre' => 'Tonkinés',              'popular' => false],
                    ['nombre' => 'Manx',                  'popular' => false],
                    ['nombre' => 'Mestizo',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Caballo', 'nombre_plural' => 'Caballos', 'icono' => '🐴', 'orden' => 3,
                'breeds' => [
                    ['nombre' => 'Pura Sangre Inglés',   'popular' => true],
                    ['nombre' => 'Cuarto de Milla',       'popular' => true],
                    ['nombre' => 'Árabe',                 'popular' => true],
                    ['nombre' => 'Andaluz (PRE)',         'popular' => true],
                    ['nombre' => 'Frisón',                'popular' => true],
                    ['nombre' => 'Appaloosa',             'popular' => false],
                    ['nombre' => 'Paint Horse',           'popular' => false],
                    ['nombre' => 'Paso Fino',             'popular' => false],
                    ['nombre' => 'Criollo',               'popular' => false],
                    ['nombre' => 'Mustang',               'popular' => false],
                    ['nombre' => 'Tennessee Walker',      'popular' => false],
                    ['nombre' => 'Clydesdale',            'popular' => false],
                    ['nombre' => 'Percherón',             'popular' => false],
                    ['nombre' => 'Morgan',                'popular' => false],
                    ['nombre' => 'Shetland',              'popular' => false],
                ],
            ],
            [
                'nombre' => 'Bovino', 'nombre_plural' => 'Bovinos', 'icono' => '🐄', 'orden' => 4,
                'breeds' => [
                    ['nombre' => 'Holstein',              'popular' => true],
                    ['nombre' => 'Angus',                 'popular' => true],
                    ['nombre' => 'Brahman',               'popular' => true],
                    ['nombre' => 'Simmental',             'popular' => true],
                    ['nombre' => 'Hereford',              'popular' => false],
                    ['nombre' => 'Charolés',              'popular' => false],
                    ['nombre' => 'Limousin',              'popular' => false],
                    ['nombre' => 'Gyr',                   'popular' => false],
                    ['nombre' => 'Nelore',                'popular' => false],
                    ['nombre' => 'Brangus',               'popular' => false],
                    ['nombre' => 'Santa Gertrudis',       'popular' => false],
                    ['nombre' => 'Criollo',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Porcino', 'nombre_plural' => 'Porcinos', 'icono' => '🐷', 'orden' => 5,
                'breeds' => [
                    ['nombre' => 'Duroc',                 'popular' => true],
                    ['nombre' => 'Hampshire',             'popular' => true],
                    ['nombre' => 'Yorkshire',             'popular' => true],
                    ['nombre' => 'Landrace',              'popular' => true],
                    ['nombre' => 'Pietrain',              'popular' => false],
                    ['nombre' => 'Berkshire',             'popular' => false],
                    ['nombre' => 'Ibérico',               'popular' => false],
                    ['nombre' => 'Mangalica',             'popular' => false],
                ],
            ],
            [
                'nombre' => 'Ovino', 'nombre_plural' => 'Ovinos', 'icono' => '🐑', 'orden' => 6,
                'breeds' => [
                    ['nombre' => 'Merino',                'popular' => true],
                    ['nombre' => 'Dorset',                'popular' => true],
                    ['nombre' => 'Suffolk',               'popular' => true],
                    ['nombre' => 'Hampshire',             'popular' => false],
                    ['nombre' => 'Rambouillet',           'popular' => false],
                    ['nombre' => 'Corridale',             'popular' => false],
                    ['nombre' => 'Romney',                'popular' => false],
                    ['nombre' => 'Criolla',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Caprino', 'nombre_plural' => 'Caprinos', 'icono' => '🐐', 'orden' => 7,
                'breeds' => [
                    ['nombre' => 'Saanen',                'popular' => true],
                    ['nombre' => 'Nubian',                'popular' => true],
                    ['nombre' => 'Boer',                  'popular' => true],
                    ['nombre' => 'Alpine',                'popular' => false],
                    ['nombre' => 'Toggenburg',            'popular' => false],
                    ['nombre' => 'Angora',                'popular' => false],
                    ['nombre' => 'Criolla',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Conejo', 'nombre_plural' => 'Conejos', 'icono' => '🐰', 'orden' => 8,
                'breeds' => [
                    ['nombre' => 'Nueva Zelanda',         'popular' => true],
                    ['nombre' => 'Rex',                   'popular' => true],
                    ['nombre' => 'Angora',                'popular' => true],
                    ['nombre' => 'California',            'popular' => false],
                    ['nombre' => 'Holandés (Dutch)',      'popular' => false],
                    ['nombre' => 'Mini Lop',              'popular' => false],
                    ['nombre' => 'Lionhead',              'popular' => false],
                    ['nombre' => 'Gigante de Flandes',    'popular' => false],
                    ['nombre' => 'Chinchilla',            'popular' => false],
                    ['nombre' => 'Bélier Francés',        'popular' => false],
                ],
            ],
            [
                'nombre' => 'Ave de Corral', 'nombre_plural' => 'Aves de Corral', 'icono' => '🐓', 'orden' => 9,
                'breeds' => [
                    ['nombre' => 'Rhode Island Red',      'popular' => true],
                    ['nombre' => 'Leghorn',               'popular' => true],
                    ['nombre' => 'Plymouth Rock',         'popular' => false],
                    ['nombre' => 'Australorp',            'popular' => false],
                    ['nombre' => 'Wyandotte',             'popular' => false],
                    ['nombre' => 'Brahma',                'popular' => false],
                    ['nombre' => 'Sussex',                'popular' => false],
                    ['nombre' => 'Orpington',             'popular' => false],
                    ['nombre' => 'Cornish',               'popular' => false],
                    ['nombre' => 'Criolla',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Ave Exótica', 'nombre_plural' => 'Aves Exóticas', 'icono' => '🦜', 'orden' => 10,
                'breeds' => [
                    ['nombre' => 'Guacamaya',             'popular' => true],
                    ['nombre' => 'Loro Amazona',          'popular' => true],
                    ['nombre' => 'Cacatúa',               'popular' => true],
                    ['nombre' => 'Periquito',             'popular' => true],
                    ['nombre' => 'Agapornis (Inseparable)', 'popular' => true],
                    ['nombre' => 'Ninfa (Cockatiel)',     'popular' => false],
                    ['nombre' => 'Lori',                  'popular' => false],
                    ['nombre' => 'Cotorra',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Pez Ornamental', 'nombre_plural' => 'Peces Ornamentales', 'icono' => '🐠', 'orden' => 11,
                'breeds' => [
                    ['nombre' => 'Guppy',                 'popular' => true],
                    ['nombre' => 'Betta',                 'popular' => true],
                    ['nombre' => 'Goldfish',              'popular' => true],
                    ['nombre' => 'Koi',                   'popular' => true],
                    ['nombre' => 'Disco (Discus)',         'popular' => false],
                    ['nombre' => 'Escalar (Angelfish)',   'popular' => false],
                    ['nombre' => 'Neón Tetra',            'popular' => false],
                    ['nombre' => 'Molly',                 'popular' => false],
                    ['nombre' => 'Platy',                 'popular' => false],
                    ['nombre' => 'Arowana',               'popular' => false],
                ],
            ],
            [
                'nombre' => 'Reptil', 'nombre_plural' => 'Reptiles', 'icono' => '🦎', 'orden' => 12,
                'breeds' => [
                    ['nombre' => 'Gecko Leopardo',        'popular' => true],
                    ['nombre' => 'Dragón Barbudo',        'popular' => true],
                    ['nombre' => 'Iguana Verde',          'popular' => true],
                    ['nombre' => 'Pitón Bola',            'popular' => false],
                    ['nombre' => 'Boa Constrictor',       'popular' => false],
                    ['nombre' => 'Tortuga Rusa',          'popular' => false],
                    ['nombre' => 'Tortuga de Caja',       'popular' => false],
                    ['nombre' => 'Camaleón',              'popular' => false],
                ],
            ],
            [
                'nombre' => 'Otro', 'nombre_plural' => 'Otros', 'icono' => '🐾', 'orden' => 13,
                'breeds' => [
                    ['nombre' => 'Sin raza específica',   'popular' => true],
                ],
            ],
        ];

        foreach ($catalog as $speciesData) {
            $breeds = $speciesData['breeds'];
            unset($speciesData['breeds']);
            $species = Species::create($speciesData);
            foreach ($breeds as $breed) {
                $species->breeds()->create($breed);
            }
        }
    }
}
