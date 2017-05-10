<?php

use Faker\Factory as Faker;
use App\Models\Documento;
use App\Repositories\DocumentoRepository;

trait MakeDocumentoTrait
{
    /**
     * Create fake instance of Documento and save it in database
     *
     * @param array $documentoFields
     * @return Documento
     */
    public function makeDocumento($documentoFields = [])
    {
        /** @var DocumentoRepository $documentoRepo */
        $documentoRepo = App::make(DocumentoRepository::class);
        $theme = $this->fakeDocumentoData($documentoFields);
        return $documentoRepo->create($theme);
    }

    /**
     * Get fake instance of Documento
     *
     * @param array $documentoFields
     * @return Documento
     */
    public function fakeDocumento($documentoFields = [])
    {
        return new Documento($this->fakeDocumentoData($documentoFields));
    }

    /**
     * Get fake data of Documento
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentoData($documentoFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'titulo' => $fake->word,
            'contenido' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $documentoFields);
    }
}
