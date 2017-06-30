<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

/*sudo /etc/init.d/elasticsearch start

curl http://localhost:9200
*/

use App\Models\Documento;
use App\Models\Interes;
use App\Models\Perfil;
use App\Models\Df;
use App\Models\Preferencias;
use App\Http\Controllers\SearchEngine;
use Illuminate\Http\Request;
use App\User;
use App\stemm_es;
use Elasticsearch\ClientBuilder;


function elacticSearch($factor1){
    $busqueda=$factor1;
   // Documento::reindex();
   // Documento::addAllToIndex();
    
    $client = ClientBuilder::create()->build();
    if(Documento::mappingExists()){
             Documento::getMapping();
    }

   $now=date("Y-m-d H:i:s");
      
   $params = [
       'index' => 'mejora11',
       'type'  => 'documento',
       'body' => [
           
               'query' => [
               'bool' => [
                   "should" => [
                       'multi_match' => [
                           'query' => $busqueda, 
                           'type' => "most_fields",
                           'fuzziness' => '1',
                           // 'fields' => ['contenido'],
                           'fields' => ['titulo^3','descripcion','contenido'],
                           'tie_breaker'=> 0.3
                       ]
                   ]
                   /* "minimum_should_match" => 3,*/
                   
               ]
           ]
           
       ]
   ];
   

//no funciona explain con eloquent, puede que con php nativo pero tendriamos
//que ver como indexar.

//funciona si hacemos el query directamente por consola.

//curl -XGET 'localhost:9200/mejora/_search?pretty' -d' { "explain": true, "query" : { "term" : { "titulo" : "relevancia" } } }'


/*
$params['body']['query']['match']['contenido'] = 'procesos';*/
   
   $collection = Documento::hydrateElasticsearchResult($client->search($params));
   return $collection;
   //si no se pone esto vuejs no lo detecta!!
   return response()->json( $collection);
}
Route::get('indexar','SearchEngine@indexar');
Route::get('search/{busqueda}','SearchEngine@puntuar_segun_busqueda');
Route::post('buscador2','SearchEngine@puntuacion_mayor');

Route::get('vector_caracteristico','SearchEngine@vector_caracteristico');

Route::get('proceso',function (){
   
   $articulos_con_valor=Preferencias::where('total_atributos','>',0)->get();
   return View::make('proceso',compact('articulos_con_valor'));
   
});

Route::get('elasticsearch',function (){
   $client = ClientBuilder::create()->build();
      $params = [
          'index' => 'mejora11'
      ];
      $response = $client->indices()->create($params);
      return Documento::addAllToIndex();
});


//antes de calificar por usuarios


//esta función permite crear los vectores caracteristicos
Route::get('vector_caracteristico2',function (){
   //  $articulos_relevantes=elacticSearch('bancos');
   //  return $articulos_relevantes;
      //resetear index.
   //  Documento::addAllToIndex();
      
       

       $documentos=Documento::all();
       //crear vectores vacios para luego darles una puntuación de 1 si tienen relevancia
       foreach ($documentos as $documento) {
          Preferencias::create(array('identificacion'=>$documento->id));
       }
        $factores=['exportaciones','tratado','fuerzas','paz','bancos'];
        foreach ($factores as $idFactor =>$factor) {
           
           $articulos_relevantes=elacticSearch($factor);
          
             foreach ($articulos_relevantes as $articulo) {

                  $guardar= Preferencias::where('identificacion',$articulo->id)->first();

                  if(isset($guardar)){
                     
                         if($factor=='exportaciones'){
                                
                                $guardar->factor1=1;
                                $guardar->save();
                            }elseif($factor=='tratado'){
                                 
                                $guardar->factor2=1;
                                $guardar->save();
                            }elseif($factor=='fuerzas'){
                                 
                                $guardar->factor3=1;
                                $guardar->save();
                            }elseif($factor=='paz'){
                                 
                                $guardar->factor4=1;
                                $guardar->save();
                            }elseif($factor=='bancos'){
                                 
                                $guardar->factor5=1;
                                $guardar->save();
                            }
                  }
                           
                
             }
      
             
        }
 
      
      //normalización de fila
      
      
      //se calcula la suma de fila
   $total_atributos=0;
   
   $documentos=Preferencias::select('factor1','factor2','factor3','factor4','factor5','factor6','identificacion')->get();
   foreach ( $documentos as $documento) {
         $total_atributos=0;
         // return $documento;
         // $identificacion=$identificaciones
          $id_documento=$documento->identificacion;
          $articulo=Preferencias::where('identificacion',$id_documento)->first();
         // return $id;
        
         // columnas de un articulo
         
               
               if($documento->factor1==1){
                     $total_atributos=$total_atributos+1;
               }
                if($documento->factor2==1){
                     $total_atributos=$total_atributos+1;
               }
                if($documento->factor3==1){
                     $total_atributos=$total_atributos+1;
               }
                if($documento->factor4==1){
                     $total_atributos=$total_atributos+1;
               }
                if($documento->factor5==1){
                     $total_atributos=$total_atributos+1;
               }
                if($documento->factor6==1){
                     $total_atributos=$total_atributos+1;
               }
               $articulo->total_atributos=$total_atributos;
               $articulo->save();
               
          
          
       }
   
   //se calcula la normalización con la suma de fila
   
   foreach ($documentos as $documento) {
      
      $articulo=Preferencias::where('identificacion',$documento->identificacion)->first();
      
       if( $articulo->total_atributos!=0){
           $articulo->factor1=$articulo->factor1/ sqrt($articulo->total_atributos);
       }else{
           $articulo->factor1=0;
       }
      
      if( $articulo->total_atributos!=0){
           $articulo->factor2=$articulo->factor2/ sqrt($articulo->total_atributos);
       }else{
           $articulo->factor2=0;
       }
         
      if( $articulo->total_atributos!=0){
           $articulo->factor3=$articulo->factor3/ sqrt($articulo->total_atributos);
       }else{
           $articulo->factor3=0;
       }
       
      if( $articulo->total_atributos!=0){
           $articulo->factor4=$articulo->factor4/ sqrt($articulo->total_atributos);
       }else{
           $articulo->factor4=0;
       }
       
      if( $articulo->total_atributos!=0){
           $articulo->factor5=$articulo->factor5/ sqrt($articulo->total_atributos);
       }else{
           $articulo->factor5=0;
       }
      
      if( $articulo->total_atributos!=0){
           $articulo->factor6=$articulo->factor6/ sqrt($articulo->total_atributos);
       }else{
           $articulo->factor6=0;
       }

      
      $articulo->save();
   }
      
      
      
      // Como se ha incorporado la tabla interes para tener un interes separado por usuario
      // es necesario crear tantas filas interes con el campo prediccion como articulos haya para obtener predicciones de articulos no puntuados 
      // que se asemejen al vector prototipo
      
      
      //la cración de los registros demora demaciado, mejor crear la columna user1,user2, por demanda con sql directo
      
      $articulos=Preferencias::all();
      foreach($articulos as $articulo){
         
         for ($i = 1; $i < 10; $i++) {
             $puntuado=Interes::create(array('id_articulo'=>$articulo->identificacion,'id_usuario'=>$i, 'interes'=>0));
         }
         
      }
 
});

//mostrar articulos 
Route::get('/usuario/{usuario}',function ($usuario){
   
   $usuario=$usuario;
   $tasks=Preferencias::where('total_atributos','>',0)->with('articulo')->orderBy('identificacion','desc')->get();
   // return $tasks;
   return View::make('articulos',compact('tasks','usuario'));

});


//calificar


Route::get('/like/{usuario}/{id}',function ($usuario,$id){
   
   $id_usuario=$usuario;
   
   $puntuado=Interes::where('id_articulo',$id)->where('id_usuario',$id_usuario)->first();
   if($puntuado!=null){
      
      $puntuado->interes=1;
   }else{
      $puntuado=Interes::create(array('id_articulo'=>$id,'id_usuario'=>$id_usuario, 'interes'=>1));
   }
   $puntuado->save();
   return back();
    
});
Route::get('dislike/{usuario}/{id}',function ($usuario,$id){
   
   $id_usuario=$usuario;
   
   $puntuado=Interes::where('id_articulo',$id)->where('id_usuario',$id_usuario)->first();
   if($puntuado!=null){
      $puntuado->interes=-1;
   }else{
      $puntuado=Interes::create(array('id_articulo'=>$id,'id_usuario'=>$id_usuario, 'interes'=>-1));
   }
   $puntuado->save();
   return back();
});

