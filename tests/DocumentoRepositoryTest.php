<?php

use App\Models\Documento;
use App\Repositories\DocumentoRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentoRepositoryTest extends TestCase
{
    use MakeDocumentoTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentoRepository
     */
    protected $documentoRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentoRepo = App::make(DocumentoRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumento()
    {
        $documento = $this->fakeDocumentoData();
        $createdDocumento = $this->documentoRepo->create($documento);
        $createdDocumento = $createdDocumento->toArray();
        $this->assertArrayHasKey('id', $createdDocumento);
        $this->assertNotNull($createdDocumento['id'], 'Created Documento must have id specified');
        $this->assertNotNull(Documento::find($createdDocumento['id']), 'Documento with given id must be in DB');
        $this->assertModelData($documento, $createdDocumento);
    }

    /**
     * @test read
     */
    public function testReadDocumento()
    {
        $documento = $this->makeDocumento();
        $dbDocumento = $this->documentoRepo->find($documento->id);
        $dbDocumento = $dbDocumento->toArray();
        $this->assertModelData($documento->toArray(), $dbDocumento);
    }

    /**
     * @test update
     */
    public function testUpdateDocumento()
    {
        $documento = $this->makeDocumento();
        $fakeDocumento = $this->fakeDocumentoData();
        $updatedDocumento = $this->documentoRepo->update($fakeDocumento, $documento->id);
        $this->assertModelData($fakeDocumento, $updatedDocumento->toArray());
        $dbDocumento = $this->documentoRepo->find($documento->id);
        $this->assertModelData($fakeDocumento, $dbDocumento->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumento()
    {
        $documento = $this->makeDocumento();
        $resp = $this->documentoRepo->delete($documento->id);
        $this->assertTrue($resp);
        $this->assertNull(Documento::find($documento->id), 'Documento should not exist in DB');
    }
}
