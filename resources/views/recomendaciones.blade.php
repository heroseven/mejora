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
                        <th>Más información</th>
                        <th>Predicción</th>
                        <th>Satisfacción</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        <form action="encuesta" method="POST">
                               <input type="submit" value="Enviar encuesta"/>
                                @foreach($articulos as $articulo)
                               
                                         <tr>
                                     <td class="table-text2">
                                        <div>{{ $articulo->articulo->id }}</div>
                                        <input type="hidden" name="id[{{ $articulo->id }}]" value="{{ $articulo->id }}"/>
                                     </td>
                                    <!-- Task Name -->
                                    <td class="table-text">
                                         <div>{{ $articulo->articulo->titulo }}</div>
                                     </td>
                                     
                                     <td class="table-text">
                                         <div><a href="detalle/{{$usuario}}/{{ $articulo->articulo->id }}">Ver más</a> </div>
                                     </td>
                                    <!-- <td class="table-text">-->
                                    <!--     <div>{{ $articulo->articulo->descripcion }}</div>-->
                                    <!--</td>-->
    
                                    <td class="table-text">
                                         <div>{{ $articulo->prediccion*100 }}%</div>
                                    </td>
                                     <td class="table-text">
                                         Satisfacción
                                         <div>
                                             <select name="puntuacion[{{ $articulo->id }}]">
                                                  <option value="">Seleccionar una opción</option>
                                                 <option value="0">0%</option>
                                                 <option value="20">20%</option>
                                                 <option value="40">40%</option>   
                                                 <option value="60">60%</option>   
                                                 <option value="80">80%</option>  
                                                 <option value="100">100%</option>   
                                             </select>
                                         </div>
                                    </td>
                                </tr>
                                
                                @endforeach
                        </form>
                       
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection