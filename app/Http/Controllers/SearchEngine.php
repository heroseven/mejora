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
//        return $documentos;
        //cada posición representa un termino registrado en el diccionario
        $dictionary = array();
        $docCount = array();

        //se recorren todos los docuemntos
        foreach($documentos as $docID => $doc) {
            $terms = explode(' ', $doc);
            //arreglo donde cada posición representa la cantidad de terminos de un doc específico
            $docCount[$docID] = count($terms);

            //se recorren los terminos que posee un documento
            foreach($terms as $term) {

                //si no cuenta con el termino en el diccionario se crea estructura
                if(!isset($dictionary[$term])) {
                    $dictionary[$term] = array('df' => 0, 'postings' => array());
                }

                //si no hay nada definido en la la posicion docid de posting
                if(!isset($dictionary[$term]['postings'][$docID])) {

                    //frecuencia del documento
                    $dictionary[$term]['df']++;

                    //se almacena la frecuencia de terminos que aparecen en un documento determinao
                    $dictionary[$term]['postings'][$docID] = array('tf' => 0);
                }

                //si cuenta con el termino en el diccionario va
                //
                    $dictionary[$term]['postings'][$docID]['tf']++;
            }
        }
        //al final se retorna cuantos terminos hay para cada docuemnto.
        // y el diccionario con la
        return array('docCount' => $docCount, 'dictionary' => $dictionary);
    }



}
