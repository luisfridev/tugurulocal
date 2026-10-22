<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEditorialAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('filament.admin.auth.login');
        }
        abort_unless($request->user()->is_super_admin, 403);
        if (blank($request->user()->getAppAuthenticationSecret())) {
            return redirect()->route('filament.admin.auth.multi-factor-authentication.set-up-required');
        }

        return $next($request);
    }
}
