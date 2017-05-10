<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentoRequest;
use App\Http\Requests\UpdateDocumentoRequest;
use App\Repositories\DocumentoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentoController extends AppBaseController
{
    /** @var  DocumentoRepository */
    private $documentoRepository;

    public function __construct(DocumentoRepository $documentoRepo)
    {
        $this->documentoRepository = $documentoRepo;
    }

    /**
     * Display a listing of the Documento.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentoRepository->pushCriteria(new RequestCriteria($request));
        $documentos = $this->documentoRepository->all();

        return view('documentos.index')
            ->with('documentos', $documentos);
    }

    /**
     * Show the form for creating a new Documento.
     *
     * @return Response
     */
    public function create()
    {
        return view('documentos.create');
    }

    /**
     * Store a newly created Documento in storage.
     *
     * @param CreateDocumentoRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentoRequest $request)
    {
        $input = $request->all();

        $documento = $this->documentoRepository->create($input);

        Flash::success('Documento saved successfully.');

        return redirect(route('documentos.index'));
    }

    /**
     * Display the specified Documento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documento = $this->documentoRepository->findWithoutFail($id);

        if (empty($documento)) {
            Flash::error('Documento not found');

            return redirect(route('documentos.index'));
        }

        return view('documentos.show')->with('documento', $documento);
    }

    /**
     * Show the form for editing the specified Documento.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documento = $this->documentoRepository->findWithoutFail($id);

        if (empty($documento)) {
            Flash::error('Documento not found');

            return redirect(route('documentos.index'));
        }

        return view('documentos.edit')->with('documento', $documento);
    }

    /**
     * Update the specified Documento in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentoRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentoRequest $request)
    {
        $documento = $this->documentoRepository->findWithoutFail($id);

        if (empty($documento)) {
            Flash::error('Documento not found');

            return redirect(route('documentos.index'));
        }

        $documento = $this->documentoRepository->update($request->all(), $id);

        Flash::success('Documento updated successfully.');

        return redirect(route('documentos.index'));
    }

    /**
     * Remove the specified Documento from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documento = $this->documentoRepository->findWithoutFail($id);

        if (empty($documento)) {
            Flash::error('Documento not found');

            return redirect(route('documentos.index'));
        }

        $this->documentoRepository->delete($id);

        Flash::success('Documento deleted successfully.');

        return redirect(route('documentos.index'));
    }
}
