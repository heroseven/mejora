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
            <a href="">1. Limpiar bd</a></br>
            <a href="webhose">2. Poblamos la base de datos con la colección de articulos de prueba</a></br>
            <a href="elasticsearch">3. Elasticsearch, cambiar nombre de indice (3 lugares)</a></br>
            <a href="vector_caracteristico2">4. Crear vector caracteristico (Solo permite crear el diccionario una vez)</a></br>
            <a href="">5. Calificar artículos</a></br>
            <ul>
            @for($i=1; $i<=11;$i++)
                <a href="rubro/usuario/{{$i}}"> - Usuario {{$i}}</a></br>
            @endfor
             </ul>
             <a href="mape/1">6. Obtener MAPE para usuario </a></br>
            <ul>
            <!--@foreach ($articulos_con_valor as $articulo)-->
            <!--    <li>{{$articulo->id}}</li>-->
            <!--@endforeach-->
            
            </ul>
            </div>
        </div>
   
@endsection