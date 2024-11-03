<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ExternalAccess;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ExternalApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the API key is present in the request header
        $apiKey = $request->header('CEA-API-KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'API key is missing in the header. Please provide a valid CEA-API-KEY in the header.'], 400);
        }

        // Retrieve record from the database with date range check
        $externalAccess = ExternalAccess::where('api_key', $apiKey)->first();

        // Check if the record exists
        if (!$externalAccess) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        // Check if the request IP is in the whitelisted IPs (if the whitelist is not empty)
        $requestIp = $request->ip();
        Log::info('Request IP: ' . $requestIp);
        if (!empty($externalAccess->whitelisted_ips) && !in_array(
            $requestIp,
            $externalAccess->whitelisted_ips
        )) {
            return response()->json(['error' => 'Your IP is not authorized to access this resource.'], 403);
        }

        // Attach external access record to request for controller use
        $request->merge(['externalAccess' => $externalAccess]);

        return $next($request);
    }
}
