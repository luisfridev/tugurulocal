<?php

namespace App\Http\Controllers;

use App\Actions\Listings\SubmitListingReport;
use App\Http\Requests\StoreListingReportRequest;
use App\Queries\PublicListingQuery;
use App\Support\PublicFormToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ListingReportController extends Controller
{
    public function create(string $listing, PublicListingQuery $query, PublicFormToken $tokens): Response
    {
        $record = $query->find($listing);
        $token = $tokens->make((string) $record->id);

        return response()->view('listings.report', ['listing' => $record, 'token' => $token])->header('X-Robots-Tag', 'noindex')->header('Cache-Control', 'private, no-store');
    }

    public function store(StoreListingReportRequest $request, string $listing, PublicListingQuery $query, SubmitListingReport $submit, PublicFormToken $tokens): RedirectResponse
    {
        $record = $query->find($listing);
        $tokens->validate($request->validated('form_token'), (string) $record->id);
        $submit->handle($record, $request->safe()->only(['field', 'explanation']));

        return redirect()->route('listings.report', ['listing' => $record->slug])->with('report_sent', true);
    }
}
