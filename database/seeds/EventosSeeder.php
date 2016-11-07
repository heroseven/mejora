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
            'titulo' => 'sap mm - gestión logística - lima',
            'contenido' => 'es un módulo de sap que logra amplio control en las operaciones logísticas, de almacenes y cadena de almacenes en diferentes rubros empresariales lo cual permite a los negocios optimizar sus procesos y maximizar sus recursos. el curso sap logística proporciona las habilidades necesarias para tener éxito en operaciones logísticas y de almacenes en diversos procesos de negocios sobre la plataforma erp sap',
        ]);

        DB::table('documento')->insert([
            'titulo' => 'bpmn con bizagi nextech',
            'contenido' => 'bpmn con bizagi nextech modelamiento de procesos con bpmn durante este curso taller usted aprenderá de forma práctica el estándar bpmn 2.0 el cual le permitirá plasmar los procesos de negocio actuales y deseados en forma gráfica facilitando el entendimiento de las colaboraciones y transacciones de negocio entre grupos de trabajo, áreas funcionales y organizaciones.',
        ]);

        DB::table('documento')->insert([
            'titulo' => 'taller práctico de redacción y ortografía',
            'contenido'=> 'una mala ortografía las ventas podrían caer hasta un 50 % empresario de internet un taller diferente, 100 % práctico, en el que veremos los errores de tus propios textos para que no vuelvas a cometerlos. dictado por: jose enrique escardó steck, director de investigación del diario altavoz y profesor de redacción periodística en el instituto isil',
        ]);
        DB::table('documento')->insert([
            'titulo' => 'curso de adm. sueldos y salarios',
            'contenido'=> 'brindar herramientas modernas de administración salarial para fortalecer el manejo del personal de la empresa administración de sueldos y salarios análisis de puestos evaluación de puestos diseño del manual de evaluación categorización de puestos diseño de estructura salarial compensación variable',
        ]);
         DB::table('documento')->insert([
            'titulo' => 'itil foundation nextech',
            'contenido'=> 'itil foundation nextech itil foundation es idóneo para que mejoren sus habilidades laborales las personas involucradas en las siguientes áreas escritorio de servicios gestión de incidentes gestión de problemas gestión de cambios gestión de configuraciones y activos de servicio gestión de disponibilidad gestión de capacidad gestión de seguridad de información gestión de nivel de servicio y en general todas las áreas administrativas de ti',
        ]);
        
        DB::table('users')->insert([
            'email' => 'raul@diblasio.com',
            'password' => 'diblasio',
            'name'=> 'Raul Diblasio',
            'estado'=>'1'
        ]);

    }
}
