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
                           <option value="f1">Agropecuario</option>
                           <option value="f2">Manufactura</option>
                           <option value="f3">Pesca</option>
                           <option value="f4">Minero</option>
                           <option value="f5">Construcción</option>
                           <option value="f6">Transporte</option>
                           <option value="f7">Financiero</option>
                           <option value="f8">Servicios empresariales</option>
                           <option value="f9">Enseñanza</option>
                           <option value="f10">Servicios</option>
                           <option value="f11">Salud</option>
                       </select>
                      </br> 
                      <label>Seleccione su interés en alguna temática</label></br>
                     
                      <input type="checkbox" name="interes[1]" value="f12">Financiación<br>
                      <input type="checkbox" name="interes[2]" value="f13">Marketing<br>
                      <input type="checkbox" name="interes[3]" value="f14">Tecnología para mejora de procesos<br>
                      <input type="checkbox" name="interes[4]" value="f15">Calidad<br>
                      <input type="checkbox" name="interes[5]" value="f16">Exportaciones<br>
                      <input type="checkbox" name="interes[6]" value="f17">Formalización<br>
                      <input type="checkbox" name="interes[7]" value="f18">Atención al cliente<br>
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