Route::group(['middleware' => ['web']], function () {
   //despues de calificar

      //se calcula el vector prototipo
      Route::get('perfil_usuario/{usuario}',function ($usuario){

         $id_usuario=$usuario;
         $articulos=Preferencias::select('factor1','factor2','factor3','factor4','factor5','factor6','identificacion')->get();
         $sum_factor1=0;
         $sum_factor2=0;
         $sum_factor3=0;
         $sum_factor4=0;
         $sum_factor5=0;
         $sum_factor6=0;
         //calculando suma de columna para cada factor
         foreach ($articulos as $articulo) {
            // return $articulo;
            $factor1=$articulo->factor1;
            $factor2=$articulo->factor2;
            $factor3=$articulo->factor3;
            $factor4=$articulo->factor4;
            $factor5=$articulo->factor5;
            $factor6=$articulo->factor6;
            
            $interes=Interes::where('id_articulo',$articulo->identificacion)->where('id_usuario',$id_usuario)->first();
            
            if(isset($interes)){
               
               //se realiza una suma de productos de la puntuación del usuario para un articulo1 con la puntuación del factor1 si existe
               //puntuacion es una variable auxiliar sin importancia
               if($interes!=null){
                     // echo $interes->interes;
                   $puntuacion=$factor1*$interes->interes;
                   $sum_factor1=$sum_factor1+$puntuacion;
                   
                   $puntuacion=$factor2*$interes->interes;
                   $sum_factor2=$sum_factor2+$puntuacion;
                   
                   $puntuacion=$factor3*$interes->interes;
                   $sum_factor3=$sum_factor3+$puntuacion;
                   
                   $puntuacion=$factor4*$interes->interes;
                   $sum_factor4=$sum_factor4+$puntuacion;
                   
                   $puntuacion=$factor5*$interes->interes;
                   $sum_factor5=$sum_factor5+$puntuacion;
                   
                   $puntuacion=$factor6*$interes->interes;
                   $sum_factor6=$sum_factor6+$puntuacion;
                   
               }
            }
         }
         
         //almacenar valores de cada factor en tabla perfil
         $perfil=Perfil::where('id_usuario',$id_usuario)->first();
          
          if($perfil!=null){
             $perfil->factor1=$sum_factor1;
             $perfil->factor2=$sum_factor2;
             $perfil->factor3=$sum_factor3;
             $perfil->factor4=$sum_factor4;
             $perfil->factor5=$sum_factor5;
             $perfil->factor6=$sum_factor6;
             $perfil->id_usuario=$id_usuario;
             $perfil->save();
          }else{
             Perfil::create(
                array('id_usuario'=>$id_usuario, 
                     'factor1'=>$sum_factor1, 
                     'factor2'=>$sum_factor2,
                     'factor3'=>$sum_factor3, 
                     'factor4'=>$sum_factor4, 
                     'factor5'=>$sum_factor5, 
                     'factor6'=>$sum_factor6));
          }   
               
      
           //calculando el vector de DF 
           
           //contar valores en una columan mayores a 0

         $total_articulos= count(Documento::all());
         $articulos=Preferencias::select('factor1','factor2','factor3','factor4','factor5','factor6','identificacion')->get();
         $sum_factor1=0;
         $sum_factor2=0;
         $sum_factor3=0;
         $sum_factor4=0;
         $sum_factor5=0;
         $sum_factor6=0;
         foreach ($articulos as $articulo) {
            
            if($articulo->factor1>0){
               $sum_factor1+= 1;
            }
            if($articulo->factor2>0){
               $sum_factor2+= 1;
            }
            if($articulo->factor3>0){
               $sum_factor3+= 1;
            }
            if($articulo->facto4>0){
               $sum_factor4+= 1;
            }
            if($articulo->factor5>0){
               $sum_factor5+= 1;
            }
            if($articulo->factor6>0){
               $sum_factor6+= 1;
            }
            
          
         }
            //aplicar la normalización de datos
            
            $sum_factor1= ($sum_factor1>0)? log($total_articulos/$sum_factor1) :0;
            $sum_factor2= ($sum_factor2>0)? log($total_articulos/$sum_factor2) :0;
            $sum_factor3= ($sum_factor3>0)? log($total_articulos/$sum_factor3) :0;
            $sum_factor4= ($sum_factor4>0)? log($total_articulos/$sum_factor4) :0;
            $sum_factor5= ($sum_factor5>0)? log($total_articulos/$sum_factor5) :0;
            $sum_factor6= ($sum_factor6>0)? log($total_articulos/$sum_factor6) :0;
            
            //almacenar en tabla DF
            
            
            //por cada usuario habria una fila pero sería con el mismo valor.
            $fila=Df::create(array('factor1'=>$sum_factor1,'factor2'=>$sum_factor2,'factor3'=>$sum_factor3,'factor4'=>$sum_factor4, 'factor5'=>$sum_factor5, 'factor6'=>$sum_factor6));
            /*  return 'ok';*/
            
            
            $perfil_usuario=Perfil::where('id_usuario',$id_usuario)->first();
            
            $df=Df::orderBy('created_at','desc')->first();
           
           
           
           //calculando predicción por la suma de productos de v caracteristico, perfil y DF
            $prediccion=0;

            foreach ($articulos as $articulo) {
               
              
               $factor1=$articulo->factor1*$perfil_usuario->factor1*$df->factor1;
               $factor2=$articulo->factor2*$perfil_usuario->factor2*$df->factor2;
               $factor3=$articulo->factor3*$perfil_usuario->factor3*$df->factor3;
               $factor4=$articulo->factor4*$perfil_usuario->factor4*$df->factor4;
               $factor5=$articulo->factor5*$perfil_usuario->factor5*$df->factor5;
               $factor6=$articulo->factor6*$perfil_usuario->factor6*$df->factor6;
               
               //existen tantos articulos como tablas de interes? no
               //suma de productos
               
               $prediccion=$prediccion+$factor1+$factor2+$factor3+$factor4+$factor5+$factor6;
               
               // if($articulo->identificacion=='94'){
               // return $prediccion;
               // }
               
               // return $prediccion;
              
               
               //esta botando nulo en articulos que no encuentra.
               $guardar_prediccion=Interes::where('id_usuario',$id_usuario)->where('id_articulo',$articulo->identificacion)->first();
               
               if(isset($guardar_prediccion)){
                  
                  $guardar_prediccion->prediccion=$prediccion;
                  $guardar_prediccion->save();
                  $prediccion=0;
               }
               
              
            } 
            Session::put('id_usuario',$id_usuario);
            return redirect('recomendaciones');
         
      });

      
      Route::get('/recomendaciones',function (Request $request){
         
         $usuario=Session::get('id_usuario');
         $tasks=Preferencias::all();
         
         $articulos=Interes::where('id_usuario',$usuario)->where('prediccion','>','0')->with('articulo')->orderBy('prediccion','desc')->get();
          return View::make('recomendaciones',compact('tasks','articulos', 'usuario'));
      
      });

});




Route::get('/',function (){
    return View::make('buscador');

});
Route::get('/prueba',function (){
    return test();
});

