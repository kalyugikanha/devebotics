<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $members = TeamMember::query()->active()->with('photo')->orderByDesc('is_leadership')->ordered()->get();

        abort_if($members->isEmpty(), 404);

        $seo = SeoData::make('Our Team', 'Meet the engineers, designers and consultants behind Softec Ai Solution.')
            ->canonical(route('team.index'))
            ->breadcrumbs([['Our team', null]]);

        return view('team.index', [
            'leadership' => $members->where('is_leadership', true)->values(),
            'members' => $members->where('is_leadership', false)->values(),
            'seo' => $seo,
        ]);
    }
}
