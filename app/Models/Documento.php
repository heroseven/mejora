<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;


class Documento extends Model {
	
	use ElasticquentTrait;

	protected $table = 'documento';
	protected $fillable= ['id','descripcion','contenido','titulo','created_at'];
	public $timestamps = true;
    
     /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'titulo' => 'string',
        'contenido' => 'string',
        'descripcion'=>'required'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'titulo' => 'required',
        'contenido' => 'required',
        /*'descripcion'=>'required'*/
    ];

  
	public function terminos()
	{
		return $this->hasMany('App\Modelos\Terminos_de_documento', 'id');
	}
	protected $indexSettings = [
	   'analysis' => [
	        //buscar en todos los campos 
            'char_filter' => [
                'replace' => [
                    'type' => 'mapping',
                    'mappings' => [
                        '&=> and '
                    ],
                ],
            ],
            'filter' => [
                //lematizacion de tipo snowball y en español
                'my_snow' => [
                        'type'=>'snowball',
                    'lenguage'=>'spanish'
                ],
                //eliminar palabras vacias
                'spanish_stop'=>[
                    'type'=> 'stop',
                    'stopwords'=>'_spanish_'
                ]
            ],
            'analyzer' => [
                'default2' => [
                    'type' => 'custom',
                    'char_filter' => [
                        'html_strip',
                        'replace',
                    ],
                    'tokenizer' => 'standard',
                    'filter' => [
                        'standard',
                        //convertir todo a minúsculas
                        'lowercase',
                        'my_snow',
                        'spanish_stop',
                        //indexar sin considerar tildes
                        "asciifolding"
                    ],
                ],
            ],
        ],
    ];
      /*  'analysis' => [
            'char_filter' => [
                'replace' => [
                    'type' => 'mapping',
                    'mappings' => [
                        '&=> and '
                    ],
                ],
            ],
            'filter' => [
                'word_delimiter' => [
                    'type' => 'word_delimiter',
                    'split_on_numerics' => false,
                    'split_on_case_change' => true,
                    'generate_word_parts' => true,
                    'generate_number_parts' => true,
                    'catenate_all' => true,
                    'preserve_original' => true,
                    'catenate_numbers' => true,
                ]
            ],
            'analyzer' => [
                'default2' => [
                    'type' => 'custom',
                    'char_filter' => [
                        'html_strip',
                        'replace',
                    ],
                    'tokenizer' => 'whitespace',
                    'filter' => [
                       
                        'word_delimiter',
                        'asciifolding',
                    ],
                ],
            ],
        ],
    ];*/

protected $mappingProperties = array(
    'titulo' => [
      'type' => 'string',
      "analyzer" => "default2"
    ],
    'contenido' => [
      'type' => 'string',
      "analyzer" => 'default2',
    ],
    'descripcion' => [
      'type' => 'string',
      "analyzer" => 'default2',
    ],
    'created_at' => [
      'type' => 'date',
      "format" => "yyyy-MM-dd HH:mm:ss",
      "analyzer" => 'default2',
    ]
    
  );
    
    
}
