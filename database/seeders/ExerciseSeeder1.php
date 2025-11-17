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
        // DB::table('exercises')->truncate();

        $exercises = [
            // --- TREN SUPERIOR: PECHO ---
            [
                'name' => "Press de Banca Plano",
                'muscle_group' => "pecho",
                'description' => "Ejercicio fundamental para el desarrollo de la masa muscular del pecho, tríceps y hombro anterior.",
                'image_path' => "/images/press_banca_plano.jpg",
                'gif_path' => "/gifs/press_banca_plano.gif",
                'instructions' => "1. Acuéstate en el banco, pies planos en el suelo, barra a la altura de los ojos.\n2. Sujeta la barra con un agarre medio (ligeramente más ancho que los hombros).\n3. Desengancha la barra y bájala lentamente hacia el medio del pecho, manteniendo los codos en un ángulo de 45 grados.\n4. Empuja la barra hacia arriba de forma explosiva hasta extender los brazos, pero sin bloquear los codos.",
                'tips' => "Mantén los omóplatos retraídos y el pecho arqueado.\nAsegura una pausa muy breve en la parte inferior para evitar rebotes.\nLa respiración: inhala al bajar, exhala al subir.",
            ],
            [
                'name' => "Aperturas con Mancuernas",
                'muscle_group' => "pecho",
                'description' => "Aísla el pectoral y mejora la conexión mente-músculo. Se realiza en un banco plano o inclinado.",
                'image_path' => "/images/aperturas_mancuernas.jpg",
                'gif_path' => "/gifs/aperturas_mancuernas.gif",
                'instructions' => "1. Acuéstate en el banco con una mancuerna en cada mano, palmas enfrentadas.\n2. Extiende los brazos hacia el techo con una ligera flexión en los codos.\n3. Abre lentamente los brazos en un amplio arco hasta sentir un estiramiento en el pecho.\n4. Utiliza los músculos del pecho para devolver las mancuernas a la posición inicial, juntándolas por encima del pecho.",
                'tips' => "La ligera flexión del codo debe mantenerse constante durante todo el movimiento.\nNo bajes las mancuernas más allá de la línea del hombro para proteger la articulación.\nVisualiza que estás abrazando un barril grande.",
            ],
            
            // --- TREN SUPERIOR: ESPALDA ---
            [
                'name' => "Dominadas (Pull-ups)",
                'muscle_group' => "espalda",
                'description' => "Excelente ejercicio compuesto para ensanchar la espalda, enfocándose en el dorsal ancho.",
                'image_path' => "/images/dominadas.jpg",
                'gif_path' => "/gifs/dominadas.gif",
                'instructions' => "1. Agárrate a la barra con un agarre prono (palmas hacia adelante) más ancho que los hombros.\n2. Comienza desde una posición de colgado total.\n3. Tira de tu cuerpo hacia arriba, juntando los omóplatos y llevando el pecho hacia la barra.\n4. Baja lentamente hasta la posición inicial, controlando el peso.",
                'tips' => "Evita el 'kipping' (balanceo). El movimiento debe ser controlado y estricto.\nConcéntrate en tirar con los codos, no con las manos.\nRealiza el rango completo de movimiento.",
            ],
            [
                'name' => "Remo con Barra",
                'muscle_group' => "espalda",
                'description' => "Construye grosor en la espalda media y baja. Mantener la espalda recta es crucial.",
                'image_path' => "/images/remo_barra.jpg",
                'gif_path' => "/gifs/remo_barra.gif",
                'instructions' => "1. Colócate sobre la barra con las rodillas ligeramente flexionadas.\n2. Inclina el torso hacia adelante unos 45 grados, manteniendo la espalda recta.\n3. Sube la barra hacia el abdomen inferior, tirando con los codos y apretando la espalda.\n4. Baja la barra de forma controlada hasta la posición inicial.",
                'tips' => "Mantén la cabeza alineada con la columna vertebral (mira hacia el suelo).\nSi la espalda se redondea, reduce el peso.\nEl agarre debe ser ligeramente más ancho que los hombros.",
            ],

            // --- TREN SUPERIOR: HOMBROS ---
            [
                'name' => "Press Militar Sentado",
                'muscle_group' => "hombros",
                'description' => "Ejercicio principal para el desarrollo del deltoide frontal y medial.",
                'image_path' => "/images/press_militar_sentado.jpg",
                'gif_path' => "/gifs/press_militar_sentado.gif",
                'instructions' => "1. Siéntate en un banco con respaldo recto, sujeta la barra con agarre prono a la altura de los hombros.\n2. Empuja la barra verticalmente hacia arriba, justo por encima de la cabeza.\n3. Bloquea los codos en la parte superior, asegurando que la barra esté alineada con la cabeza.\n4. Baja la barra lentamente y con control hasta la posición inicial (altura de la barbilla o nariz).",
                'tips' => "Controla el movimiento en todo momento; no uses impulso.\nMantén el tronco apretado para proteger la zona lumbar.\nEl banco evita que uses las piernas para hacer trampa.",
            ],
            
            // --- TREN SUPERIOR: BICEPS ---
            [
                'name' => "Curl de Bíceps con Barra",
                'muscle_group' => "biceps",
                'description' => "Movimiento básico para el desarrollo del bíceps braquial y braquial anterior.",
                'image_path' => "/images/curl_biceps_barra.jpg",
                'gif_path' => "/gifs/curl_biceps_barra.gif",
                'instructions' => "1. Ponte de pie, tronco recto, sujeta la barra con agarre supino (palmas hacia arriba) a la anchura de los hombros.\n2. Manteniendo los codos pegados a los costados, levanta la barra con un movimiento de flexión, contrayendo el bíceps.\n3. Aprieta el bíceps fuertemente en la parte superior.\n4. Baja la barra lentamente, resistiendo el peso hasta la extensión completa del brazo.",
                'tips' => "Evita balancear el torso para asistir el movimiento (hacer trampa).\nEl movimiento debe ser lento y controlado, especialmente la fase negativa (bajada).\nConcéntrate en la contracción del músculo.",
            ],
            
            // --- TREN SUPERIOR: TRICEPS ---
            [
                'name' => "Extensión de Tríceps sobre la Cabeza",
                'muscle_group' => "triceps",
                'description' => "Estira la cabeza larga del tríceps, esencial para su desarrollo completo.",
                'image_path' => "/images/extension_triceps_cabeza.jpg",
                'gif_path' => "/gifs/extension_triceps_cabeza.gif",
                'instructions' => "1. Siéntate o ponte de pie, sujeta una mancuerna o barra Z con ambas manos sobre la cabeza.\n2. Mantén los codos apuntando hacia arriba, pegados a la cabeza.\n3. Baja lentamente el peso por detrás de la cabeza, doblando solo los codos.\n4. Extiende los brazos vigorosamente para levantar el peso a la posición inicial, sintiendo la contracción del tríceps.",
                'tips' => "No dejes que los codos se abran hacia los lados; mantenlos cerrados.\nEs un movimiento de aislamiento; usa un peso que te permita mantener la técnica.\nSiéntete libre de realizarlo sentado para mayor estabilidad.",
            ],

            // --- TREN INFERIOR: PIERNAS ---
            [
                'name' => "Sentadilla con Barra",
                'muscle_group' => "piernas",
                'description' => "El rey de los ejercicios. Trabaja cuádriceps, glúteos y lumbares.",
                'image_path' => "/images/sentadilla_barra.jpg",
                'gif_path' => "/gifs/sentadilla_barra.gif",
                'instructions' => "1. Coloca la barra sobre los trapecios (espalda alta), pies a la anchura de los hombros.\n2. Inicia el descenso empujando las caderas hacia atrás, como si te fueras a sentar.\n3. Baja hasta que los muslos estén paralelos al suelo o más abajo, manteniendo el pecho erguido.\n4. Sube empujando con los talones, extendiendo caderas y rodillas.",
                'tips' => "Mantén la espalda recta y el core activado.\nAsegúrate de que las rodillas sigan la dirección de los pies.\nBusca profundidad para maximizar el trabajo de los glúteos.",
            ],
            [
                'name' => "Prensa de Piernas",
                'muscle_group' => "piernas",
                'description' => "Permite levantar mucho peso aislando los cuádriceps y glúteos sin la tensión lumbar de la sentadilla.",
                'image_path' => "/images/prensa_piernas.jpg",
                'gif_path' => "/gifs/prensa_piernas.gif",
                'instructions' => "1. Siéntate en la máquina y coloca los pies en la plataforma, separados a la altura de los hombros.\n2. Desbloquea la seguridad y baja la plataforma doblando las rodillas hasta un ángulo de 90 grados.\n3. Empuja con los talones para extender las piernas, sin bloquear las rodillas al final del movimiento.\n4. Regresa lentamente para completar la repetición.",
                'tips' => "No bajes demasiado, ya que esto puede redondear la espalda baja y causar lesiones.\nPara enfocarte más en el cuádriceps, coloca los pies más bajos y juntos.\nPara glúteos/femorales, coloca los pies más altos y separados.",
            ],

            // --- TREN INFERIOR: FEMORALES ---
            [
                'name' => "Peso Muerto Rumano",
                'muscle_group' => "femorales",
                'description' => "Excelente para el desarrollo de los isquiotibiales y glúteos.",
                'image_path' => "/images/peso_muerto_rumano.jpg",
                'gif_path' => "/gifs/peso_muerto_rumano.gif",
                'instructions' => "1. De pie, sujeta la barra con agarre prono a la altura de los hombros.\n2. Con las rodillas ligeramente flexionadas (y fijas), inclínate hacia adelante desde la cadera.\n3. Baja la barra por delante de las piernas, manteniendo la espalda recta, hasta sentir un estiramiento profundo en los isquiotibiales.\n4. Vuelve a la posición inicial apretando los glúteos en la parte superior.",
                'tips' => "El movimiento es un 'bisagra de cadera', no una sentadilla.\nLa barra se mantiene muy cerca del cuerpo en todo momento.\nPrioriza el estiramiento y la forma sobre el peso levantado.",
            ],

            // --- TREN INFERIOR: GLÚTEOS ---
            [
                'name' => "Hip Thrust",
                'muscle_group' => "gluteos",
                'description' => "El mejor constructor de glúteos, con énfasis en la extensión de cadera.",
                'image_path' => "/images/hip_thrust.jpg",
                'gif_path' => "/gifs/hip_thrust.gif",
                'instructions' => "1. Siéntate en el suelo con la espalda alta apoyada en un banco, una barra (o mancuerna) sobre las caderas.\n2. Pies planos en el suelo, separados a la altura de las caderas, rodillas dobladas.\n3. Impulsa las caderas hacia arriba, utilizando los glúteos, hasta que el torso y los muslos estén paralelos al suelo.\n4. Aprieta los glúteos fuertemente en la cima y baja lentamente para completar la repetición.",
                'tips' => "Mantén la barbilla metida (mirando hacia adelante) para mantener la columna alineada.\nEl foco principal es la máxima contracción del glúteo en la cima.\nUtiliza una almohadilla o protector para la barra en las caderas.",
            ],
        ];

        // Insertar los datos
        DB::table('exercises')->insert($exercises);
    }
}