@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Emparejar contenidos relacionados</div>

                <div class="panel-body">
                    <form method="post" class="form-horizontal" role="form" action="buscador2">
                        Terminos a buscar: <input class="form-control" type="text" name="terminos"/>
                       
                        
                         <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i>Buscar
                                </button>
                        
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