Route::get('/json',function (){
    
    
    $json='{ 
        
        "topic": [
         {
            "identifier": "41-AH",
            "title": "Pesticidas en alimentos para bebes",
            "description": "Encontrar noticias sobre pesticidas en alimentos para bebes.",
            "narrative": "Los documentos relevantes proporcionan información sobre el descubrimiento de pesticidas en alimentos para bebes. Se informa sobre diferentes marcas, supermercados y compañías que ofrecieron alimentos para bebes que contenian pesticidas. Se discuten también medidas contra la contaminación de alimentos para bebes con pesticidas.",
            "_lang": "es"
         },
         {
            "identifier": "42-AH",
            "title": "Naciones Unidas y Estados Unidos invaden Haití",
            "description": "Encontrar documentos sobre la invasión de Haití por los soldados de la ONU y de los Estados Unidos.",
            "narrative": "Los documentos comentan tanto la discusión sobre la decisión de la ONU de enviar las tropas americanas a Haití, como la invasión misma. Se habla también de sus consecuencias directas.",
            "_lang": "es"
         },
         {
            "identifier": "43-AH",
            "title": "\"El Niño\" y el tiempo",
            "description": "Encontrar noticias que expliquen el fenómeno de \"El Niño\" y su repercusión en el clima del planeta (incluidos los efectos que tiene sobre la temperatura, presión atmosférica, precipitaciones, etc.).",
            "narrative": "Los documentos relevantes proporcionarán información sobre los efectos de \"El Niño\". Las interacciones entre los océanos y la atmósfera de la Tierra son interesantes en relación con este fenómeno. \"El Niño\" es especialmente importante en el Pacífico Sur debido a su influencia sobre el clima mundial.",
            "_lang": "es"
         },
         {
            "identifier": "44-AH",
            "title": "Indurain gana el Tour",
            "description": "Reacciones al cuarto Tour de Francia ganado por Miguel Indurain.",
            "narrative": "Los documentos relevantes comentan las reacciones a la cuarta victoria consecutiva de Miguel Indurain en el Tour de Francia. Los documentos que discuten la relevancia de Indurain en el ciclismo mundial después de esta victoria también son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "45-AH",
            "title": "El tratado de paz entre Israel y Jordania",
            "description": "Encontrar noticias que mencionen los nombres de los principales negociadores del tratado de paz en el Medio Oriente entre Israel y Jordania, y también documentos que den una información detallada sobre el tratado.",
            "narrative": "El 26 de octubre de 1996 se firmó un tratado de paz entre Israel y Jordania, abriendo nuevas posibilidades para las relaciones diplomáticas entre ambos países. Los documentos relevantes proporcionarán detalles del tratado y/o mencionarán a los participantes principales de las negociaciones.",
            "_lang": "es"
         },
         {
            "identifier": "46-AH",
            "title": "Embargo sobre Irak",
            "description": "¿Qué efectos ha tenido el embargo de la ONU en la vida del pueblo iraquí?",
            "narrative": "Son relevantes los documentos que describen los cambios en la vida de los iraquíes comparando su vida antes y después del embargo, siempre que los cambios sean directamente atribuibles al embargo. No son relevantes los documentos que contienen retórica insustancial del tipo de \"Cartas al director\" o noticias desde puntos de vista claramente sesgados con fines políticos.",
            "_lang": "es"
         },
         {
            "identifier": "47-AH",
            "title": "Intervención rusa en Chechenia",
            "description": "¿Cuáles son las causas de la intervención militar de Rusia en Chechenia?",
            "narrative": "En los documentos relevantes se discutirán las razones y causas subyacentes de la intervención de las tropas rusas en Chechenia. Se considerarán relevantes también las declaraciones de políticos rusos, incluido el presidente Yeltsin, que justifican el envío de las tropas rusas a Chechenia.",
            "_lang": "es"
         },
         {
            "identifier": "48-AH",
            "title": "Fuerzas de paz en Bosnia.",
            "description": "Razones del retiro de las fuerzas de paz de las Naciones Unidas (ONU) de Bosnia.",
            "narrative": "En 1994, algunas de las naciones europeas que participaban en la misión de paz en Bosnia quisieron retirar sus tropas. Los documentos relevantes informarán sobre las razones para proponer esa retirada.",
            "_lang": "es"
         },
         {
            "identifier": "49-AH",
            "title": "Caída de las exportaciones de coches en Japón.",
            "description": "Los documentos informarán sobre el decrecimiento de las exportaciones de automóviles en Japón.",
            "narrative": "Son relevantes los documentos que informen sobre la caída de las exportaciones de automóviles en Japon en general, o el decrecimiento de las importaciones de coches japoneses en algún país o área concreta. El decrecimiento puede medirse en términos de número de automóviles exportados o en términos de pérdidas financieras por parte de la industria japonesa del automóvil. Los documentos que no incluyan algún tipo de datos para medir esa caída no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "60-AH",
            "title": "Corrupción política en Francia",
            "description": "Encontrar documentos sobre corrupción política en Francia, en particular con referencia a la financiación ilegal de los partidos políticos franceses.",
            "narrative": "Varias figuras públicas relevantes en Francia, incluyendo políticos e industriales, han estado involucradas en casos de corrupción. Los documentos relevantes informan acerca de esos casos. También son de interés las investigaciones policiales o los juicios relacionados con la corrupción en la política.",
            "_lang": "es"
         },
         {
            "identifier": "62-AH",
            "title": "Terremoto en el norte de Japón",
            "description": "Encontrar documentos que informen sobre el terremoto en la costa este de Hokkaido, en el norte de Japón, en 1994.",
            "narrative": "Son relevantes los documentos que describan el terremoto, con magnitud 7.9 en la escala Richter, que sacudió Hokkaido y otras regiones del norte de Japón en Octubre de 1994. También son de interés los avisos de maremoto en áreas costeras del Pacífico en Hokkaido en el momento del terremoto. Los documentos que hablen de cualquier otro terremoto en Japón no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "63-AH",
            "title": "Reserva de ballenas",
            "description": "Encontrar documentos sobre la reserva de la Antártida en la que está prohibida cazar ballenas.",
            "narrative": "Los documentos relevantes discuten los pros y contras de la reserva de ballenas en la Antártida, y mencionan los paises que apoyan la reserva o protestan contra ella. También son relevantes los informes sobre violaciones del área protegida.",
            "_lang": "es"
         },
         {
            "identifier": "64-AH",
            "title": "Síndrome RSI y ratones de ordenador",
            "description": "Encontrar documentos que informen sobre RSI (\"repetitive strain injuries\" o \"enfermedad del periodista\") producidas por el uso del ratón del ordenador.",
            "narrative": "Los documentos relevantes informan sobre daños causados por el uso continuado de un ratón de ordenador. Los documentos que proponen formas de evitar el RSI cuando se usa el ordenador también son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "65-AH",
            "title": "Búsqueda de tesoros",
            "description": "Encontrar documentos sobre buscadores de tesoros y actividades de búsqueda de tesoros.",
            "narrative": "Identificar tipos de búsqueda de tesoros, tales como búsqueda de oro, excavaciones arqueológicas o exploraciones submarinas en busca de galeones hundidos.",
            "_lang": "es"
         },
         {
            "identifier": "66-AH",
            "title": "Retirada de tropas rusas de Letonia",
            "description": "Encontrar noticias y debates sobre la retirada de las tropas rusas de Letonia",
            "narrative": "Los documentos contienen información sobre el debate antes, durante y después de la retirada de las tropas rusas de Letonia. También se incluyen declaraciones de políticos de Rusia, Letonia y otros países sobre esta operación y sobre la planificación del proceso.",
            "_lang": "es"
         },
         {
            "identifier": "67-AH",
            "title": "Colisiones navales",
            "description": "Encontrar información sobre el número de personas heridas o muertas en colisiones entre barcos.",
            "narrative": "Los documentos relevantes deben informar sobre el número de víctimas (muertos o heridos) en colisiones entre barcos o vehículos navales de cualquier tipo. Los documentos que hablan de las víctimas sin proporcionar datos no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "68-AH",
            "title": "Ataques a sinagogas europeas",
            "description": "Encontrar documentos que describan actos de terrorismo o vandalismo contra sinagogas europeas desde el fin de la segunda guerra mundial.",
            "narrative": "Los documentos relevantes mencionarán explosiones de bombas, hallazgo de bombas sin explotar o cualquier otro acto de terrorismo en - o cerca de - sinagogas en Europa. También son relevantes las descripciones de profanaciones, amenazas o escritos ofensivos sobre sinagogas. No son relevantes las referencias a sucesos acaecidos con anterioridad a 1947.",
            "_lang": "es"
         },
         {
            "identifier": "69-AH",
            "title": "Clonación y ética",
            "description": "¿Cuáles son las aplicaciones prácticas de la clonación y qué argumentos existen en contra de ella?",
            "narrative": "Son relevantes los documentos que hablen de alguna aplicación práctica de la clonación en la vida cotidiana, así como los documentos que comenten los argumentos éticos y morales que pueda haber en contra de la clonación. No son relevantes los documentos genéricos sobre técnicas de bio-ingeniería o ingeniería genética.",
            "_lang": "es"
         },
         {
            "identifier": "80-AH",
            "title": "Huelgas de hambre",
            "description": "Los documentos proporcionarán cualquier información relacionada con huelgas de hambre organizadas con el fin de atraer la atención hacia una causa.",
            "narrative": "Identificar casos en que una huelga de hambre haya sido convocada, incluido el motivo de la huelga y el resultado, si se conoce.",
            "_lang": "es"
         },
         {
            "identifier": "81-AH",
            "title": "Secuestro de un Airbus francés",
            "description": "Encontrar toda la información concerniente al papel de un grupo islámico armado en el secuestro de un Airbus de Air France.",
            "narrative": "El Grupo Islámico Armado (GIA) ha realizado numerosos ataques terroristas en Francia. También fueron responsables del secuestro de un Airbus de Air France. Los documentos relevantes darán detalles sobre este secuestro.",
            "_lang": "es"
         },
         {
            "identifier": "82-AH",
            "title": "El IRA ataca aeropuertos",
            "description": "Encontrar documentos que describan actos terroristas cometidos por el Ejército Republicano Irlandés (IRA) en aeropuertos europeos.",
            "narrative": "Los documentos relevantes mencionarán tiroteos u otras acciones terroristas que el Ejército Republicano Irlandés haya realizado - o amenazado con realizar - contra aeropuertos en Europa. Las amenazas que hayan resultado falsas alarmas también son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "83-AH",
            "title": "Subasta de objetos de Lennon.",
            "description": "Encontrar subastas públicas de objetos de John Lennon.",
            "narrative": "Los documentos relevantes hablan de subastas que incluyen objetos que pertenecieron a John Lennon, o que se atribuyen a John Lennon.",
            "_lang": "es"
         },
         {
            "identifier": "84-AH",
            "title": "Ataques de tiburones",
            "description": "Los documentos contienen cualquier información relacionada con los ataques de tiburones a humanos.",
            "narrative": "Identificar ejemplos en que un humano fue atacado por un tiburón, que incluyan dónde tuvo lugar el ataque y sus circunstancias. Son relevantes solamente aquellos documentos que narran un ataque concreto. Los ataques no confirmados o las sospechas de mordeduras no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "85-AH",
            "title": "Programa Turquesa en Ruanda",
            "description": "Encontrar información detallada sobre la operación \"Turquesa\", un programa francés de ayuda humanitaria en Ruanda.",
            "narrative": "Durante el conflicto entre los Hutus y Tutsis, Francia inició la operación \"Turquesa\" en el suroeste de Ruanda con el fin de proporcionar ayuda humanitaria a la población. Los documentos relevantes informarán sobre esta operación.",
            "_lang": "es"
         },
         {
            "identifier": "86-AH",
            "title": "Energía renovable",
            "description": "Encontrar documentos que describan la utilización o políticas relacionadas con energías ecológicas, es decir, energía generada a partir de fuentes renovables.",
            "narrative": "Los documentos relevantes discuten el uso de fuentes de energía renovables como la solar, eólica, de biomasa, hidroeléctrica y geotermal. Los vehículos de baja emisión, como los coches eléctricos o de gas natural comprimido (CNG), no son relevantes. Las celdas de combustible no son relevantes, a menos que el combustible las califique como renovables.",
            "_lang": "es"
         },
         {
            "identifier": "87-AH",
            "title": "Inflación y elecciones en Brasil",
            "description": "Encontrar documentos que analicen la influencia del \"Plan Real\" contra la inflación en las elecciones brasileñas.",
            "narrative": "Los documentos relevantes analizan los efectos del \"Plan Real\", propuesto por el gobierno brasileño para detener la inflación, en las elecciones brasileñas.",
            "_lang": "es"
         },
         {
            "identifier": "88-AH",
            "title": "Vacas locas en Europa",
            "description": "Encontrar documentos que mencionen casos de Encelopatía Espongiforme Bovina (el mal de las vacas locas) en Europa.",
            "narrative": "Los documentos relevantes contendrán estadísticas o cifras sobre casos de animales infectados con Encelopatía Espongiforme Bovina (BSE), conocida como mal de las vacas locas, en Europa. Los documentos que discutan la posible transmisión del mal a los humanos no se consideran relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "89-AH",
            "title": "Schneider en quiebra",
            "description": "Encontrar documentos sobre la bancarrota del agente inmobiliario alemán Schneider.",
            "narrative": "Los documentos informan sobre la bancarrota del agente inmobiliario alemán Schneider y las circunstancias que rodean el hecho. También se estudian los fallos, omisiones y responsabilidades de los bancos alemanes en este caso.",
            "_lang": "es"
         },
         {
            "identifier": "90-AH",
            "title": "Exportadores de verduras",
            "description": "¿Qué países son exportadores de verduras frescas, secas o congeladas?",
            "narrative": "Es relevante cualquier noticia que hable de un país o una región que exporta verduras frescas, secas o congeladas, o que indique el país de origen de verduras importadas. No son relevantes las noticias sobre conservas de verduras, zumos o verduras tratadas de algún otro modo.",
            "_lang": "es"
         },
         {
            "identifier": "91-AH",
            "title": "AI en Latinoamérica",
            "description": "Informes de Amnistía Internacional sobre los derechos humanos en Latinoamérica.",
            "narrative": "Los documentos relevantes deben hablar sobre informes de Amnistía Internacional en relación con los derechos humanos en Latinoamérica, o sobre reacciones a estos informes.",
            "_lang": "es"
         },
         {
            "identifier": "92-AH",
            "title": "Sanciones de la ONU contra Irak.",
            "description": "¿Qué medidas ha tomado Irak para lograr el levantamiento del embargo económico de la ONU, así como de las sanciones políticas impuestas después de su invasión de Kuwait en 1990?",
            "narrative": "Los documentos deben mencionar concretamente de qué forma Irak ha intentado que le sean levantadas las sanciones. Meras descripciones de las sanciones, o de protestas retóricas contra las sanciones, no son relevantes. Las expresiones de arrepentimiento por la invasión de Kuwait por parte de oficiales iraquíes sí se consideran relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "93-AH",
            "title": "Eurofighter",
            "description": "Encontrar documentos que informen acerca del proyecto EFA o \"Eurofighter\".",
            "narrative": "Los documentos relevantes informan sobre el proyecto EFA. Los países socios, Alemania, Reino Unido, Italia y España formaron el consorcio \"Eurofighter\". Son también de interés las informaciones sobre el reparto de trabajo entre los grupos involucrados, así como las estimaciones de costes.",
            "_lang": "es"
         },
         {
            "identifier": "94-AH",
            "title": "Retorno de Solzhenitsin",
            "description": "Encontrar documentos que informen sobre el retorno a Rusia del ganador del premio Nobel de literatura, Solzhenitsin.",
            "narrative": "Los documentos relevantes informarán de las razones y el momento del retorno de Solzhenitsin a Rusia. También pueden mencionar las razones de su emigración a los Estados Unidos.",
            "_lang": "es"
         },
         {
            "identifier": "95-AH",
            "title": "Conflicto en Palestina",
            "description": "Encontrar artículos que traten de conflictos armados en los territorios palestinos y la participación de una parte de la población civil.",
            "narrative": "Los documentos relevantes proporcionarán información sobre sucesos recientes en el conflicto palestino-israelí. También son relevantes los ataques suicidas.",
            "_lang": "es"
         },
         {
            "identifier": "96-AH",
            "title": "¿Debería dimitir González?",
            "description": "Peticiones públicas de dimisión para Felipe González por parte de personalidades políticas en España.",
            "narrative": "Los documentos deben mencionar alguna de las múltiples ocasiones en las que se pidió la dimisión del presidente español Felipe González por parte de sus adversarios políticos. Se deben mencionar las razones para esas demandas.",
            "_lang": "es"
         },
         {
            "identifier": "97-AH",
            "title": "Referéndum sobre la independencia de Moldavia",
            "description": "Encontrar documentos que informen acerca del referéndum sobre la independencia de la República de Moldavia.",
            "narrative": "Los documentos relevantes informan acerca del referéndum en el que la mayoría de la población ha votado a favor de la independencia de Moldavia, es decir, también en contra de una unión con Rumanía. El trasfondo y las consecuencias de esa decisión, así como los resultados del referéndum en diferentes regiones, son de interés.",
            "_lang": "es"
         },
         {
            "identifier": "98-AH",
            "title": "Películas de los Kaurismäki",
            "description": "Buscar información sobre las películas dirigidas por cualquiera de los dos hermanos Aki y Mika Kaurismäki.",
            "narrative": "Los documentos relevantes proporcionan títulos de una o más películas dirigidas por Aki o Mika Kaurismäki.",
            "_lang": "es"
         },
         {
            "identifier": "99-AH",
            "title": "Negación del holocausto",
            "description": "Encontrar documentos que informen sobre las medidas tomadas en Alemania para limitar las acciones públicas negando el Shoah.",
            "narrative": "Se requiere información sobre las medidas que se han tomado para limitar o prohibir los discursos o escritos que nieguen claramente el holocausto. Los documentos relevantes también pueden discutir temas de libertad de expresión en Alemania en relación con las opiniones que niegan la existencia del holocausto.",
            "_lang": "es"
         },
         {
            "identifier": "110-AH",
            "title": "Extradición de sospechosos iraníes",
            "description": "Francia extraditó a Teherán los dos iraníes sospechosos de haber asesinado a Kazem Radjavi en Suiza.",
            "narrative": "Kazem Radjavi, un oponente político del gobierno islámico de Teherán, fue asesinado en Suiza. Dos sospechosos iraníes que residían en Francia fueron extraditados a Irán en lugar de ser extraditados a Suiza. Los documentos relevantes informan sobre la extradición, y también sobre sus efectos en las relaciones entre Francia e Irán.",
            "_lang": "es"
         },
         {
            "identifier": "111-AH",
            "title": "Animación por ordenador",
            "description": "Encontrar discusiones sobre el impacto de la animación por ordenador en la industria cinematográfica.",
            "narrative": "Cualquier información relacionada con el efecto de la animación por ordenador en la industria del cine es relevante; por ejemplo, la viabilidad económica de las películas animadas por ordenador. Los documentos que hablen de la animación por ordenador para aplicaciones distintas al cine no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "112-AH",
            "title": "Pulp Fiction",
            "description": "¿Qué premio ganó la película \"Pulp Fiction\", dirigida por Quentin Tarantino, en el Festival de Cine de Cannes?",
            "narrative": "El film Pulp Fiction, con John Travolta, recibió un premio en el festival de Cannes. Los documentos relevantes deben dar el nombre del premio asociado con esa cinta.",
            "_lang": "es"
         },
         {
            "identifier": "113-AH",
            "title": "Eurocopa",
            "description": "Encontrar documentos que discutan las fases preliminares, y cualquier otro asunto organizativo, de la Eurocopa de fútbol de 1996.",
            "narrative": "Cualquier información sobre la fase preparatoria de la Eurocopa 1996 es relevante. Los documentos pueden discutir la organización de los grupos de la fase previa, o los equipos que se clasifican para la fase final, etc.",
            "_lang": "es"
         },
         {
            "identifier": "114-AH",
            "title": "Guerra Civil en Afganistán",
            "description": "Encontrar todos los documentos que describan la situación política en Afganistán que llevó a la guerra civil tras la deposición del líder comunista Najibullah.",
            "narrative": "La caída del gobierno comunista liderado por Najibullah en Abril de 1992 fue seguida por una guerra civil, causada por los desacuerdos políticos entre las facciones del primer ministro Hekmatyar y el presidente de la república, Rabbani. Sólo son de interés aquellos documentos que hagan referencia explícita a la caída del gobierno de Najibullah como una causa de los conflictos civiles y políticos posteriores.",
            "_lang": "es"
         },
         {
            "identifier": "115-AH",
            "title": "Estadísticas de divorcio",
            "description": "Proporcionar estadísticas sobre tasas de divorcio en distintos países.",
            "narrative": "Encontrar información sobre tendencias internacionales de tasas de divorcio. Los documentos relevantes incluirán cifras de divorcios en cualquier país del mundo.",
            "_lang": "es"
         },
         {
            "identifier": "116-AH",
            "title": "El snowboard",
            "description": "Encontrar documentos que hablen de la introducción de un nuevo deporte de invierno: el snowboard.",
            "narrative": "Los documentos relevantes deben hablar de la aparición, uso y riesgos del snowboard, y también pueden proporcionar detalles sobre accidentes en pistas de esquí causados por los usuarios de snowboard. Los documentos que hablan en favor de este deporte, alegando que no es más peligroso que el esquí, también se consideran relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "117-AH",
            "title": "Elecciones al parlamento europeo",
            "description": "¿Cuántos votantes fueron llamados a las urnas para elegir al nuevo parlamento europeo en 1994?",
            "narrative": "Los documentos relevantes informan sobre el número de ciudadanos de la Unión Europea con derecho a voto en las elecciones al parlamento europeo en 1994. Éstos pueden dar cifras de votantes de un país o región concreta, o de la totalidad de la Unión Europea. No son relevantes los documentos que mencionen porcentajes de votantes que finalmente ejercieron su derecho al voto.",
            "_lang": "es"
         },
         {
            "identifier": "118-AH",
            "title": "Primer comisario finlandés de la UE",
            "description": "¿Quién fue nombrado el primer comisario finlandés de la Unión Europea?",
            "narrative": "Dar el nombre del primer comisario finlandés de la Unión Europea. Los documentos relevantes pueden mencionar también los puntos de interés dentro de las tareas del nuevo comisario.",
            "_lang": "es"
         },
         {
            "identifier": "119-AH",
            "title": "Destrucción de armas nucleares ucranianas",
            "description": "Encontrar documentos que hablen sobre el acuerdo entre Ucrania, Rusia y los Estados Unidos para la retirada y destrucción de armas nucleares ucranianas en Junio de 1996.",
            "narrative": "Los documentos relevantes informan del acuerdo entre Ucrania, Rusia y los Estados Unidos sobre la retirada y destrucción de armas nucleares en Ucrania. El número de armas y los detalles de las negociaciones son también de importancia.",
            "_lang": "es"
         },
         {
            "identifier": "130-AH",
            "title": "Muerte del líder de Nirvana",
            "description": "¿Cómo murió el cantante y líder del grupo americano de rock y grunge Nirvana?",
            "narrative": "Kurt Cobain, cantante y líder del famosos grupo de música Nirvana, murió en Abril de 1994. Los documentos que hablan de la muerte de Cobain sin mencionar su causa no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "131-AH",
            "title": "Derechos de propiedad intelectual",
            "description": "Encontrar información sobre los derechos de propiedad intelectual de los autores y los esfuerzos de distintos países para proporcionar protección a los autores.",
            "narrative": "Los documentos relevantes deben discutir medios para proteger los derechos de propiedad intelectual en cualquier lugar del mundo. La información sobre regulación de patentes no es relevante.",
            "_lang": "es"
         },
         {
            "identifier": "132-AH",
            "title": "Área de Kaliningrado",
            "description": "Encontrar documentos que discutan el futuro político y económico del enclave de Kaliningrado.",
            "narrative": "Sólo son de interés las informaciones sobre política y economía en Kaliningrado. Las perspectivas de relaciones futuras con Escandinavia, los países bálticos y Rusia también son relevantes. La información histórica o turística no es relevante.",
            "_lang": "es"
         },
         {
            "identifier": "133-AH",
            "title": "Fuerzas Armadas alemanas fuera de área",
            "description": "Encontrar documentos que informen sobre las decisiones políticas y jurídicas sobre los usos \"fuera de área\" de las Fuerzas Armadas Alemanas.",
            "narrative": "Los documentos relevantes deben, o bien informar sobre las decisiones políticas y jurídicas sobre usos fuera de área (es decir, misiones de combate fuera del área de la OTAN) de las fuerzas armadas federales alemanas, o bien discutir las consecuencias de esa decisión.",
            "_lang": "es"
         },
         {
            "identifier": "134-AH",
            "title": "La sonda espacial Clementine",
            "description": "Encontrar informes sobre la sonda espacial americana Clementine, lanzada para recoger datos sobre la luna y el asteroide, cercano a la tierra, 1620 Geographos.",
            "narrative": "Los objetivos principales de Clementine, la sonda espacial americana lanzada por la NASA y el Departamento de Defensa, son estudiar la resistencia de los sistemas de micro-electrónicos y sensores, y recoger datos sobre la luna y el asteroide Geographos. Los documentos relevantes deben proporcionar información sobre los aspectos científicos y técnicos de la misión.",
            "_lang": "es"
         },
         {
            "identifier": "135-AH",
            "title": "Candidatos a la presidencia de la Comisión Europea",
            "description": "¿Quiénes fueron mencionados como posibles candidatos a la presidencia de la Comisión Europea?",
            "narrative": "Los documentos deben dar nombres de personas que hayan sido mencionadas como posibles candidatos a la presidencia de la Comisión Europea.",
            "_lang": "es"
         },
         {
            "identifier": "136-AH",
            "title": "Torre inclinada de Pisa",
            "description": "¿En qué estado se encuentra la torre inclinada de Pisa?",
            "narrative": "Los documentos relevantes discutirán el estado de salud de la torre inclinada de Pisa, y en particular mencionarán su inclinación y cómo está variando ésta. También son de interés Los documentos que informen de acciones para reducir esta inclinación.",
            "_lang": "es"
         },
         {
            "identifier": "137-AH",
            "title": "Concursos internacionales de belleza",
            "description": "Encontrar nombres de ganadores de concursos internacionales de belleza en 1994 o 1995.",
            "narrative": "Los documentos relevantes deben citar los nombres de hombres o mujeres que ganaran un concurso internacional de belleza en 1994 o 1995. No se incluyen las competiciones de body-building.",
            "_lang": "es"
         },
         {
            "identifier": "138-AH",
            "title": "Extranjerismos en el francés",
            "description": "Encontrar documentos que traten del papel de los extranjerismos en la lengua francesa.",
            "narrative": "El tribunal constitucional ha revisado, para hacerla menos estricta, la ley de Toubon sobre el uso del idioma francés. Los documentos relevantes discuten el papel y el uso de extranjerismos en el francés.",
            "_lang": "es"
         },
         {
            "identifier": "139-AH",
            "title": "Cuotas de pesca en la UE",
            "description": "Encontrar información acerca de las cuotas de pesca en la UE.",
            "narrative": "Los documentos deben incluir cifras o discusiones acerca de las cuotas de pesca entre los países de la UE.",
            "_lang": "es"
         },
         {
            "identifier": "140-AH",
            "title": "Teléfonos móviles",
            "description": "Perspectivas del uso de teléfonos móviles.",
            "narrative": "Los documentos relevantes informarán sobre las perspectivas para el uso de teléfonos móviles y el desarrollo de la industria de telefonía móvil.",
            "_lang": "es"
         },
         {
            "identifier": "141-AH",
            "title": "Carta-bomba para Kiesbauer",
            "description": "Encontrar información sobre la explosión de una carta-bomba en el estudio de la presentadora del canal de televisión PRO7 Arabella Kiesbauer.",
            "narrative": "Una carta-bomba enviada por radicales de extrema derecha, a la famosa presentadora de televisión negra Arabella Kiesbauer hizo explosión en un estudio del canal de TV PRO7 el 9 de junio de 1995. Una asistente resultó herida. Todos los documentos sobre la explosión y las investigaciones policiales tras el suceso son relevantes. Otros informes sobre ataques con cartas-bomba no son de interés.",
            "_lang": "es"
         },
         {
            "identifier": "142-AH",
            "title": "Christo envuelve el edificio del Reichstag alemán",
            "description": "Encontrar documentos que hablen de este acto del artista alemán Christo en el Reichstag alemán en Berlín.",
            "narrative": "El artista Christo tardó dos semanas en junio de 1995 en envolver el Reichstag alemán utilizando una enorme cantidad de material. Encontrar documentos sobre este evento artístico. Cualquier información sobre los preparativos o el evento mismo es relevante, incluyendo los debates políticos, las decisiones y los preparativos técnicos.",
            "_lang": "es"
         },
         {
            "identifier": "143-AH",
            "title": "Conferencia de la Mujer en Pekín",
            "description": "Las posiciones controvertidas adoptadas por algunos delegados hicieron que la Conferencia mundial de la Mujer en Pekín se expusiese al fracaso.",
            "narrative": "En los documentos relevantes se debe discutir alguno de los numerosos problemas o discrepancias que surgieron en relación con la Conferencia de la Mujer en Pekín. Son interesantes, en particular, las posturas mantenidas por los representantes del Vaticano, las comunidades islámicas y el Partido Comunista Chino.",
            "_lang": "es"
         },
         {
            "identifier": "144-AH",
            "title": "Diamantes y rebelión en Sierra Leona",
            "description": "¿Cómo han influido las rebeliones y otras manifestaciones de la inestabilidad política en la industria de diamantes de Sierra Leona?",
            "narrative": "Para ser relevantes, los documentos deben ser explícitos en cuanto a las consecuencias de la rebelión para la industria de diamantes.",
            "_lang": "es"
         },
         {
            "identifier": "145-AH",
            "title": "Importaciones de arroz en Japón",
            "description": "Encontrar documentos que discutan las razones y las consecuencias de las primeras importaciones de arroz en Japón.",
            "narrative": "En 1994, Japón decidió abrir el mercado nacional de arroz a otros países por primera vez. Los documentos relevantes deben comentar este tema. La discusión puede incluir los nombres de los países de los que se importa arroz, los tipos de arroz, y las controversias que ha desatado esta decisión en Japón.",
            "_lang": "es"
         },
         {
            "identifier": "146-AH",
            "title": "Comida rápida en Japón",
            "description": "¿Qué cadena americana de comida rápida tiene un gran número de restaurantes franquicia en Japón?",
            "narrative": "Los documentos relevantes deben mencionar el nombre de la cadena americana de comida rápida de más éxito en Japón, y pueden contener información adicional sobre la introducción de esta clase de comida en la sociedad japonesa.",
            "_lang": "es"
         },
         {
            "identifier": "147-AH",
            "title": "Accidentes petrolíferos y aves",
            "description": "Encontrar documentos que describan daños o lesiones causadas a las aves por vertidos accidentales de petróleo o polución.",
            "narrative": "Son relevantes todos los documentos que traten de los daños que sufren las aves a causa de los accidentes petrolíferos. Los informes sobre vertidos de sentina o derrames intencionados de petróleo no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "148-AH",
            "title": "Destrucción de la capa de ozono",
            "description": "¿Qué agujeros en la capa de ozono no son una consecuencia de la contaminación?",
            "narrative": "No todos los tipos de destrucción que sufre la capa de ozono son fruto de la contaminación. Los documentos relevantes deben informar sobre otras causas de los agujeros en la capa de ozono.",
            "_lang": "es"
         },
         {
            "identifier": "149-AH",
            "title": "Visita del Papa a Sri Lanka",
            "description": "Encontrar informes acerca de las protestas o los problemas causados por las declaraciones previas del Papa acerca del budismo durante su visita a Sri Lanka.",
            "narrative": "El Papa Juan Pablo II se encontró con una acogida desigual cuando visitó Sri Lanka, país mayoritariamente budista. La visita del Papa a Sri Lanka se convirtió en una prueba para las relaciones entre la Iglesia Católica y los líderes budistas, a la que siguieron comentarios sobre el Budismo en un libro publicado recientemente.",
            "_lang": "es"
         },
         {
            "identifier": "160-AH",
            "title": "Consumo de whisky escocés",
            "description": "Los documentos tratarán sobre la cantidad de whisky escocés consumida por los escoceses, en relación con la cantidad de whisky escocés que exporta Escocia.",
            "narrative": "Para ser relevantes, los documentos deben informar sobre el consumo de whisky escocés por los escoceses en relación con el volumen de producción. Los documentos que hablen del consumo de whisky escocés en otros países o analicen las causas de la disminución de las exportaciones no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "161-AH",
            "title": "Dietas para celíacos",
            "description": "Encontrar informes que discutan los problemas de dieta de los celíacos.",
            "narrative": "Encontrar informes que discutan los problemas nutricionales de los celíacos. Son de particular interés los informes sobre si los celíacos pueden o no comer avena.",
            "_lang": "es"
         },
         {
            "identifier": "162-AH",
            "title": "Aduanas entre la UE y Turquía",
            "description": "Encontrar documentos sobre los problemas planteados por Grecia en relación con la desaparición de aduanas entre la Unión Europea y Turquía.",
            "narrative": "Los documentos relevantes deben mencionar explícitamente la posición de Grecia con referencia a la propuesta de unión aduanera entre la UE y Turquía.",
            "_lang": "es"
         },
         {
            "identifier": "163-AH",
            "title": "Restricciones para los fumadores",
            "description": "Encontrar documentos sobre normas o leyes cuyo objetivo sea prohibir o limitar el tabaco en restaurantes.",
            "narrative": "Son relevantes todos los documentos que hablen de leyes y regulaciones relacionadas con la prohibición o limitación del consumo de tabaco en restaurantes, así como los documentos que describan propuestas o consecuencias de este tipo de legislación.",
            "_lang": "es"
         },
         {
            "identifier": "164-AH",
            "title": "Sentencias sobre drogas en Europa",
            "description": "¿Qué sentencias se han dictado en Europa por la venta ilegal de drogas?",
            "narrative": "Los documentos relevantes deben proporcionar información sobre sentencias dictadas en países europeos en relación con el tráfico de drogas. Esta información debe incluir el tipo de sentencia, la duración de la condena o penas alternativas. Las penas por consumo de drogas no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "165-AH",
            "title": "Globos de Oro 1994",
            "description": "¿Qué película ganó el Globo de Oro al mejor drama en 1994?",
            "narrative": "Los Globos de Oro de la temporada de cine de 1994 se concedieron en Los Ángeles en junio de 1995. Los documentos relevantes deben mencionar el nombre de la película que ganó el premio al mejor drama en Los Ángeles.",
            "_lang": "es"
         },
         {
            "identifier": "166-AH",
            "title": "El general francés y la zona de seguridad en los Balcanes",
            "description": "¿Quién fue el general francés responsable de la creación de la zona de seguridad durante el conflicto de los Balcanes?",
            "narrative": "Los documentos relevantes darán el nombre del general francés de las fuerzas de la IFOR que crearon la zona de seguridad durante el conflicto de los Balcanes.",
            "_lang": "es"
         },
         {
            "identifier": "167-AH",
            "title": "Relaciones China-Mongolia",
            "description": "Encontrar información sobre las relaciones recientes y acuerdos de cooperación entre China y Mongolia.",
            "narrative": "Son relevantes los documentos que proporcionen información sobre relaciones políticas y/o económicas entre China y Mongolia durante el siglo XX.",
            "_lang": "es"
         },
         {
            "identifier": "168-AH",
            "title": "Asesinato de Rabin",
            "description": "¿Quién disparó a Isaac Rabin y por qué?",
            "narrative": "Los documentos relevantes deberán dar el nombre del asesino de Isaac Rabin, el primer ministro de Israel, y explicar algunas de las razones del asesinato.",
            "_lang": "es"
         },
         {
            "identifier": "170-AH",
            "title": "Lenguas oficiales de la UE",
            "description": "Encontrar documentos acerca de los planes franceses para reducir a cinco el número de lenguas oficiales de la Unión Europea.",
            "narrative": "Francia ha propuesto limitar las lenguas principales de la Unión a francés, inglés, alemán, italiano y español. Tras una fuerte oposición, la propuesta ha sido retirada. Los documentos relevantes deben hacer referencia a esta propuesta.",
            "_lang": "es"
         },
         {
            "identifier": "171-AH",
            "title": "Final de hockey sobre hielo en Lillehammer",
            "description": "¿Qué equipos jugaron la final de hockey sobre hielo en los Juegos Olímpicos de Lillehammer en 1994?",
            "narrative": "Los documentos relevantes deben mencionar a los equipos que jugaron la final de hockey sobre hielo en los Juegos Olímpicos de Lillehammer de 1994. Los documentos que informen de qué equipos quedaron en primer y segundo lugar en este partido sin mencionar explícitamente la final, también son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "172-AH",
            "title": "Récords mundiales de Atletismo de 1995",
            "description": "¿Qué nuevas marcas se lograron durante los campeonatos del mundo de 1995 en Göteborg?",
            "narrative": "Los documentos relevantes deberán mencionar los récords que se batieron, describir la prueba y el resultado, y dar el nombre del nuevo poseedor del récord.",
            "_lang": "es"
         },
         {
            "identifier": "173-AH",
            "title": "Pruebas del quark top",
            "description": "Encontrar informes sobre pruebas experimentales de científicos norteamericanos de la existencia del quark top.",
            "narrative": "Científicos de un centro de investigación norteamericano comunicaron que se había podido probar experimentalmente la existencia del quark top. Las partículas elementales llamadas quarks son los constituyentes fundamentales de la materia. Los documentos relevantes deben discutir esta prueba experimental, y no sobre las partículas elementales en general.",
            "_lang": "es"
         },
         {
            "identifier": "174-AH",
            "title": "Polémica de crucifijos en Bavaria",
            "description": "Encontrar documentos sobre la polémica de los crucifijos en las escuelas bávaras.",
            "narrative": "En la polémica de los crucifijos, la corte constitucional federal de Alemania confirmó que los padres tienen derecho a quejarse de forma individual sobre la colocación de crucifijos en las aulas de las escuelas bávaras.",
            "_lang": "es"
         },
         {
            "identifier": "175-AH",
            "title": "Impacto medioambiental en los Everglades",
            "description": "Encontrar noticias sobre el impacto medioambiental que han sufrido los Everglades, por ejemplo, sobre el causado por la industria azucarera de Florida.",
            "narrative": "Las noticias deben tratar el daño causado al medio ambiente en los Everglades. Son de particular interés las discusiones sobre los problemas causados por la industria azucarera. También serán relevantes las referencias al impacto medioambiental producido por los vertidos de granjas en general.",
            "_lang": "es"
         },
         {
            "identifier": "176-AH",
            "title": "El Shoemaker-Levy y Júpiter",
            "description": "Encontrar noticias sobre el impacto del cometa Shoemaker-Levy con el planeta Júpiter.",
            "narrative": "Los documentos relevantes proporcionarán datos sobre la desintegración del cometa Shoemaker-Levy y la colisión de sus restos con Júpiter.",
            "_lang": "es"
         },
         {
            "identifier": "177-AH",
            "title": "Consumo de leche en Europa",
            "description": "Proporcionar estadísticas o información relativa al consumo de leche en Europa.",
            "narrative": "Los documentos relevantes deben proporcionar estadísticas u otro tipo de información sobre el consumo de leche en Europa o en algún país europeo. Los informes sobre derivados de la leche no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "178-AH",
            "title": "Insumisión",
            "description": "Encontrar casos de personas que estén dispuestas a ir a la cárcel antes que realizar el servicio militar.",
            "narrative": "Los documentos relevantes deben hablar de las personas que rehusen por completo el servicion militar (así como su equivalente civil) y estén dispuestos a ir a la cárcel en defensa de sus ideas.",
            "_lang": "es"
         },
         {
            "identifier": "179-AH",
            "title": "Dimisión del Secretario General de la OTAN",
            "description": "¿Por qué el Secretario General de la OTAN se vio obligado a dimitir en 1995?",
            "narrative": "En 1995 el Secretario General de la OTAN se encontraba bajo una fuerte presión política que le obligó a dimitir. Los documentos relevantes deben mencionar al menos una de las razones de esta dimisión.",
            "_lang": "es"
         },
         {
            "identifier": "190-AH",
            "title": "Trabajo infantil en Asia",
            "description": "Encontrar documentos que hablen del trabajo infantil en Asia y de propuestas para erradicarlo o para mejorar las condiciones laborales de los niños.",
            "narrative": "Son relevantes los documentos que traten del trabajo infantil en países de Asia concretos, de las condiciones laborales de los niños y de medidas propuestas para erradicar el trabajo infantil.",
            "_lang": "es"
         },
         {
            "identifier": "191-AH",
            "title": "Cultivos en el Delta del Ebro",
            "description": "¿Cuál era el cultivo predominante en el Delta del Ebro a principios de los 90?",
            "narrative": "Los documentos relevantes deben mencionar el cultivo predominante en el Delta del Ebro en los noventa. También pueden mencionar los cambios previstos según los posibles acuerdos tomados por el GATT.",
            "_lang": "es"
         },
         {
            "identifier": "192-AH",
            "title": "Asesinato del director de una cadena de TV en Rusia",
            "description": "¿Cómo se llamaba el jefe de una cadena de televisión rusa que fue asesinado en la escalera de su domicilio en Moscú?",
            "narrative": "Los documentos relevantes deben hablar del asesinato en Moscú del director de una de las cadenas de televisión de Rusia y antiguo periodista, proporcionando su nombre.",
            "_lang": "es"
         },
         {
            "identifier": "193-AH",
            "title": "La UE y los países bálticos",
            "description": "Encontrar documentos que hablen sobre las negociaciones entre los países bálticos (Estonia, Letonia o Lituania) y la Unión Europea relacionadas con su adhesión a la UE.",
            "narrative": "Los documentos relevantes deben describir las negociaciones entre la UE y Estonia, Letonia o Lituania. Las noticias sobre acuerdos de cooperación individuales entre países europeos concretos y alguna de las repúblicas bálticas no son relevantes.",
            "_lang": "es"
         },
         {
            "identifier": "194-AH",
            "title": "Familia real italiana",
            "description": "Encontrar información sobre el exilio de Italia de los miembros varones de la Casa de Saboya.",
            "narrative": "Los documentos relevantes proporcionarán información acerca de los términos del exilio de la familia real italiana, los Saboya, y/o las condiciones impuestas. Los documentos que proporcionen debates y opiniones relacionadas con un posible fin del destierro de los herederos y su regreso a Italia también deben ser tenidos en cuenta.",
            "_lang": "es"
         },
         {
            "identifier": "196-AH",
            "title": "Fusión de bancos japoneses",
            "description": "Encontrar documentos sobre la fusión del banco japonés Mitsubishi y el Banco de Tokyo para formar el mayor banco del mundo.",
            "narrative": "Las dos instituciones japonesas de crédito Mitsubishi Bank y Bank of Tokyo llegaron a un acuerdo de fusión. El nuevo banco es el mayor del mundo.",
            "_lang": "es"
         },
         {
            "identifier": "197-AH",
            "title": "Tratado de paz de Dayton",
            "description": "Encontrar documentos sobre el acuerdo de paz alcanzado en Dayton sobre Bosnia-Herzegovina.",
            "narrative": "De acuerdo con el tratado de Dayton, la OTAN se hizo responsable del mantenimiento militar de la paz en Bosnia-Herzegovina. Los documentos relevantes deben informar sobre las negociaciones, los contenidos y las conclusiones del acuerdo.",
            "_lang": "es"
         },
         {
            "identifier": "198-AH",
            "title": "Oscar honorífico para directores italianos",
            "description": "Encontrar información acerca de directores italianos que hayan sido premiados con un Oscar honorífico como reconocimiento a toda su carrera.",
            "narrative": "Los documentos relevantes proporcionarán información sobre qué directores de cine italianos han recibido un Oscar en la entrega de los Premios de la Academia en Los Ángeles por su trayectoria de toda una vida dedicada al cine.",
            "_lang": "es"
         },
         {
            "identifier": "199-AH",
            "title": "Epidemia de ébola en Zaire",
            "description": "Encontrar documentos sobre las medidas preventivas tomadas tras el brote epidémico de ébola en el Zaire.",
            "narrative": "En Mayo de 1995, en la ciudad zaireña de Kikwit, se produjo un brote de una de las epidemias más temidas: la epidemia de ébola. Los documentos relevantes informan de las medidas que se tomaron para impedir la propagación de esta enfermedad.",
            "_lang": "es"
         },
         {
            "identifier": "200-AH",
            "title": "Inundaciones en Holanda y Alemania",
            "description": "Encontrar estadísticas sobre las inundaciones en Holanda y Alemania en 1995.",
            "narrative": "Los documentos relevantes deben cuantificar los efectos del daño causado por las inundaciones que tuvieron lugar en Alemania y los Países Bajos en 1995, en términos de número de personas y animales evacuados y/o de pérdidas económicas.",
            "_lang": "es"
         }
      ]
        
        
           }';
      //$json = '{"a":[{"hola":"hola"}]}';
      
      
   //$documentos= json_decode($json)->topic[1]->title;
   $documentos= json_decode($json)->topic;
   //return $documentos;
   $size= count(json_decode($json)->topic);
   
   foreach ($documentos as $documento) {
      
      Documento::create(
         array('titulo' =>$documento->title,
               'descripcion'=>$documento->description,
               'contenido'=>$documento->narrative));
               

   }
   return 'ok';   
});


