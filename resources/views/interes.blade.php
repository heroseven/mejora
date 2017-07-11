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
               Para iniciar seleccione sus preferencias
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">
                 <form action="../../intereses" method="POST">
                  <label>Seleccione el rubro de su empresa</label>
                       <select class="form-control" name="rubro">
                           <option value="Agropecuario">Agropecuario</option>
                           <option value="Manufactura">Manufactura</option>
                           <option value="Pesca">Pesca</option>
                           <option value="Minero">Minero</option>
                           <option value="Construcción">Construcción</option>
                           <option value="Transporte">Transporte</option>
                           <option value="Financiero">Financiero</option>
                           <option value="Servicios empresariales">Servicios empresariales</option>
                           <option value="Enseñanza">Enseñanza</option>
                           <option value="Servicios">Servicios</option>
                           <option value="Salud">Salud</option>
                       </select>
                      </br> 
                      <label>Seleccione su interés en alguna temática</label></br>
                     
                      <input type="checkbox" name="interes[1]" value="financiacion">Financiación<br>
                      <input type="checkbox" name="interes[2]" value="marketing">Marketing<br>
                      <input type="checkbox" name="interes[3]" value="tecnologia">Tecnología para mejora de procesos<br>
                      <input type="checkbox" name="interes[4]" value="calidad">Calidad<br>
                      <input type="checkbox" name="interes[5]" value="exportaciones">Exportaciones<br>
                      <input type="checkbox" name="interes[6]" value="formalización">Formalización<br>
                      <input type="checkbox" name="interes[7]" value="atencion al cliente">Atención al cliente<br>
                      <input type="hidden" name="id" value="{{$id}}"/>
                      </br>
                      <input class="form-control" type="submit" value="Enviar"/>
                         
                </form>
                    <!-- Table Body -->
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>

@endsection