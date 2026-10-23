<?php

namespace App\Http\Controllers;

use App\Actions\Submissions\SubmitSubmission;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Category;
use App\Models\City;
use App\Support\PublicFormToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class SubmissionController extends Controller
{
    public function create(PublicFormToken $tokens): Response
    {
        return response()->view('submissions.create', [
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'listing_type']),
            'token' => $tokens->make('submission'),
        ]);
    }

    public function store(StoreSubmissionRequest $request, PublicFormToken $tokens, SubmitSubmission $submit): RedirectResponse
    {
        $tokens->validate($request->validated('form_token'), 'submission');
        $city = City::query()->where('slug', 'playa-del-carmen')->where('is_public', true)->firstOrFail();
        $submit->handle($request->safe()->only(['category_id', 'type', 'name', 'address', 'reason', 'maps_url', 'evidence_url', 'email', 'whatsapp', 'instagram', 'credit_consent']) + ['city_id' => $city->id]);

        return redirect()->route('submissions.create')->with('submission_sent', true);
    }
}
