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


use App\Models\Documento;
use App\Http\Controllers\SearchEngine;
use Illuminate\Http\Request;
use App\User;
use App\stemm_es;
use Elasticsearch\ClientBuilder;

Route::get('indexar','SearchEngine@indexar');
Route::get('search/{busqueda}','SearchEngine@puntuar_segun_busqueda');
Route::post('buscador2','SearchEngine@puntuacion_mayor');

Route::get('/',function (){
    return View::make('buscador');

});
Route::get('/date',function (){
   return  date("Y-m-d H:m:s"); 
});


Route::post('buscador',function (Request $request){

$busqueda=$request->input('terminos');
    
    /*
    
    causante de error si queremos hacer range
    
    Documento::reindex();
    Documento::rebuildMapping();*/
    Documento::addAllToIndex();
    $client = ClientBuilder::create()->build();
    /*if(Documento::mappingExists()){
             Documento::getMapping();
    }*/
    $params = array(
        'index' => 'my_custom_index_name',
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

$params = [
    'index' => 'my_custom_index_name',
    'type'  => 'documento',
    'body' => [
        'query' => [
            'bool' => [
                "must" => [
                    ["match"=>['contenido'=> 'procesos']],
                    [ "range" => [ "created_at" => ["gte" => '2014-01-01 00:00:00', "format" => "yyyy-MM-dd HH:mm:ss" ]]]
                    
                ]
                
            ]
        ]
    ]
];

//   [ "range" => [ "created_at" => ["gte" => '2014-01-01 00:00:00', "format" => "yyyy-MM-dd HH:mm:ss" ]]] 
  
    

/*$query = [
    'multi_match' => [
        'query' => $busqueda, 
        'fuzziness' => 'AUTO',
        'fields' => ['titulo^3', 'contenido'],
    ],
];
$params = [
    'index' => 'my_custom_index_name',
    'type'  => 'documento',
    'body' => [
        'query' => $query
    ]
];*/

/*
$params['body']['query']['match']['contenido'] = 'procesos';*/

$collection = Documento::hydrateElasticsearchResult($client->search($params));
return $collection;
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