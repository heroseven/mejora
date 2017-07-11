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
                Lista de artículos para Usuario {{$usuario}}
                - <a href="../perfil_usuario/{{$usuario}}">Ver recomendaciones </a>
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Identificación</th>
                        <th>Artículo</th>
                        <th>Ver más</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                 <td class="table-text2">
                                    <div>{{ $task->identificacion }}</div>
                                 </td>
                                <!-- Task Name -->
                                <td class="table-text">
                                     <div>{{ $task->articulo->titulo }}</div>
                                     <div><a href="../like/{{$usuario}}/{{ $task->identificacion }}">Me gusta</a>  -  <a href="../dislike/{{$usuario}}/{{ $task->id }}">No me gusta</a></div>
                                </td>
                               <!-- <td class="table-text">
                                     <div>{{ $task->articulo->descripcion }}</div>
                                </td>-->
                                 <td class="table-text">
                                     
                                     <div><a href="../detalle/{{$usuario}}/{{ $task->identificacion }}">Ver más</a> </div>
                                </td>
                               
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection