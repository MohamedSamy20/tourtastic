<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_email');
            $table->string('api_password');
            $table->string('agency_code');
            $table->string('api_base_url');
            $table->boolean('enabled')->default(false);
            $table->string('service_class');
            $table->timestamps();
        });
        
        // Insert default Seeru provider
        DB::table('flight_providers')->insert([
            'name' => 'Seeru Flights',
            'api_email' => env('SEERU_API_EMAIL', 'Wesambadr2015@gmail.com'),
            'api_password' => env('SEERU_API_PASSWORD', 'Wesam@2025'),
            'agency_code' => env('SEERU_API_AGENCY_CODE', 'TOURTASTIC'),
            'api_base_url' => env('SEERU_API_ENDPOINT', 'https://sandbox-api.seeru.travel/v1/flights'),
            'enabled' => true,
            'service_class' => 'App\\Services\\Flights\\SeeruFlightsService',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_providers');
    }
}
