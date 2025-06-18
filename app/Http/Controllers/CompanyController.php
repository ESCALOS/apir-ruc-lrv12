<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\SearchCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Company::query();

        // Búsqueda solo por RUC (quitar espacios)
        if ($request->has('ruc')) {
            $searchRuc = str_replace(' ', '', $request->ruc);
            $query->whereRaw('REPLACE(ruc, " ", "") LIKE ?', ['%' . $searchRuc . '%']);
        }

        $companies = $query->paginate(15);

        return (new CompanyCollection($companies))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());

        return (new CompanyResource($company))
            ->additional([
                'success' => true,
                'message' => 'Empresa creada exitosamente'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): JsonResponse
    {
        return (new CompanyResource($company))
            ->additional(['success' => true])
            ->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return (new CompanyResource($company))
            ->additional([
                'success' => true,
                'message' => 'Empresa actualizada exitosamente'
            ])
            ->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empresa eliminada exitosamente'
        ]);
    }

    /**
     * Search companies by RUC
     */
    public function searchByRuc(SearchCompanyRequest $request): JsonResponse
    {
        if (!$request->validateRuc()) {
            return response()->json([
                'success' => false,
                'message' => 'El RUC debe tener exactamente 11 dígitos'
            ], 422);
        }

        $searchRuc = str_replace(' ', '', $request->ruc);
        $companies = Company::whereRaw('REPLACE(ruc, " ", "") LIKE ?', ['%' . $searchRuc . '%'])->get();

        return CompanyResource::collection($companies)
            ->additional(['success' => true])
            ->response();
    }

    /**
     * Search companies by name (razón social)
     */
    public function searchByName(SearchCompanyRequest $request): JsonResponse
    {
        $searchName = $this->normalizeText($request->name);
        
        $companies = Company::whereRaw(
            'UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, ".", ""), "á", "A"), "é", "E"), "í", "I"), "ó", "O"), "ú", "U"), "ñ", "N")) LIKE ?',
            ['%' . $searchName . '%']
        )->get();

        return CompanyResource::collection($companies)
            ->additional(['success' => true])
            ->response();
    }

    /**
     * Normalizar texto removiendo acentos, puntos y convirtiendo a mayúsculas
     */
    private function normalizeText(string $text): string
    {
        $text = trim($text);
        $text = strtoupper($text);
        
        // Primero remover puntos
        $text = str_replace('.', '', $text);
        
        // Luego remover acentos
        $unwanted_array = [
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ā' => 'A', 'Ã' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E', 'Ē' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Ī' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Ō' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U', 'Ū' => 'U',
            'Ñ' => 'N',
            'á' => 'A', 'à' => 'A', 'ä' => 'A', 'â' => 'A', 'ā' => 'A', 'ã' => 'A',
            'é' => 'E', 'è' => 'E', 'ë' => 'E', 'ê' => 'E', 'ē' => 'E',
            'í' => 'I', 'ì' => 'I', 'ï' => 'I', 'î' => 'I', 'ī' => 'I',
            'ó' => 'O', 'ò' => 'O', 'ö' => 'O', 'ô' => 'O', 'ō' => 'O', 'õ' => 'O',
            'ú' => 'U', 'ù' => 'U', 'ü' => 'U', 'û' => 'U', 'ū' => 'U',
            'ñ' => 'N'
        ];
        
        return strtr($text, $unwanted_array);
    }
}