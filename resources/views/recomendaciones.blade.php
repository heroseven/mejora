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
                Recomendaciones - <a href="usuario/{{$usuario}}">Seguir calificando artículos</a>
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
                        
                       
                           
                                @foreach($articulos as $articulo)
                               
                               
                                         <tr>
                                     <td class="table-text2">
                                        <div>{{ $articulo->articulo->id }}</div>
                                     </td>
                                    <!-- Task Name -->
                                    <td class="table-text">
                                         <div>{{ $articulo->articulo->titulo }}</div>
                                     </td>
                                     <td class="table-text">
                                         <div>{{ $articulo->articulo->descripcion }}</div>
                                    </td>
    
                                    <td class="table-text">
                                         <div>{{ $articulo->prediccion }}%</div>
                                    </td>
                                </tr>
                                
                              
                                
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection