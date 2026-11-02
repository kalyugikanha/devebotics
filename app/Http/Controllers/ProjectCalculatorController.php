<?php

namespace App\Http\Controllers;

use App\Services\ProjectEstimatorService;
use App\Support\Seo\SeoData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectCalculatorController extends Controller
{
    public function index(): View
    {
        $seo = SeoData::make(
            'AI Project Cost & Scope Calculator',
            'Estimate your software development, website, mobile app, or AI project cost in seconds. Transparent baseline pricing starting from ₹5,000 with real-time technical scope breakdowns.'
        )
            ->canonical(route('calculator.index'))
            ->breadcrumbs([
                ['Home', route('home')],
                ['Project Calculator', null],
            ]);

        return view('calculator.index', [
            'seo' => $seo,
            'whatsappNumber' => '916397013739',
        ]);
    }

    public function calculate(Request $request, ProjectEstimatorService $estimator): JsonResponse
    {
        $validated = $request->validate([
            'idea_prompt' => ['nullable', 'string', 'max:1500'],
            'project_type' => ['nullable', 'string', 'max:50'],
            'scale' => ['nullable', 'string', 'max:50'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:50'],
            'urgency' => ['nullable', 'string', 'max:30'],
        ]);

        $result = $estimator->estimate($validated);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
