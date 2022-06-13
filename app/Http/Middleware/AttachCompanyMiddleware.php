<?php

namespace App\Http\Middleware;

use App\Enums\AppHeaders;
use App\Exceptions\BreezeException;
use App\Exceptions\BreezeNotFoundException;
use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

class AttachCompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $client = Client::with('companies')->find(base64_decode($request->header(AppHeaders::X_APP_ID->value)));

        if (!$client) {
            throw new BreezeNotFoundException(__('auth.client_not_found'));
        }

        $clientCompanies = $client->companyIds();
        $user = $request->user();

        $user?->load('company.client');

        if (!in_array($user->company->id, $clientCompanies)) {
            throw new BreezeException(__('general.not_allowed'));
        }


        $request->merge(['company' => $user->company]);

        return $next($request);
    }
}
