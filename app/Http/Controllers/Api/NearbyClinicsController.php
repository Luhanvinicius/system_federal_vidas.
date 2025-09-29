<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Clinic;

class NearbyClinicsController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'cep' => ['required','regex:/^\d{5}-?\d{3}$/'],
            'radius_km' => ['nullable','numeric','min:0.5','max:50'],
        ]);

        $cep = preg_replace('/\D/', '', $request->cep);
        $radiusKm = (float) ($request->input('radius_km', 20));

        $apiKey = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');
        if (!$apiKey) {
            return response()->json(['message' => 'GOOGLE_MAPS_API_KEY não configurada.'], 500);
        }

        $geo = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $cep,
            'region'  => 'br',
            'key'     => $apiKey,
        ]);

        if (!$geo->ok()) {
            return response()->json(['message' => 'Falha ao consultar Geocoding'], 502);
        }

        $json = $geo->json();
        if (($json['status'] ?? '') !== 'OK') {
            return response()->json(['message' => 'CEP não geocodificado', 'google_status' => $json['status'] ?? null], 404);
        }

        $loc = $json['results'][0]['geometry']['location'] ?? null;
        if (!$loc) {
            return response()->json(['message' => 'Localização não encontrada'], 404);
        }

        $lat = $loc['lat'];
        $lng = $loc['lng'];

        $earth = 6371;
        $haversine = sprintf(
            '(%d * acos(cos(radians(%f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%f)) + sin(radians(%f)) * sin(radians(latitude))))',
            $earth, $lat, $lng, $lat
        );

        $clinics = Clinic::selectRaw("clinics.*, {$haversine} AS distance_km")
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->where('active', true)
            ->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km', 'asc')
            ->limit(3)
            ->get();

        return response()->json([
            'origin' => ['lat' => $lat, 'lng' => $lng],
            'radius_km' => $radiusKm,
            'count' => $clinics->count(),
            'clinics' => $clinics,
        ]);
    }
}
