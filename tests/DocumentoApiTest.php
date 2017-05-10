<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentoApiTest extends TestCase
{
    use MakeDocumentoTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumento()
    {
        $documento = $this->fakeDocumentoData();
        $this->json('POST', '/api/v1/documentos', $documento);

        $this->assertApiResponse($documento);
    }

    /**
     * @test
     */
    public function testReadDocumento()
    {
        $documento = $this->makeDocumento();
        $this->json('GET', '/api/v1/documentos/'.$documento->id);

        $this->assertApiResponse($documento->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumento()
    {
        $documento = $this->makeDocumento();
        $editedDocumento = $this->fakeDocumentoData();

        $this->json('PUT', '/api/v1/documentos/'.$documento->id, $editedDocumento);

        $this->assertApiResponse($editedDocumento);
    }

    /**
     * @test
     */
    public function testDeleteDocumento()
    {
        $documento = $this->makeDocumento();
        $this->json('DELETE', '/api/v1/documentos/'.$documento->iidd);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentos/'.$documento->id);

        $this->assertResponseStatus(404);
    }
}
