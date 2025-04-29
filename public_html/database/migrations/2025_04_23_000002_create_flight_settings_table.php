<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('auto_issue_ticket')->default(false);
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('flight_settings')->insert([
            'auto_issue_ticket' => false,
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
        Schema::dropIfExists('flight_settings');
    }
}
