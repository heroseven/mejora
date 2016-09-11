<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchEngine extends Controller
{


    public function search(){



        $documentos=Documento::all();
        //return $documentos;
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



}
