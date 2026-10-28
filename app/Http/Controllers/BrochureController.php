<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\RedirectResponse;

/**
 * Stable URL for the downloadable company profile configured in Settings.
 */
class BrochureController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $media = Media::query()->find(settings('company.brochure_media_id'));

        abort_if($media === null, 404);

        return redirect()->away($media->url());
    }
}
