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
        // Limpiar la tabla antes de insertar (Descomentar si es necesario)
        // DB::table('exercises')->truncate();

        $exercises = [];

        // --- TREN SUPERIOR: PECHO (50 EJERCICIOS) ---

        // PECHO: PLANO (Básicos)
        $exercises[] = [
            'name' => "Press de Banca Plano con Barra",
            'muscle_group' => "pecho",
            'description' => "Ejercicio compuesto fundamental para el desarrollo del pectoral mayor y tríceps.",
            'image_path' => "/images/press_banca_plano.jpg",
            'gif_path' => "/gifs/press_banca_plano.gif",
            'instructions' => "Acuéstate, agarre medio. Baja la barra al medio del pecho, empuja explosivamente. Mantén los omóplatos retraídos.",
            'tips' => "Mantén el arco natural en la espalda baja. Codos a 45 grados. Inhala al bajar, exhala al subir.",
        ];
        $exercises[] = [
            'name' => "Press de Banca Plano con Mancuernas",
            'muscle_group' => "pecho",
            'description' => "Permite un mayor rango de movimiento y trabaja la estabilidad.",
            'image_path' => "/images/press_banca_plano_mancuernas.jpg",
            'gif_path' => "/gifs/press_banca_plano_mancuernas.gif",
            'instructions' => "Mancuernas sobre el pecho, palmas enfrentadas o hacia adelante. Baja lentamente, estirando el pectoral. Empuja hacia arriba.",
            'tips' => "Comienza con las mancuernas en tus muslos y patéalas hasta la posición inicial. Concéntrate en la simetría.",
        ];
        $exercises[] = [
            'name' => "Aperturas Planas con Mancuernas",
            'muscle_group' => "pecho",
            'description' => "Aisla el pectoral, enfocándose en la parte media y en el estiramiento.",
            'image_path' => "/images/aperturas_planas_mancuernas.jpg",
            'gif_path' => "/gifs/aperturas_planas_mancuernas.gif",
            'instructions' => "Brazos extendidos con codos ligeramente doblados. Abre en un arco amplio. Junta las mancuernas sin tocarlas arriba.",
            'tips' => "El ligero ángulo del codo debe ser constante. Visualiza que estás abrazando un árbol grande.",
        ];
        $exercises[] = [
            'name' => "Press en Máquina Smith Plano",
            'muscle_group' => "pecho",
            'description' => "Press de pecho guiado para enfocarse en la contracción sin preocuparse por la estabilización.",
            'image_path' => "/images/press_smith_plano.jpg",
            'gif_path' => "/gifs/press_smith_plano.gif",
            'instructions' => "Coloca el banco debajo de la barra, asegurando que baje hacia el medio del pecho. Desbloquea y realiza el press.",
            'tips' => "Útil para entrenamiento de alta intensidad y repeticiones forzadas. No apto para todos los ángulos de hombro.",
        ];
        $exercises[] = [
            'name' => "Flexiones de Pecho (Push-ups)",
            'muscle_group' => "pecho",
            'description' => "Ejercicio de peso corporal para fuerza y resistencia del pecho, hombros y tríceps.",
            'image_path' => "/images/flexiones_pecho.jpg",
            'gif_path' => "/gifs/flexiones_pecho.gif",
            'instructions' => "Manos ligeramente más anchas que los hombros. Baja el pecho cerca del suelo. Empuja de vuelta manteniendo el cuerpo recto.",
            'tips' => "Mantén el core apretado para evitar que la cadera caiga. Si es muy difícil, hazlas con rodillas apoyadas.",
        ];
        // PECHO: INCLINADO (Alto)
        $exercises[] = [
            'name' => "Press Inclinado con Barra",
            'muscle_group' => "pecho",
            'description' => "Enfoca la porción superior del pectoral mayor.",
            'image_path' => "/images/press_inclinado_barra.jpg",
            'gif_path' => "/gifs/press_inclinado_barra.gif",
            'instructions' => "Banco inclinado a 30-45 grados. Baja la barra a la clavícula. Empuja hacia arriba.",
            'tips' => "Un ángulo demasiado alto (más de 45°) pone demasiado énfasis en los hombros. Controla el descenso.",
        ];
        $exercises[] = [
            'name' => "Press Inclinado con Mancuernas",
            'muscle_group' => "pecho",
            'description' => "Permite rotar las mancuernas para una mejor contracción del pectoral superior.",
            'image_path' => "/images/press_inclinado_mancuernas.jpg",
            'gif_path' => "/gifs/press_inclinado_mancuernas.gif",
            'instructions' => "Banco inclinado. Press vertical. Puedes empezar con agarre neutral y rotar a prono al empujar.",
            'tips' => "Concéntrate en apretar el pectoral superior en la cima del movimiento.",
        ];
        $exercises[] = [
            'name' => "Aperturas Inclinadas con Mancuernas",
            'muscle_group' => "pecho",
            'description' => "Estiramiento y aislamiento de la parte superior del pecho.",
            'image_path' => "/images/aperturas_inclinadas_mancuernas.jpg",
            'gif_path' => "/gifs/aperturas_inclinadas_mancuernas.gif",
            'instructions' => "Movimiento de arco similar al plano, pero en banco inclinado.",
            'tips' => "Controla el peso para que el movimiento sea estable y no se convierta en un press.",
        ];
        $exercises[] = [
            'name' => "Cruce de Cables desde Abajo (Low Cable Crossover)",
            'muscle_group' => "pecho",
            'description' => "Énfasis en la porción superior del pectoral. Junta los mangos frente al pecho a la altura de los hombros.",
            'image_path' => "/images/cruce_cables_bajo.jpg",
            'gif_path' => "/gifs/cruce_cables_bajo.gif",
            'instructions' => "Poleas en posición baja. Da un paso adelante. Junta los mangos frente al cuerpo a la altura del pecho. Aprieta el pecho superior.",
            'tips' => "Mantén una ligera flexión en los codos. Controla la fase excéntrica (estiramiento).",
        ];
        // PECHO: DECLINADO (Bajo)
        $exercises[] = [
            'name' => "Press Declinado con Barra",
            'muscle_group' => "pecho",
            'description' => "Enfoca la porción inferior del pectoral mayor, permitiendo levantar más peso.",
            'image_path' => "/images/press_declinado_barra.jpg",
            'gif_path' => "/gifs/press_declinado_barra.gif",
            'instructions' => "Banco declinado. Baja la barra al pecho inferior. Empuja hacia arriba.",
            'tips' => "Asegúrate de que los pies estén anclados de forma segura. Menor recorrido, más fuerza.",
        ];

        // Se añaden 40 ejercicios más de Pecho (variaciones, máquinas, cables, etc.)
        for ($i = 11; $i <= 50; $i++) {
            $name = ($i % 2 == 0) ? "Press Sentado en Máquina (Pecho) Var. $i" : "Fondos en Paralelas (Pecho) Var. $i";
            $exercises[] = [
                'name' => $name,
                'muscle_group' => "pecho",
                'description' => "Ejercicio auxiliar para terminar de agotar las fibras musculares del pecho. (Placeholder $i)",
                'image_path' => "/images/pecho_ejercicio_$i.jpg",
                'gif_path' => "/gifs/pecho_ejercicio_$i.gif",
                'instructions' => "Ajustar la máquina a la altura del pecho y empujar hacia adelante. Mantener la espalda contra el respaldo.",
                'tips' => "Alternar el agarre (neutro, ancho, estrecho) para cambiar el enfoque muscular. No bloquear los codos.",
            ];
        }

        // --- TREN SUPERIOR: ESPALDA (50 EJERCICIOS) ---

        // ESPALDA: LAT (Ancho/Vertical)
        $exercises[] = [
            'name' => "Jalón al Pecho Agarre Ancho",
            'muscle_group' => "espalda",
            'description' => "Desarrolla el dorsal ancho (amplitud) y la parte superior de la espalda.",
            'image_path' => "/images/jalon_pecho_ancho.jpg",
            'gif_path' => "/gifs/jalon_pecho_ancho.gif",
            'instructions' => "Agarre prono muy ancho. Inclina ligeramente el torso hacia atrás. Tira de la barra hacia el pecho superior, llevando los codos hacia abajo y atrás.",
            'tips' => "Concéntrate en la depresión escapular. Evita usar el impulso del cuerpo. Bajar lento para estirar.",
        ];
        $exercises[] = [
            'name' => "Jalón al Pecho Agarre Cerrado",
            'muscle_group' => "espalda",
            'description' => "Enfocado en el dorsal inferior y en el grosor medio de la espalda.",
            'image_path' => "/images/jalon_pecho_cerrado.jpg",
            'gif_path' => "/gifs/jalon_pecho_cerrado.gif",
            'instructions' => "Usa un agarre neutral o supino cerrado. Tira de la barra hacia el pecho. Aprieta la espalda.",
            'tips' => "Permite mayor rango de movimiento y es más amigable con los hombros.",
        ];
        $exercises[] = [
            'name' => "Dominadas (Pull-ups) Agarre Prono",
            'muscle_group' => "espalda",
            'description' => "Mejor ejercicio de peso corporal para ancho de espalda.",
            'image_path' => "/images/dominadas_prono.jpg",
            'gif_path' => "/gifs/dominadas_prono.gif",
            'instructions' => "Agarrate a la barra con agarre prono ancho. Sube hasta que la barbilla pase la barra. Baja controladamente.",
            'tips' => "Si no puedes hacerlas, usa una banda de resistencia o la máquina asistida. Evita balancearte.",
        ];
        $exercises[] = [
            'name' => "Dominadas (Chin-ups) Agarre Supino",
            'muscle_group' => "espalda",
            'description' => "Mayor activación de bíceps, enfocando el dorsal inferior.",
            'image_path' => "/images/dominadas_supino.jpg",
            'gif_path' => "/gifs/dominadas_supino.gif",
            'instructions' => "Agarrate a la barra con agarre supino (palmas hacia ti), separación al ancho de los hombros. Sube y aprieta.",
            'tips' => "Más fáciles de realizar que las Pull-ups para principiantes.",
        ];
        // ESPALDA: REMO (Grosor/Horizontal)
        $exercises[] = [
            'name' => "Remo con Barra Inclinado (Bent-Over Row)",
            'muscle_group' => "espalda",
            'description' => "Construye un gran grosor en la espalda media y baja (erectores, trapecios, dorsales).",
            'image_path' => "/images/remo_barra_inclinado.jpg",
            'gif_path' => "/gifs/remo_barra_inclinado.gif",
            'instructions' => "Torso a 45 grados, espalda recta. Tira la barra hacia el abdomen. Aprieta los omóplatos.",
            'tips' => "Mantén la posición del torso estricta. Usa cinturón si el peso es alto. Inhala en la bajada.",
        ];
        $exercises[] = [
            'name' => "Remo con Mancuerna a un Brazo",
            'muscle_group' => "espalda",
            'description' => "Excelente para simetría, concentración y trabajo del dorsal de forma unilateral.",
            'image_path' => "/images/remo_man_unilateral.jpg",
            'gif_path' => "/gifs/remo_man_unilateral.gif",
            'instructions' => "Apoya una mano y rodilla en el banco. Fila la mancuerna hacia la cadera, estirando el brazo en la bajada.",
            'tips' => "Concéntrate en la conexión mente-músculo. No uses el hombro para levantar; usa la espalda.",
        ];
        $exercises[] = [
            'name' => "Remo Sentado en Polea Baja",
            'muscle_group' => "espalda",
            'description' => "Ejercicio básico para la espalda media, fácil de variar con diferentes agarres.",
            'image_path' => "/images/remo_polea_sentado.jpg",
            'gif_path' => "/gifs/remo_polea_sentado.gif",
            'instructions' => "Siéntate, espalda recta. Tira del agarre hacia el abdomen. Retrae los omóplatos. Extiende los brazos sin encorvar la espalda.",
            'tips' => "Evita inclinarte demasiado hacia adelante y hacia atrás. El movimiento debe venir de los brazos.",
        ];
        // ESPALDA: LUMBARES/TRAPECIOS
        $exercises[] = [
            'name' => "Encogimiento de Hombros con Mancuernas (Shrugs)",
            'muscle_group' => "espalda",
            'description' => "Enfocado en el trapecio superior.",
            'image_path' => "/images/encogimiento_hombros.jpg",
            'gif_path' => "/gifs/encogimiento_hombros.gif",
            'instructions' => "De pie con mancuernas. Levanta los hombros directamente hacia las orejas. Mantén una pausa en la cima.",
            'tips' => "No ruedes los hombros; el movimiento debe ser puramente vertical.",
        ];
        $exercises[] = [
            'name' => "Extensiones Lumbares (Hiperextensiones)",
            'muscle_group' => "espalda",
            'description' => "Fortalece los erectores espinales, glúteos y femorales.",
            'image_path' => "/images/extensiones_lumbares.jpg",
            'gif_path' => "/gifs/extensiones_lumbares.gif",
            'instructions' => "Colócate en el banco. Baja el torso, manteniendo la espalda recta. Sube hasta que el cuerpo esté alineado.",
            'tips' => "No subas más allá de la línea recta para evitar hiperextensión. Si es fácil, usa un disco en el pecho.",
        ];

        // Se añaden 41 ejercicios más de Espalda (variaciones de remo, jalones, peso muerto, etc.)
        for ($i = 51; $i <= 100; $i++) {
            $name = ($i % 2 == 0) ? "Remo en Máquina T (T-Bar Row) Var. $i" : "Jalón a un Brazo con Cable Var. $i";
            $exercises[] = [
                'name' => $name,
                'muscle_group' => "espalda",
                'description' => "Variación avanzada para trabajar el grosor y amplitud de la espalda. (Placeholder $i)",
                'image_path' => "/images/espalda_ejercicio_$i.jpg",
                'gif_path' => "/gifs/espalda_ejercicio_$i.gif",
                'instructions' => "Enfocarse en la retracción escapular y mantener el core activo. Controlar la fase negativa (estiramiento).",
                'tips' => "Probar diferentes agarres (ancho, cerrado, neutral) para enfatizar distintas áreas del dorsal. Evitar el impulso corporal.",
            ];
        }

        // --- TREN SUPERIOR: HOMBROS (50 EJERCICIOS) ---

        // HOMBROS: DELTOIDES ANTERIORES
        $exercises[] = [
            'name' => "Press Militar con Barra de Pie",
            'muscle_group' => "hombros",
            'description' => "Press de hombros completo, trabaja hombro anterior, tríceps y core.",
            'image_path' => "/images/press_militar_pie.jpg",
            'gif_path' => "/gifs/press_militar_pie.gif",
            'instructions' => "Barra a la altura del pecho. Empuja verticalmente por encima de la cabeza. No uses las rodillas para empujar.",
            'tips' => "Mantén los glúteos apretados y el tronco firme para evitar arquear la espalda.",
        ];
        $exercises[] = [
            'name' => "Press de Hombros Sentado con Mancuernas",
            'muscle_group' => "hombros",
            'description' => "Versión más estable para aislar los deltoides.",
            'image_path' => "/images/press_hombros_mancuernas_sentado.jpg",
            'gif_path' => "/gifs/press_hombros_mancuernas_sentado.gif",
            'instructions' => "Banco con respaldo. Empuja las mancuernas desde la altura de la oreja hasta arriba.",
            'tips' => "Evita que las mancuernas se toquen en la cima. Baja el peso controladamente.",
        ];
        $exercises[] = [
            'name' => "Elevaciones Frontales con Mancuernas",
            'muscle_group' => "hombros",
            'description' => "Aísla el deltoide anterior. Útil para pre-agotamiento.",
            'image_path' => "/images/elevaciones_frontales.jpg",
            'gif_path' => "/gifs/elevaciones_frontales.gif",
            'instructions' => "De pie. Levanta las mancuernas hacia adelante hasta la altura del hombro, con los brazos rectos. Baja lentamente.",
            'tips' => "No uses pesos muy pesados; es un ejercicio de aislamiento. No levantar más alto que los hombros.",
        ];
        // HOMBROS: DELTOIDES LATERALES
        $exercises[] = [
            'name' => "Elevaciones Laterales con Mancuernas",
            'muscle_group' => "hombros",
            'description' => "El mejor ejercicio para el deltoide medial (que da amplitud).",
            'image_path' => "/images/elevaciones_laterales_man.jpg",
            'gif_path' => "/gifs/elevaciones_laterales_man.gif",
            'instructions' => "Levanta las mancuernas a los lados hasta la altura del hombro. Vierte el agua de una jarra (pulgar hacia abajo).",
            'tips' => "Piensa en empujar con los codos. Un agarre neutral ayuda a aislar mejor el músculo. Control en la bajada.",
        ];
        $exercises[] = [
            'name' => "Elevaciones Laterales en Polea Baja",
            'muscle_group' => "hombros",
            'description' => "Mantiene la tensión constante durante todo el movimiento.",
            'image_path' => "/images/elevaciones_laterales_polea.jpg",
            'gif_path' => "/gifs/elevaciones_laterales_polea.gif",
            'instructions' => "Polea baja. Agarra el cable con la mano opuesta. Levanta lateralmente hasta la altura del hombro.",
            'tips' => "El cable genera resistencia incluso en la posición inicial, lo que maximiza la activación.",
        ];
        // HOMBROS: DELTOIDES POSTERIORES
        $exercises[] = [
            'name' => "Face Pulls (Jalones a la Cara)",
            'muscle_group' => "hombros",
            'description' => "Excelente para fortalecer la parte trasera de los hombros y mejorar la postura.",
            'image_path' => "/images/face_pulls.jpg",
            'gif_path' => "/gifs/face_pulls.gif",
            'instructions' => "Polea a la altura del pecho/cabeza con cuerda. Tira de la cuerda hacia la cara, separando las puntas de la cuerda.",
            'tips' => "El movimiento se centra en la rotación externa y la retracción. Usa un peso ligero y alta repetición.",
        ];
        $exercises[] = [
            'name' => "Pájaros con Mancuernas Inclinado",
            'muscle_group' => "hombros",
            'description' => "Aísla el deltoide posterior sin involucrar demasiado la espalda media.",
            'image_path' => "/images/pajaros_mancuernas.jpg",
            'gif_path' => "/gifs/pajaros_mancuernas.gif",
            'instructions' => "Pecho apoyado en banco inclinado. Levanta las mancuernas a los lados en un arco, como si fueran alas.",
            'tips' => "Mantén los codos ligeramente doblados. Evita que los trapecios tomen el control.",
        ];

        // Se añaden 43 ejercicios más de Hombros (variaciones de press, elevaciones en máquina, rotadores)
        for ($i = 101; $i <= 150; $i++) {
            $name = ($i % 2 == 0) ? "Press Arnold Sentado Var. $i" : "Elevaciones Laterales con Cable Inclinado Var. $i";
            $exercises[] = [
                'name' => $name,
                'muscle_group' => "hombros",
                'description' => "Movimiento de aislamiento para el deltoide posterior/medial. (Placeholder $i)",
                'image_path' => "/images/hombro_ejercicio_$i.jpg",
                'gif_path' => "/gifs/hombro_ejercicio_$i.gif",
                'instructions' => "Ejecutar el movimiento lentamente, controlando el peso tanto en la subida como en la bajada. Sentir la contracción en la parte superior.",
                'tips' => "Prioriza la técnica antes que el peso. Los hombros se lesionan fácilmente. Calentamiento esencial.",
            ];
        }

        // --- TREN SUPERIOR: BRAZOS (50 EJERCICIOS) ---

        // BÍCEPS
        $exercises[] = [
            'name' => "Curl de Bíceps con Barra Z",
            'muscle_group' => "biceps",
            'description' => "Curl de bíceps que reduce la tensión en las muñecas.",
            'image_path' => "/images/curl_barra_z.jpg",
            'gif_path' => "/gifs/curl_barra_z.gif",
            'instructions' => "Agarre supino. Codos fijos a los costados. Flexiona los brazos hasta el hombro. Baja lentamente.",
            'tips' => "La fase negativa (bajada) es crucial para el crecimiento. No balancear el cuerpo.",
        ];
        $exercises[] = [
            'name' => "Curl de Martillo con Mancuernas",
            'muscle_group' => "biceps",
            'description' => "Enfocado en el braquial y braquiorradial (grosor del antebrazo y bíceps).",
            'image_path' => "/images/curl_martillo_man.jpg",
            'gif_path' => "/gifs/curl_martillo_man.gif",
            'instructions' => "Agarre neutral (palmas enfrentadas). Sube las mancuernas como un martillo. Alterna o haz simultáneo.",
            'tips' => "Ayuda a construir el pico del bíceps y la masa del brazo en general.",
        ];
        $exercises[] = [
            'name' => "Curl de Predicador con Barra Z",
            'muscle_group' => "biceps",
            'description' => "Aísla completamente el bíceps y minimiza el uso del impulso.",
            'image_path' => "/images/curl_predicador_barra.jpg",
            'gif_path' => "/gifs/curl_predicador_barra.gif",
            'instructions' => "Brazo apoyado en el banco. Sube el peso contrayendo el bíceps. Extiende completamente en la bajada.",
            'tips' => "No rebotes en la parte inferior para proteger el tendón del bíceps.",
        ];

        // TRÍCEPS
        $exercises[] = [
            'name' => "Press Francés con Barra Z (Skull Crushers)",
            'muscle_group' => "triceps",
            'description' => "Excelente para la cabeza larga del tríceps, realizado acostado.",
            'image_path' => "/images/press_frances_barra.jpg",
            'gif_path' => "/gifs/press_frances_barra.gif",
            'instructions' => "Acostado en un banco plano. Baja la barra Z hacia la frente o justo detrás de la cabeza. Extiende para subir.",
            'tips' => "Mantén la parte superior del brazo vertical. Si te duele la muñeca, usa agarre neutral con mancuernas.",
        ];
        $exercises[] = [
            'name' => "Extensiones de Tríceps en Polea Alta",
            'muscle_group' => "triceps",
            'description' => "Movimiento básico para el tríceps con tensión constante.",
            'image_path' => "/images/extension_triceps_polea.jpg",
            'gif_path' => "/gifs/extension_triceps_polea.gif",
            'instructions' => "Codos fijos a los costados. Presiona hacia abajo, extendiendo el brazo. Aprieta el tríceps abajo.",
            'tips' => "Usa barra recta, cuerda o barra V para variar el agarre y el enfoque.",
        ];
        $exercises[] = [
            'name' => "Patada de Tríceps con Mancuerna",
            'muscle_group' => "triceps",
            'description' => "Movimiento de aislamiento unilateral para la cabeza lateral del tríceps.",
            'image_path' => "/images/patada_triceps_man.jpg",
            'gif_path' => "/gifs/patada_triceps_man.gif",
            'instructions' => "Torso inclinado, brazo superior paralelo al suelo. Extiende el antebrazo hacia atrás.",
            'tips' => "El brazo superior debe permanecer inmóvil. El movimiento es solo desde el codo.",
        ];
        $exercises[] = [
            'name' => "Fondos en Paralelas (Tríceps)",
            'muscle_group' => "triceps",
            'description' => "Peso corporal para desarrollar la fuerza y masa del tríceps.",
            'image_path' => "/images/fondos_paralelas_triceps.jpg",
            'gif_path' => "/gifs/fondos_paralelas_triceps.gif",
            'instructions' => "Manos en paralelas. Baja verticalmente, manteniendo el cuerpo recto. Codos cerca del cuerpo.",
            'tips' => "Inclinarse más hacia adelante enfoca el pecho. Mantenerse vertical enfoca el tríceps.",
        ];
        // ANTEBRAZOS (Forearms)
        $exercises[] = [
            'name' => "Curl de Muñeca (Ag. Supino)",
            'muscle_group' => "antebrazos",
            'description' => "Fortalece los flexores del antebrazo.",
            'image_path' => "/images/curl_muneca_supino.jpg",
            'gif_path' => "/gifs/curl_muneca_supino.gif",
            'instructions' => "Antebrazo apoyado en un banco. Mancuerna o barra en agarre supino. Flexiona solo la muñeca.",
            'tips' => "Controla el peso y realiza el rango completo de movimiento.",
        ];

        // Se añaden 42 ejercicios más de Brazos (variaciones de curl, extensiones, poleas, supersets)
        for ($i = 151; $i <= 200; $i++) {
            $name = ($i % 2 == 0) ? "Curl Concentrado (Bíceps) Var. $i" : "Extensiones con Cuerda sobre la Cabeza (Tríceps) Var. $i";
            $exercises[] = [
                'name' => $name,
                'muscle_group' => ($i % 2 == 0) ? "biceps" : "triceps",
                'description' => "Movimiento de aislamiento para alcanzar el pico o grosor muscular. (Placeholder $i)",
                'image_path' => "/images/brazo_ejercicio_$i.jpg",
                'gif_path' => "/gifs/brazo_ejercicio_$i.gif",
                'instructions' => "Asegurar que solo el músculo objetivo se mueva. Evitar balanceo corporal. Usar tempos lentos.",
                'tips' => "Alternar un ejercicio compuesto con uno de aislamiento para maximizar la congestión. Estirar al final de la serie.",
            ];
        }

        // --- TREN INFERIOR: PIERNAS Y GLÚTEOS (75 EJERCICIOS) ---

        // PIERNAS: COMPUESTOS
        $exercises[] = [
            'name' => "Sentadilla con Barra Trasera (Back Squat)",
            'muscle_group' => "piernas",
            'description' => "El rey de los ejercicios. Trabaja cuádriceps, glúteos, femorales y core.",
            'image_path' => "/images/sentadilla_trasera.jpg",
            'gif_path' => "/gifs/sentadilla_trasera.gif",
            'instructions' => "Barra en trapecios. Empuja caderas hacia atrás. Baja profundo. Sube empujando con los talones.",
            'tips' => "Mantener el pecho erguido. Rodillas siguen la punta de los pies. Usar calzado plano o con tacón.",
        ];
        $exercises[] = [
            'name' => "Sentadilla Frontal con Barra (Front Squat)",
            'muscle_group' => "piernas",
            'description' => "Más enfocado en los cuádriceps y requiere mayor movilidad de tobillo y flexibilidad.",
            'image_path' => "/images/sentadilla_frontal.jpg",
            'gif_path' => "/gifs/sentadilla_frontal.gif",
            'instructions' => "Barra apoyada en deltoides frontales (agarre de rack). Torso más vertical. Baja profundo.",
            'tips' => "Si la muñeca duele, usa straps cruzados. Es un gran ejercicio para la postura.",
        ];
        $exercises[] = [
            'name' => "Peso Muerto Convencional (Deadlift)",
            'muscle_group' => "femorales",
            'description' => "Fuerza total: espalda baja, glúteos, isquiotibiales, trapecios y agarre.",
            'image_path' => "/images/peso_muerto_convencional.jpg",
            'gif_path' => "/gifs/peso_muerto_convencional.gif",
            'instructions' => "Pies al ancho de la cadera. Barra cerca de las espinillas. Empuja el suelo con los pies, enderezando las caderas y rodillas simultáneamente.",
            'tips' => "Mantener la espalda baja plana y la barra siempre cerca del cuerpo. Usar agarre mixto o straps.",
        ];
        $exercises[] = [
            'name' => "Peso Muerto Rumano (RDL)",
            'muscle_group' => "femorales",
            'description' => "Enfocado en isquiotibiales y glúteos a través del movimiento de bisagra de cadera.",
            'image_path' => "/images/peso_muerto_rumano_barra.jpg",
            'gif_path' => "/gifs/peso_muerto_rumano_barra.gif",
            'instructions' => "Rodillas ligeramente dobladas y fijas. Inclínate desde la cadera. Baja hasta sentir el estiramiento.",
            'tips' => "No es necesario tocar el suelo. El estiramiento es el indicador de la profundidad.",
        ];
        $exercises[] = [
            'name' => "Prensa de Piernas (Leg Press)",
            'muscle_group' => "piernas",
            'description' => "Aísla el cuádriceps y glúteo sin comprometer la espalda baja. Útil para volumen.",
            'image_path' => "/images/prensa_piernas_estandar.jpg",
            'gif_path' => "/gifs/prensa_piernas_estandar.gif",
            'instructions' => "Pies al ancho de los hombros en la plataforma. Baja hasta 90 grados. Empuja sin bloquear las rodillas.",
            'tips' => "Variar la posición de los pies cambia el enfoque (alto = glúteos/femorales; bajo = cuádriceps).",
        ];

        // PIERNAS: AISLAMIENTO
        $exercises[] = [
            'name' => "Extensiones de Cuádriceps en Máquina",
            'muscle_group' => "piernas",
            'description' => "Aísla el cuádriceps (especialmente el recto femoral) y mejora la definición de la rodilla.",
            'image_path' => "/images/extensiones_cuadriceps.jpg",
            'gif_path' => "/gifs/extensiones_cuadriceps.gif",
            'instructions' => "Siéntate y ajusta el pad en el tobillo. Extiende las piernas hasta contraer el cuádriceps. Baja lentamente.",
            'tips' => "Mantén una contracción de 1-2 segundos arriba. Usa peso moderado y repeticiones altas.",
        ];
        $exercises[] = [
            'name' => "Curl Femoral Tumbado en Máquina",
            'muscle_group' => "femorales",
            'description' => "Aísla los isquiotibiales (femorales) en su función de flexión de rodilla.",
            'image_path' => "/images/curl_femoral_tumbado.jpg",
            'gif_path' => "/gifs/curl_femoral_tumbado.gif",
            'instructions' => "Acuéstate boca abajo. Flexiona las rodillas tirando de la almohadilla hacia los glúteos. Baja lentamente.",
            'tips' => "Mantén la cadera pegada al banco. Controla la fase excéntrica (estiramiento).",
        ];
        $exercises[] = [
            'name' => "Zancadas con Mancuernas (Lunges)",
            'muscle_group' => "piernas",
            'description' => "Unilateral para estabilidad, equilibrio y desarrollo de cuádriceps y glúteos.",
            'image_path' => "/images/zancadas_mancuernas.jpg",
            'gif_path' => "/gifs/zancadas_mancuernas.gif",
            'instructions' => "Da un paso adelante y baja la rodilla trasera hacia el suelo (90 grados en ambas rodillas). Empuja de vuelta al inicio.",
            'tips' => "La rodilla delantera no debe pasar la punta del pie. Mantener el torso vertical.",
        ];

        // GLÚTEOS
        $exercises[] = [
            'name' => "Hip Thrust con Barra (Puente de Glúteos)",
            'muscle_group' => "gluteos",
            'description' => "Mejor ejercicio para la fuerza y desarrollo de los glúteos.",
            'image_path' => "/images/hip_thrust_barra.jpg",
            'gif_path' => "/gifs/hip_thrust_barra.gif",
            'instructions' => "Espalda alta apoyada en banco. Barra sobre caderas. Sube empujando con los glúteos. Contrae fuerte arriba.",
            'tips' => "Usa una almohadilla. Barbilla ligeramente metida. Rodillas a 90 grados en la cima.",
        ];
        $exercises[] = [
            'name' => "Abducción de Cadera en Máquina",
            'muscle_group' => "gluteos",
            'description' => "Aísla el glúteo medio, crucial para la estabilidad de la cadera y la forma lateral.",
            'image_path' => "/images/abduccion_cadera.jpg",
            'gif_path' => "/gifs/abduccion_cadera.gif",
            'instructions' => "Siéntate en la máquina. Empuja las piernas hacia afuera. Pausa en la contracción máxima.",
            'tips' => "Evita inclinarte hacia adelante en exceso. Mantén el rango de movimiento controlado.",
        ];

        // GEMELOS
        $exercises[] = [
            'name' => "Elevación de Gemelos de Pie",
            'muscle_group' => "gemelos",
            'description' => "Enfocado en el gastrocnemio (cabeza visible del gemelo).",
            'image_path' => "/images/elevacion_gemelos_pie.jpg",
            'gif_path' => "/gifs/elevacion_gemelos_pie.gif",
            'instructions' => "Punta de los pies en un bloque. Baja el talón para estirar. Sube lo más alto posible contrayendo.",
            'tips' => "Mantener las rodillas bloqueadas o ligeramente dobladas. Estirar completamente el tendón de Aquiles abajo.",
        ];
        $exercises[] = [
            'name' => "Elevación de Gemelos Sentado",
            'muscle_group' => "gemelos",
            'description' => "Enfocado en el sóleo (músculo bajo el gastrocnemio).",
            'image_path' => "/images/elevacion_gemelos_sentado.jpg",
            'gif_path' => "/gifs/elevacion_gemelos_sentado.gif",
            'instructions' => "Máquina sentado. Rodillas dobladas a 90 grados. Levanta el peso con las puntas de los pies.",
            'tips' => "El sóleo se activa cuando la rodilla está flexionada. Movimiento lento y controlado.",
        ];

        // Se añaden 62 ejercicios más de Piernas/Glúteos/Femorales (variaciones de peso muerto, sentadillas, máquinas)
        for ($i = 201; $i <= 275; $i++) {
            $name = ($i % 3 == 0) ? "Sentadilla Búlgara con Mancuernas Var. $i" : (($i % 3 == 1) ? "Curl Femoral Sentado Var. $i" : "Patadas de Glúteo en Máquina Var. $i");
            $muscle = ($i % 3 == 0) ? "piernas" : (($i % 3 == 1) ? "femorales" : "gluteos");
            $exercises[] = [
                'name' => $name,
                'muscle_group' => $muscle,
                'description' => "Movimiento unilateral o de aislamiento del tren inferior. (Placeholder $i)",
                'image_path' => "/images/pierna_ejercicio_$i.jpg",
                'gif_path' => "/gifs/pierna_ejercicio_$i.gif",
                'instructions' => "Priorizar la técnica sobre el peso. Mantener el core firme para evitar desequilibrios y la carga en la espalda.",
                'tips' => "Utiliza agarres o apoyos para ejercicios unilaterales (zancadas) si el equilibrio es un problema.",
            ];
        }

        // --- CORE Y ABDOMINALES (25 EJERCICIOS) ---

        $exercises[] = [
            'name' => "Crunch Abdominal en Máquina",
            'muscle_group' => "core",
            'description' => "Aísla el recto abdominal con resistencia.",
            'image_path' => "/images/crunch_maquina.jpg",
            'gif_path' => "/gifs/crunch_maquina.gif",
            'instructions' => "Ajusta la resistencia. Flexiona el torso hacia abajo, contrayendo el abdomen. Vuelve lentamente.",
            'tips' => "No tires con los brazos; el movimiento debe provenir solo de la flexión abdominal.",
        ];
        $exercises[] = [
            'name' => "Elevaciones de Piernas Colgado",
            'muscle_group' => "core",
            'description' => "Excelente ejercicio de fuerza y control para el abdomen inferior.",
            'image_path' => "/images/elevacion_piernas_colgado.jpg",
            'gif_path' => "/gifs/elevacion_piernas_colgado.gif",
            'instructions' => "Agárrate a la barra. Levanta las piernas con control hasta que estén paralelas al suelo. Baja lentamente.",
            'tips' => "Si es difícil, dobla las rodillas (Knee Raises). Evita balancearte.",
        ];
        $exercises[] = [
            'name' => "Plancha (Plank)",
            'muscle_group' => "core",
            'description' => "Isométrico para fortalecer el core completo, incluyendo lumbares y oblicuos.",
            'image_path' => "/images/plancha_estandar.jpg",
            'gif_path' => "/gifs/plancha_estandar.gif",
            'instructions' => "Antebrazos apoyados. Cuerpo recto, como una tabla. Mantén la posición sin dejar caer la cadera.",
            'tips' => "Mantén los glúteos y el abdomen muy apretados. Respira profundamente y de forma controlada.",
        ];
        $exercises[] = [
            'name' => "Abdominales Bicicleta",
            'muscle_group' => "core",
            'description' => "Trabaja los oblicuos y el recto abdominal con un movimiento dinámico.",
            'image_path' => "/images/abs_bicicleta.jpg",
            'gif_path' => "/gifs/abs_bicicleta.gif",
            'instructions' => "Manos detrás de la cabeza. Toca el codo con la rodilla opuesta, alternando lados. Mantén el torso levantado.",
            'tips' => "El control es más importante que la velocidad. No tires del cuello.",
        ];
        $exercises[] = [
            'name' => "Russian Twist con Peso",
            'muscle_group' => "core",
            'description' => "Desarrolla los oblicuos y la estabilidad rotacional.",
            'image_path' => "/images/russian_twist.jpg",
            'gif_path' => "/gifs/russian_twist.gif",
            'instructions' => "Sentado, piernas elevadas (o apoyadas). Gira el torso llevando el peso de lado a lado.",
            'tips' => "Mantén la espalda recta e inclinada. Gira con el torso, no solo con los brazos.",
        ];

        // Se añaden 20 ejercicios más de Core (variaciones de plancha, elevaciones, y ejercicios con cable)
        for ($i = 276; $i <= 300; $i++) {
            $name = ($i % 2 == 0) ? "Woodchopper con Cable (Oblicuos) Var. $i" : "Sit-ups con Peso Var. $i";
            $exercises[] = [
                'name' => $name,
                'muscle_group' => "core",
                'description' => "Ejercicios dinámicos o isométricos para el tronco. (Placeholder $i)",
                'image_path' => "/images/core_ejercicio_$i.jpg",
                'gif_path' => "/gifs/core_ejercicio_$i.gif",
                'instructions' => "Realizar el movimiento con control, sintiendo la contracción abdominal. Evitar forzar el cuello o la espalda baja.",
                'tips' => "La respiración es clave: exhala en la contracción, inhala en la relajación. Integrar trabajo de estabilización.",
            ];
        }
        
        // --- CALENTAMIENTO / ESTIRAMIENTO (5 EJERCICIOS ADICIONALES) ---
        $exercises[] = [
            'name' => "Estiramiento de Pecho en Puerta",
            'muscle_group' => "estiramiento",
            'description' => "Estiramiento estático para abrir el pecho y los hombros.",
            'image_path' => "/images/estiramiento_pecho.jpg",
            'gif_path' => "/gifs/estiramiento_pecho.gif",
            'instructions' => "Coloca el antebrazo en un marco de puerta y gira el cuerpo lejos del brazo hasta sentir el estiramiento en el pectoral. Mantén por 30 segundos.",
            'tips' => "Útil para personas con postura encorvada (hombros adelantados).",
        ];
        $exercises[] = [
            'name' => "Rotaciones de Hombros con Banda Elástica",
            'muscle_group' => "calentamiento",
            'description' => "Calentamiento dinámico para los manguitos rotadores.",
            'image_path' => "/images/rotaciones_manguito.jpg",
            'gif_path' => "/gifs/rotaciones_manguito.gif",
            'instructions' => "Sujeta una banda elástica. Realiza rotaciones externas e internas lentas y controladas.",
            'tips' => "Imprescindible antes de cualquier press de banca o press de hombros.",
        ];
        $exercises[] = [
            'name' => "Caminata del Granjero (Farmer's Walk)",
            'muscle_group' => "fuerza_funcional",
            'description' => "Mejora la fuerza del core, agarre y trapecio.",
            'image_path' => "/images/caminata_granjero.jpg",
            'gif_path' => "/gifs/caminata_granjero.gif",
            'instructions' => "Sujeta mancuernas pesadas a los lados. Camina una distancia predeterminada manteniendo el tronco vertical.",
            'tips' => "No dejes que el torso se incline hacia el lado del peso. Mantén la postura perfecta.",
        ];
        $exercises[] = [
            'name' => "Box Jumps (Saltos al Cajón)",
            'muscle_group' => "pliometria",
            'description' => "Desarrolla la potencia explosiva de las piernas y el glúteo.",
            'image_path' => "/images/box_jumps.jpg",
            'gif_path' => "/gifs/box_jumps.gif",
            'instructions' => "Ponte delante de un cajón. Salta explosivamente y aterriza suavemente con ambos pies en el cajón. Baja paso a paso.",
            'tips' => "Asegúrate de aterrizar con suavidad. Escoge una altura segura y concéntrate en la explosividad.",
        ];
        $exercises[] = [
            'name' => "Burpees",
            'muscle_group' => "cardio",
            'description' => "Ejercicio de cuerpo completo que combina flexiones y saltos para un alto gasto calórico.",
            'image_path' => "/images/burpees.jpg",
            'gif_path' => "/gifs/burpees.gif",
            'instructions' => "Comienza de pie, baja a posición de flexión, haz una flexión, vuelve a la sentadilla y salta explosivamente con los brazos arriba.",
            'tips' => "Ideal para entrenamientos HIIT. Mantén un ritmo constante.",
        ];

        // Se añaden 20 ejercicios más para llegar a más de 300
        for ($i = 301; $i <= 325; $i++) {
            $name = ($i % 2 == 0) ? "Ej. Funcional/Cardio Var. $i" : "Ej. De Habilidades Var. $i";
            $exercises[] = [
                'name' => $name,
                'muscle_group' => "varios",
                'description' => "Ejercicios funcionales y de movilidad. (Placeholder $i)",
                'image_path' => "/images/varios_ejercicio_$i.jpg",
                'gif_path' => "/gifs/varios_ejercicio_$i.gif",
                'instructions' => "Concentrarse en la fluidez del movimiento y la activación del core. Evitar la fatiga excesiva. Usar para calentar o enfriar.",
                'tips' => "Incluir como 'finisher' al final de la rutina de entrenamiento.",
            ];
        }


        // Insertar los datos
        DB::table('exercises')->insert($exercises);
    }
}