Route::get('buscador',function (Request $request){

$busqueda=$request->input('variables');
/*return $busqueda;*/



//es super necesario para poder obtener un analizador diferente crear un nuevo index para que jale de allí

/*return 
Documento::createIndex($shards = null, $replicas = null);*/
   //return Documento::getMapping();
    //causante de error si queremos hacer range
   /*Documento::reindex();*/
   Documento::addAllToIndex();
    
    $client = ClientBuilder::create()->build();
    /*if(Documento::mappingExists()){
             Documento::getMapping();
    }*/
    $params = array(
        'index' => 'mejora',
        'type'  => 'documento'
    );


/*"filtered": {
      "filter": {
        "bool": {
          "must": [
            {
              "range": {
                "date": {
                  "gte": "2015-11-01",
                  "lte": "2015-11-30"
                }
              }
            }
          ]
        }
      }
    }*/
    
$now=date("Y-m-d H:i:s");
      
/*$params = [
    'index' => 'my_custom_index_name',
    'type' => 'documento',
    'body' => [
        'query' => [
            'bool' => [
                'should' => [
                    'match' => [ 'contenido' => 'procesos' ]
                ],
                "filter" => [
                    "range" => [ "created_at" => [ "gte" => "2014-01-01" ]] 
                ]
            ]
        ]
    ]
];
*/


/*'filter'=> [
                    'range' => [
                        'timestamp'=> [
                            'created_at' => [
                                'gte' => $now
                            ]
                        ]
                    ]
                ],
*/



/*$params = [
    'index' => 'my_custom_index_name',
    'type'  => 'documento',
    'body' => [
        'query' => [
            'bool' => [
                "should" => [
                    ["match"=> ['contenido'=> 'procesos']]
                ],
                "filter" =>  [
                    [ "range" => [ "created_at" => ["gte" => '2014-01-01 00:00:00']]
                ]
                
      
                
            ]
        ]
    ]
];*/

/*$params = [
    'index' => 'my_custom_index_name',
    'type'  => 'documento',
    'body' => [
        'query' => [
            'bool' => [
                "must" => [
                    'multi_match' => [
                        "explain" => true,
                        'query' => $busqueda, 
                        'fuzziness' => 'AUTO',
                        'fields' => ['titulo', 'contenido'],
                    ]
                ]
                
            ]
        ]
    ]
];*/

/*
$params = [
    'index' => 'my_custom_index_name',
    'type'  => 'documento',
    'body' => [
        'query' => [
            'bool' => [
                "must" => [
                    'multi_match' => [
                        'query' => $busqueda, 
                        'fuzziness' => 'AUTO',
                        'fields' => ['titulo', 'contenido'],
                    ],
                    [ "range" => [ "created_at" => ["gte" => '2013-12-09 00:00:00', "format" => "yyyy-MM-dd HH:mm:ss" ]]]
                    
                ]
                
            ]
        ]
    ]
];*/

//   [ "range" => [ "created_at" => ["gte" => '2014-01-01 00:00:00', "format" => "yyyy-MM-dd HH:mm:ss" ]]] 
  
    

// $query = [
//     'multi_match' => [
//         'query' => $busqueda, 
//         'fuzziness' => 'AUTO',
//         'fields' => ['titulo^3', 'contenido'],
//     ],
// ];
// $params = [
//     'index' => 'mejora',
//     'type'  => 'documento',
//     'body' => [
//         'query' => [
//             "term" => ["contenido" => "itil"]
//         ],
//         'aggs' => [
//             'mis_terminos'=> [
//                     'terms' => [
//                             'field' =>'contenido',
//                     ],
                   
//             ]
//         ],
//                                      'size' => 3
//     ]
// ];


//funciona perfecto el lematizador

// $params = [
//     'index' => 'mejora',
//     'analyzer'=>'default2',
//     'body' => 'itil foundation nextech itil foundation es idóneo para que mejoren sus habilidades laborales las personas involucradas en las siguientes áreas escritorio de servicios gestión de incidentes gestión de problemas gestión de cambios gestión de configuraciones y activos de servicio gestión de disponibilidad gestión de capacidad gestión de seguridad de información gestión de nivel de servicio y en general todas las áreas administrativas de ti',
    
// ];

// // Document will be indexed to my_index/my_type/my_id
// $response = $client->indices()->analyze($params);
// return $response;


$busqueda='importaciones exportaciones japon';  
$params = [
    'index' => 'mejora',
    'type'  => 'documento',
    'body' => [
        
            'query' => [
            'bool' => [
                "should" => [
                    'multi_match' => [
                        'query' => $busqueda, 
                        'type' => "most_fields",
                        'fuzziness' => '1',
                        /*'fields' => ['contenido'],*/
                        'fields' => ['titulo', 'descripcion','contenido'],
                        'tie_breaker'=> 0.3
                    ]
                ]
                /* "minimum_should_match" => 3,*/
                
            ]
        ]
        
    ]
];


//no funciona explain con eloquent, puede que con php nativo pero tendriamos
//que ver como indexar.

//funciona si hacemos el query directamente por consola.

//curl -XGET 'localhost:9200/mejora/_search?pretty' -d' { "explain": true, "query" : { "term" : { "titulo" : "relevancia" } } }'


/*
$params['body']['query']['match']['contenido'] = 'procesos';*/

$collection = Documento::hydrateElasticsearchResult($client->search($params));
return $collection;
//si no se pone esto vuejs no lo detecta!!
return response()->json( $collection);
});



