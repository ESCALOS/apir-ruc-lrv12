<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchCompanyByNameRequest;
use App\Http\Requests\SearchCompanyByRucRequest;
use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
{
    $query = Company::query();

    // Si se pasa RUC, limpiamos y comparamos de forma exacta
    if ($request->filled('ruc')) {
        $ruc = trim($request->ruc);

        // Solo si tiene exactamente 11 dígitos numéricos
        if (preg_match('/^\d{11}$/', $ruc)) {
            $query->where('ruc', $ruc);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El RUC debe tener exactamente 11 dígitos numéricos.',
            ], 422);
        }
    }

    $companies = $query->paginate(15);

    return (new CompanyCollection($companies))
        ->additional(['success' => true])
        ->response();
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
    public function searchByRuc(SearchCompanyByRucRequest $request): JsonResponse
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

    public function searchPerson(SearchCompanyByNameRequest $request)
    {
        return $this->searchByType($request, '10');
    }

    public function searchBusiness(SearchCompanyByNameRequest $request)
    {
        return $this->searchByType($request, '20');
    }

    private function searchByType(SearchCompanyByNameRequest $request, string $type)
    {
        $input = $request->get('query');

        if (!$input) {
            return response()->json(['error' => 'Empty query'], 422);
        }

        $normalized = $this->normalizeName($input);

        $company = DB::table('companies')
            ->select('ruc', 'name')
            ->where('type', $type)
            ->where('name', $normalized)
            ->first();

        return response()->json($company ?? []);
    }

    private function normalizeName(string $input): string
    {
        $map = [
            '/\bS[\.\s]?A[\.\s]?C\b/i'     => 'S.A.C.',
            '/\bE[\.\s]?I[\.\s]?R[\.\s]?L\b/i' => 'E.I.R.L.',
            '/\bS[\.\s]?R[\.\s]?L\b/i'     => 'S.R.L.',
            '/\bS[\.\s]?A\b/i'             => 'S.A.',
        ];

        $normalized = strtoupper($input);

        foreach ($map as $pattern => $replacement) {
            $normalized = preg_replace($pattern, $replacement, $normalized);
        }

        return preg_replace('/\s+/', ' ', trim($normalized));
    }
}