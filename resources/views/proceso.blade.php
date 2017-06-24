@extends('layouts.app')

@section('content')

<style>
    .table-text{
        width:200px!important;
    }
    .table-text2{
        width:100px!important;
    }
</style>
    <!-- Create Task Form... -->

    <!-- Current Tasks -->
   
        <div class="panel panel-default">
            <div class="panel-heading">
                Proceso
            </div>
            <div class="panel-body">
            <a href="">0. Mostrar el analisis de contenido, diccionario</a></br>
            <a href="json">1. Limpiar bd</a></br>
            <a href="json">2. Poblamos la base de datos con la colección de articulos de prueba</a></br>
            <a href="vector_caracteristico">3. Crear vector caracteristico</a></br>
            <a href="normalizacion_fila">4. Normalización fila</a></br>
            <a href="usuario/1">5. Calificar artículos</a></br>
            <a href="perfil_usuario">6. Crear vector prototipo</a></br>
            <a href="columna">7. Calcular el vector de DF y predicción</a></br>
            <a href="recomendaciones">8. Ver recomendaciones</a></br>
            </div>
        </div>
   
@endsection