<?php

namespace App\Repositories;

use App\Models\Documento;
use InfyOm\Generator\Common\BaseRepository;

class DocumentoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'titulo',
        'contenido'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Documento::class;
    }
}