Route::get('/elasticsearch',function (){
    
    Documento::addAllToIndex();
/*    $documento = Documento::searchByQuery(array('match' => array('contenido' => 'modulo')));
*/

    
    /*Documento::putMapping($ignoreConflicts = true);
    if(Documento::mappingExists()){
        return Documento::getMapping();
    }
    */
    $documento = Documento::complexSearch(array(
        'body' => array(
            'query' => array(
                'match' => array(
                    'contenido' => 'logisticas~1'
                ),
               
            )
        )
    ));
    
    

   return $documento;
   $documento=Documento::find(1)->isDocument();
   return $documento;
    /*return Documento::addAllToIndex();*/
    /*return Documento::getMapping();
    return */
    $documento =Documento::search('el curso sap');
     
    return $documento->documentScore();

});


Route::get('/',function (){
    return View::make('buscador');

});
Route::get('/test',function (){
    return Documento::find(2);

});

Route::get('/test',function (){
    return Documento::find(2);

});


Route::get('/lematizacion',function (){
    
    
$lines = File::get('stemm_test_corpus.txt');
    
    $now = time();
    foreach ($lines as $line) {
    	$part = split(' ', $linea);
    	$st = stemm_es::stemm($part[0]);
    	if ($st != $part[1]) {
    		print "Word: " . $part[0] . ", stem: " . $st . ", ";
    		print "expected: " . $part[1];
    		print " -- BAD<HR>";
    	}
    }
    
    print "<BR>Stemmed: " . count($lines) . " words in " . (time() - $now) . " secs";


});


