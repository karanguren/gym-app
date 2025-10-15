<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la tabla antes de insertar
        DB::table('exercises')->truncate();

        $exercises = [
            // --- TREN SUPERIOR: PECHO ---
            [
                'name' => 'Press de Banca Plano',
                'muscle_group' => 'pecho',
                'description' => 'Ejercicio fundamental para el desarrollo de la masa muscular del pecho, tríceps y hombro anterior.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Press+Banca',
                'gif_path' => 'https://example.com/gifs/press_banca.gif', // Placeholder GIF
            ],
            [
                'name' => 'Aperturas con Mancuernas',
                'muscle_group' => 'pecho',
                'description' => 'Aísla el pectoral y mejora la conexión mente-músculo. Se realiza en un banco plano o inclinado.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Aperturas',
                'gif_path' => 'https://example.com/gifs/aperturas.gif',
            ],
            
            // --- TREN SUPERIOR: ESPALDA ---
            [
                'name' => 'Dominadas (Pull-ups)',
                'muscle_group' => 'espalda',
                'description' => 'Excelente ejercicio compuesto para ensanchar la espalda, enfocándose en el dorsal ancho.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Dominadas',
                'gif_path' => 'https://example.com/gifs/dominadas.gif',
            ],
            [
                'name' => 'Remo con Barra',
                'muscle_group' => 'espalda',
                'description' => 'Construye grosor en la espalda media y baja. Mantener la espalda recta es crucial.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Remo+Barra',
                'gif_path' => 'https://example.com/gifs/remo_barra.gif',
            ],

            // --- TREN SUPERIOR: HOMBROS ---
            [
                'name' => 'Press Militar Sentado',
                'muscle_group' => 'hombros',
                'description' => 'Ejercicio principal para el desarrollo del deltoide frontal y medial.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Press+Militar',
                'gif_path' => 'https://example.com/gifs/press_militar.gif',
            ],
            
            // --- TREN SUPERIOR: BICEPS ---
            [
                'name' => 'Curl de Bíceps con Barra',
                'muscle_group' => 'biceps',
                'description' => 'Movimiento básico para el desarrollo del bíceps braquial y braquial anterior.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Curl+Barra',
                'gif_path' => 'https://example.com/gifs/curl_barra.gif',
            ],
            
            // --- TREN SUPERIOR: TRICEPS ---
            [
                'name' => 'Extensión de Tríceps sobre la Cabeza',
                'muscle_group' => 'triceps',
                'description' => 'Estira la cabeza larga del tríceps, esencial para su desarrollo completo.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Triceps+Ext',
                'gif_path' => 'https://example.com/gifs/triceps_extension.gif',
            ],

            // --- TREN INFERIOR: PIERNAS ---
            [
                'name' => 'Sentadilla con Barra',
                'muscle_group' => 'piernas',
                'description' => 'El rey de los ejercicios. Trabaja cuádriceps, glúteos y lumbares.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Sentadilla',
                'gif_path' => 'https://example.com/gifs/sentadilla.gif',
            ],
            [
                'name' => 'Prensa de Piernas',
                'muscle_group' => 'piernas',
                'description' => 'Permite levantar mucho peso aislando los cuádriceps y glúteos sin la tensión lumbar de la sentadilla.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Prensa',
                'gif_path' => 'https://example.com/gifs/prensa.gif',
            ],

            // --- TREN INFERIOR: FEMORALES ---
            [
                'name' => 'Peso Muerto Rumano',
                'muscle_group' => 'femorales',
                'description' => 'Excelente para el desarrollo de los isquiotibiales y glúteos.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Peso+Muerto+Rumano',
                'gif_path' => 'https://example.com/gifs/peso_muerto_rumano.gif',
            ],

            // --- TREN INFERIOR: GLÚTEOS ---
            [
                'name' => 'Hip Thrust',
                'muscle_group' => 'gluteos',
                'description' => 'El mejor constructor de glúteos, con énfasis en la extensión de cadera.',
                'image_path' => 'https://placehold.co/400x400/394c5e/white?text=Hip+Thrust',
                'gif_path' => 'https://example.com/gifs/hip_thrust.gif',
            ],
        ];

        // Insertar los datos
        DB::table('exercises')->insert($exercises);
    }
}
