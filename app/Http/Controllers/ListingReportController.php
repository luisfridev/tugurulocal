<?php

namespace App\Http\Controllers;

use App\Actions\Listings\SubmitListingReport;
use App\Http\Requests\StoreListingReportRequest;
use App\Queries\PublicListingQuery;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class ListingReportController extends Controller
{
    public function create(string $listing, PublicListingQuery $query): Response
    {
        abort_unless(SubmitListingReport::available(), 404);
        $record = $query->find($listing);
        $token = Crypt::encryptString($record->id.'|'.now()->timestamp.'|'.session()->getId());

        return response()->view('listings.report', ['listing' => $record, 'token' => $token])->header('X-Robots-Tag', 'noindex')->header('Cache-Control', 'private, no-store');
    }

    public function store(StoreListingReportRequest $request, string $listing, PublicListingQuery $query, SubmitListingReport $submit): RedirectResponse
    {
        $record = $query->find($listing);
        try {
            $token = explode('|', Crypt::decryptString($request->validated('form_token')));
        } catch (DecryptException) {
            throw ValidationException::withMessages(['form_token' => 'Abre de nuevo el formulario para enviar el reporte.']);
        }
        if (count($token) !== 3 || (int) $token[0] !== $record->id || $token[2] !== session()->getId() || now()->timestamp - (int) $token[1] < 3 || now()->timestamp - (int) $token[1] > 7200) {
            throw ValidationException::withMessages(['form_token' => 'Espera unos segundos o vuelve a abrir el formulario.']);
        }
        $submit->handle($record, $request->safe()->only(['field', 'explanation']));

        return redirect()->route('listings.report', ['listing' => $record->slug])->with('report_sent', true);
    }
}