Route::post('identificacion', function(Request $request){
   $email=$request->input('email');
   $password=$request->input('password');
   
   $usuario=User::where('email',$email)->where('password',$password)->where('estado',1)->first();
   
   if($usuario){
       return 'exito';
   }else{
       return 'error';
   }
   
   
   return $usuario;
   
    
});

Route::post('crear-perfil', function(Request $request){
   $email=$request->input('email');
   $password=$request->input('password');
   
   $usuario=User::where('email',$email)->where('password',$password)->where('estado',1)->first();
   
   if($usuario){
       
        //usuario normal
        if($usuario->estado==1){
            return redirect('../hacerpedido');
        }else{
            //usuario admin
            return redirect('../mostrarpedidos');
        }
    }else{
        return 'El usuario no esta registrado.';
    }
   
   
   return $registro;
   
    
});


Route::group(['middleware' => ['web']], function () {

    // Route::get('/', function () {
    //     return view('welcome');
    // })->middleware('guest');

    Route::get('/tasks', 'TaskController@index');
    Route::post('/task', 'TaskController@store');
    Route::delete('/task/{task}', 'TaskController@destroy');

    Route::auth();

});

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {
    Route::group(['prefix' => 'v1'], function () {
        require config('infyom.laravel_generator.path.api_routes');
    });
});


Route::auth();

Route::get('/home', 'HomeController@index');

Route::resource('documentos', 'DocumentoController');