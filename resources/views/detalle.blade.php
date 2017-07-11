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
                Lista de artículos para Usuario {{$usuario}}
                Ver recomendaciones <a href="../../perfil_usuario/{{$usuario}}">Aquí</a>
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Item</th>
                       <th>Valor</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                             <tr>
                                 <td class="table-text2">
                                    <div>Calificación</div>
                                 </td>
                                 <td class="table-text">
                                    <div><a href="../../like/{{$usuario}}/{{$documento->id }}">Me gusta</a>  -  <a href="../../dislike/{{$usuario}}/{{$documento->id }}">No me gusta</a></div>
                                </td>
                               
                               
                            </tr>
                            <tr>
                                <td class="table-text2">
                                    <div>Identificación</div>
                                 </td>
                                 <td class="table-text2">
                                    <div>{{ $documento->id }}</div>
                                 </td>
                               
                               
                            </tr>
                            
                            <tr>
                                <td class="table-text2">
                                    <div>Título</div>
                                </td>
                                <td class="table-text">
                                     <div>{{ $documento->titulo }}</div>
                                    
                                </td>
                               
                               
                               
                            </tr>
                             <tr>
                                 <td class="table-text2">
                                    <div>Contenido</div>
                                 </td>
                                 <td class="table-text">
                                     <div>{{ $documento->contenido }}</div>
                                </td>
                                 
                               
                            </tr>
                            
                            
                            
                            
                            
                        
                    </tbody>
                </table>
            </div>
        </div>

@endsection