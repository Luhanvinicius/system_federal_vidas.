INSTRUÇÕES (Clínicas próximas por CEP - raio 3km)

1) Copie estes arquivos para o projeto.
2) Configure sua chave no .env:
   GOOGLE_MAPS_API_KEY=SEU_TOKEN_DO_GOOGLE
   E opcionalmente em config/services.php:
   'google' => ['maps_api_key' => env('GOOGLE_MAPS_API_KEY')],

3) Rode migrations e seed:
   php artisan migrate
   php artisan db:seed --class=Database\Seeders\ClinicsSeeder

4) Suba o servidor e acesse /appointments/create.
   Ao digitar o CEP e sair do campo, uma lista de clínicas próximas aparecerá.

API criada:
GET /api/clinics/nearby?cep=60060130&radius_km=3
