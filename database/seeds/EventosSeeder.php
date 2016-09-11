<?php

use Illuminate\Database\Seeder;

class EventosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('documento')->insert([
            'titulo' => 'SAP MM - Gestión Logística - Lima',
            'contenido' => 'es un módulo de SAP que logra amplio control en las operaciones logísticas, de almacenes y cadena de almacenes en diferentes rubros empresariales lo cual permite a los negocios optimizar sus procesos y maximizar sus recursos. El curso SAP Logística proporciona las habilidades necesarias para tener éxito en operaciones logísticas y de almacenes en diversos procesos de negocios sobre la plataforma ERP SAP',
        ]);

        DB::table('documento')->insert([
            'titulo' => 'BPMN con Bizagi Nextech',
            'contenido' => 'MODELAMIENTO DE PROCESOS CON BPMN Durante este curso taller usted aprenderá de forma práctica el estándar BPMN 2.0 el cual le permitirá plasmar los procesos de negocio actuales y deseados en forma gráfica, facilitando el entendimiento de las colaboraciones y transacciones de negocio entre grupos de trabajo, áreas funcionales y organizaciones.',
        ]);

        DB::table('documento')->insert([
            'titulo' => 'Taller Práctico de Redacción y Ortografía',
            'contenido'=> 'una mala ortografía, las ventas podrían caer hasta un 50 % empresario de internet Un taller diferente, 100 % práctico, en el que veremos los errores de tus propios textos para que no vuelvas a cometerlos. Dictado por: Jose Enrique Escardó Steck, director de Investigación del diario Altavoz y profesor de Redacción Periodística en el Instituto ISIL',
        ]);

    }
}
