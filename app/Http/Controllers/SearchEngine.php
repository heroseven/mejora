<?php

namespace App\Http\Controllers;
use App\Models\Preferencias;
use App\Models\Documento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class SearchEngine extends Controller
{

    public function indexar(){



        $documentos=Documento::all();
        $documentos=$documentos->pluck('titulo');
        
        //cada posición representa un termino registrado en el diccionario
        $dictionary = array();
        $docCount = array();

        //se recorren todos los docuemntos
        foreach($documentos as $docID => $doc) {
            
            //arreglo donde cada posición representa un termino 
            $terms = explode(' ', $doc);
            // return $terms;
           
           //cada posicion representa la cantidad de terminos que tiene cada doc
            $docCount[$docID] = count($terms);
    
            //se recorren los terminos que posee uno de los documentos
            foreach($terms as $term) {

                //si no cuenta con el termino en el diccionario se crea estructura
                if(!isset($dictionary[$term])) {
                    $dictionary[$term] = array('df' => 0, 'postings' => array());
                }

                //si no hay nada definido en la la posicion docid de posting
                if(!isset($dictionary[$term]['postings'][$docID])) {

                    //cantidad de veces que se repite un termino en todos los documentos
                    $dictionary[$term]['df']++;

                    //se crea la estructura para poder sumar la cantidad de terminos iguales en un doc
                    $dictionary[$term]['postings'][$docID] = array('tf' => 0);
                }

                    //cantidad de veces que aparece un termnino en un documento
                    $dictionary[$term]['postings'][$docID]['tf']++;
            }
        }
        //al final se retorna cuantos terminos hay para cada docuemnto.
        // y el diccionario con la
        return array('docCount' => $docCount, 'dictionary' => $dictionary);
    }
        
    public function puntuar_segun_busqueda($busqueda){
        
        
        $diccionario=$this->indexar();
        
        //puntuaciones del termino buscado
        $puntuaciones=$diccionario['dictionary'][$busqueda];
        //return $puntuaciones;
        $ocurrencia_global=$puntuaciones['df'];
 

        $numero_de_documentos=count($diccionario['docCount']);

        //para guardar puntuación
        
        $formula=array();
        
        foreach ($puntuaciones['postings'] as $docID => $puntuacion_por_documento) {
            
            //docID +1 
            //puntuacion local + puntuacion global.
            $formula[$docID]= $puntuacion_por_documento['tf']*log($numero_de_documentos/$ocurrencia_global);
            
    
        }
        
         arsort($formula); // high to low
        
        var_dump($formula);
        
        // // return $formula;
        // return var_dump($formula);
        //todo es perfecto pero no es tan preciso porque no considera la loingitud del texto
        //la propouesta es condierar cada docuemnto no como un punto en el espacio sino como un vector
        //calcular el punto del vector es facil, multiplicar cada posicion es el resultado.
        //se puede comparar un documento con otro.
        /*podemos hallar el peso del docuemnto en terminos de x(termino1) e y(termino2)
        si son muchos terminos en una pagina hay que noramlizar dividiendo entre la cantidad de terminos
        no sería eficiente comparar todos los docuemntos con la busqueda
        
        
        */
        
        
    }    
    public function puntuacion_mayor(Request $request){
   
        $index = $this->indexar();
        $matchDocs=array();
        //corregir error de Undefined offset: 3
        $docCount = count($index['docCount']);
        for($i=0;$i<$docCount;$i++){
            $matchDocs[$i]=0;
        }
        
        
        // $busqueda="ejemplo un es hola las";
        $busqueda=$request->input('terminos');
        $query = explode(" ", $busqueda);
        
        foreach($query as $qterm) {
          
            if(isset($index['dictionary'][$qterm])){
                    
                    $entry = $index['dictionary'][$qterm];
          
                 
                    foreach($entry['postings'] as $docID => $posting) {
                        
                    //si añadimos 1 mas tanto a $docCount y df tendremos mejores valores con pocos elementos.
                   
                    //este arreglo aumenta de tamaño miemtras más contenido tenga
                    //si se encuentra una palabra x en dos articulos, el segundo chanca al primero.
                    //no puede ser multiplicacion porque si fuera cero caga todo.
                    $matchDocs[$docID] = $matchDocs[$docID]+$posting['tf'] * log($docCount / $entry['df'], 10);
                    }
            }

             $putuacion[$qterm]=$matchDocs;
            
        }
        
        // normalizar segun el tamaño del contenido.
        foreach($matchDocs as $docID => $score) {
            $matchDocs[$docID] = $score/$index['docCount'][$docID];
        }
        
        arsort($matchDocs); // odenar de mayor a menor
        var_dump($matchDocs);
        $posicion=key($matchDocs)+1;
        echo Documento::find($posicion)->contenido;
        return 'El documento más asociado es el '.(key($matchDocs)+1); 
        
        var_dump($matchDocs);
        
    
    }
    public function puntuacion_mayor_un_termino($termino){
   
        $index = $this->indexar();
        $matchDocs=array();
        //corregir error de Undefined offset: 3
        $docCount = count($index['docCount']);
        for($i=0;$i<$docCount;$i++){
            $matchDocs[$i]=0;
        }
    
        $qterm = $termino;
        
       
          
            if(isset($index['dictionary'][$qterm])){
                    
                    $entry = $index['dictionary'][$qterm];
          
                 
                    foreach($entry['postings'] as $docID => $posting) {
                        
                    //si añadimos 1 mas tanto a $docCount y df tendremos mejores valores con pocos elementos.
                   
                    //este arreglo aumenta de tamaño miemtras más contenido tenga
                    //si se encuentra una palabra x en dos articulos, el segundo chanca al primero.
                    //no puede ser multiplicacion porque si fuera cero caga todo.
                    $matchDocs[$docID] = $matchDocs[$docID]+$posting['tf'] * log($docCount / $entry['df'], 10);
                    }
            }


        
        // normalizar segun el tamaño del contenido.
        foreach($matchDocs as $docID => $score) {
            $matchDocs[$docID] = $score/$index['docCount'][$docID];
        }
        
        arsort($matchDocs); // odenar de mayor a menor
        // var_dump($matchDocs);
        $posicion=key($matchDocs)+1;
        // echo Documento::find($posicion)->contenido;
        // return 'El documento más asociado es el '.(key($matchDocs)+1); 
        
        return $matchDocs;
        
    
    }
    
    public function vector_caracteristico(){
  
        // $guardar= Preferencias::where('identificacion',132)->first();
        //         //   return $guardar->first();
        // $guardar->total_atributos=3;
        // $guardar->save();
                
        // return $guardar;
        $base=['factor1','factor2','factor3','factor4'];
        $factores=['exportaciones','tratado','fuerzas','paz','bancos'];
        foreach ($factores as $idFactor =>$factor) {
         
           $puntuaciones_por_documento_factor = $this->puntuacion_mayor_un_termino($factor);
            // return $puntuaciones_por_documento_factor;
            
           
             foreach ($puntuaciones_por_documento_factor as $idTitulo =>$valor) {
                    $idTitulo=intval($idTitulo)+1;
                    
                   
                    $guardar= Preferencias::where('identificacion',$idTitulo)->first();
                    //return $guardar->first();
                
                    if($valor>=0.04){
                        if($guardar!=null){
                           
                            if($factor=='exportaciones'){
                                $guardar->factor1=1;
                                $guardar->save();
                            }elseif($factor=='tratado'){
                                // return 'ok';
                                $guardar->factor2=1;
                                $guardar->save();
                            }elseif($factor=='fuerzas'){
                                // return 'ok';
                                $guardar->factor3=1;
                                $guardar->save();
                            }elseif($factor=='paz'){
                                // return 'ok';
                                $guardar->factor4=1;
                                $guardar->save();
                            }elseif($factor=='bancos'){
                                // return 'ok';
                                $guardar->factor5=1;
                                $guardar->save();
                            }
                           
                          
                            
                           
                        }else{
                         
                             $guardar = Preferencias::create(array('identificacion'=>$idTitulo));
                             
                            if($factor=='exportaciones'){
                                // return 'ok';
                                $guardar->factor1=1;
                                $guardar->save();
                            }elseif($factor=='tratado'){
                                // return 'ok';
                                $guardar->factor2=1;
                                $guardar->save();
                            }elseif($factor=='fuerzas'){
                                // return 'ok';
                                $guardar->factor3=1;
                                $guardar->save();
                            }elseif($factor=='paz'){
                                // return 'ok';
                                $guardar->factor4=1;
                                $guardar->save();
                            }elseif($factor=='bancos'){
                                // return 'ok';
                                $guardar->factor5=1;
                                $guardar->save();
                            }
                         
                        }
                    }else{
                        
                        
                          if($guardar!=null){
                           
                            if($factor=='exportaciones'){
                                $guardar->factor1=0;
                                $guardar->save();
                            }elseif($factor=='tratado'){
                                // return 'ok';
                                $guardar->factor2=0;
                                $guardar->save();
                            }elseif($factor=='fuerzas'){
                                // return 'ok';
                                $guardar->factor3=0;
                                $guardar->save();
                            }elseif($factor=='paz'){
                                // return 'ok';
                                $guardar->factor4=0;
                                $guardar->save();
                            }elseif($factor=='bancos'){
                                // return 'ok';
                                $guardar->factor5=0;
                                $guardar->save();
                            }
                           
                          
                            
                           
                        }else{
                           
                             $guardar = Preferencias::create(array('identificacion'=>$idTitulo));
                             
                            if($factor=='exportaciones'){
                                // return 'ok';
                                $guardar->factor1=0;
                                $guardar->save();
                            }elseif($factor=='tratado'){
                                // return 'ok';
                                $guardar->factor2=0;
                                $guardar->save();
                            }elseif($factor=='fuerzas'){
                                // return 'ok';
                                $guardar->factor3=0;
                                $guardar->save();
                            }elseif($factor=='paz'){
                                // return 'ok';
                                $guardar->factor4=0;
                                $guardar->save();
                            }elseif($factor=='bancos'){
                                // return 'ok';
                                $guardar->factor5=0;
                                $guardar->save();
                            }
                         
                        }
                        
                    }
                    
                    // $guardar = Preferencias::where('identificacion',$puntuacionId);
                    // $guardar->factor."".$idFactor=$valor;
                    // $guardar->save();
                    
                    // return $puntuacionId." ".$valor;
             }
       
             /*
             por un tema de optimizcion vamos a buscar los terminos en todos los 
             documentos y vamos a almacenar los resultados de manera vertical
             */
             
             
             
        }
    }
    
    
    public function transformación_matriz(){
        
        
        $sum=0;
        $articulos=Preferencias::all();
        foreach ($articulos as $articulo) {
        
            foreach ($articulo as $factores) {
                
                $articulo=$articulo->first();
                
                foreach($factores as $factor){
                    if($factor==1){
                        $sum=$sum+1;
                    }
                    
                  
                }
                foreach($factores as $factor){
                   
                    
                  
                }
                
            }
                
        }
    }
    
    
    public function buscador(){
        return View::make('buscador');
    }
}
