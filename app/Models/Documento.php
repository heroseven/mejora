<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;


class Documento extends Model {
	
	use ElasticquentTrait;

	protected $table = 'documento';
	protected $fillable= ['id','contenido','titulo'];
	public $timestamps = true;


  
	public function terminos()
	{
		return $this->hasMany('App\Modelos\Terminos_de_documento', 'id');
	}
	protected $indexSettings = [
	   /*'analysis' => [
            
            'filter' => [
                'my_snow' => [
                    'type'=>'snowball',
                    'lenguage'=>'spanish'
                ],
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
                        'lowercase',
                        'my_snow',
                        'spanish_stop',"asciifolding"
                    ],
                ],
            ],
        ],
    ];*/
        'analysis' => [
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
                'default' => [
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
    ];

protected $mappingProperties = array(
    'titulo' => [
      'type' => 'string',
      "analyzer" => "default",
    ],
    'contenido' => [
      'type' => 'string',
      "analyzer" => 'default',
    ],
    
  );
    
    
}