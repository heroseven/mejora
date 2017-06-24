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
    @if (count($tasks) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Recomendaciones
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Identificación</th>
                        <th>Artículo</th>
                        <th>Descripción</th>
                        <th>Predicción</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        
                        @foreach ($tasks as $task)
                           
                                
                                @if($articulos_calificados[0]==$task->articulo->id or $articulos_calificados[1]==$task->articulo->id)
                                     <tr style="color:red">
                                 <td class="table-text2">
                                    <div>{{ $task->articulo->id }}</div>
                                 </td>
                                <!-- Task Name -->
                                <td class="table-text">
                                     <div>{{ $task->articulo->titulo }}</div>
                                     <!--<div><a href="like/{{ $task->identificacion }}">Me gusta</a>  -  <a href="dislike/{{ $task->identificacion }}">No me gusta</a></div>-->
                                </td>
                                 <td class="table-text">
                                     <div>{{ $task->articulo->descripcion }}</div>
                                </td>

                                <td class="table-text">
                                     <div>{{ $task->prediccion }}%</div>
                                </td>
                            </tr>
                                
                                @else
                                     <tr>
                                 <td class="table-text2">
                                    <div>{{ $task->articulo->id }}</div>
                                 </td>
                                <!-- Task Name -->
                                <td class="table-text">
                                     <div>{{ $task->articulo->titulo }}</div>
                                     <!--<div><a href="like/{{ $task->identificacion }}">Me gusta</a>  -  <a href="dislike/{{ $task->identificacion }}">No me gusta</a></div>-->
                                </td>
                                 <td class="table-text">
                                     <div>{{ $task->articulo->descripcion }}</div>
                                </td>

                                <td class="table-text">
                                     <div>{{ $task->prediccion }}%</div>
                                </td>
                            </tr>
                                @endif
                                
                  
                           
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection