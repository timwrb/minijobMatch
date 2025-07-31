<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add rs_code column to states table
        Schema::table('states', function (Blueprint $table) {
            $table->string('rs_code', 2)->after('iso_code')->index();
        });

        // Clear existing data and populate with all 16 German Bundesländer
        // Handle foreign key constraints for different databases
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('states')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            // For SQLite and other databases, delete instead of truncate
            DB::table('states')->delete();
        }

        $germanStates = [
            ['name' => 'Schleswig-Holstein', 'iso_code' => 'SH', 'rs_code' => '01', 'country_iso_code' => 'DE'],
            ['name' => 'Hamburg', 'iso_code' => 'HH', 'rs_code' => '02', 'country_iso_code' => 'DE'],
            ['name' => 'Niedersachsen', 'iso_code' => 'NI', 'rs_code' => '03', 'country_iso_code' => 'DE'],
            ['name' => 'Bremen', 'iso_code' => 'HB', 'rs_code' => '04', 'country_iso_code' => 'DE'],
            ['name' => 'Nordrhein-Westfalen', 'iso_code' => 'NW', 'rs_code' => '05', 'country_iso_code' => 'DE'],
            ['name' => 'Hessen', 'iso_code' => 'HE', 'rs_code' => '06', 'country_iso_code' => 'DE'],
            ['name' => 'Rheinland-Pfalz', 'iso_code' => 'RP', 'rs_code' => '07', 'country_iso_code' => 'DE'],
            ['name' => 'Baden-Württemberg', 'iso_code' => 'BW', 'rs_code' => '08', 'country_iso_code' => 'DE'],
            ['name' => 'Bayern', 'iso_code' => 'BY', 'rs_code' => '09', 'country_iso_code' => 'DE'],
            ['name' => 'Saarland', 'iso_code' => 'SL', 'rs_code' => '10', 'country_iso_code' => 'DE'],
            ['name' => 'Berlin', 'iso_code' => 'BE', 'rs_code' => '11', 'country_iso_code' => 'DE'],
            ['name' => 'Brandenburg', 'iso_code' => 'BB', 'rs_code' => '12', 'country_iso_code' => 'DE'],
            ['name' => 'Mecklenburg-Vorpommern', 'iso_code' => 'MV', 'rs_code' => '13', 'country_iso_code' => 'DE'],
            ['name' => 'Sachsen', 'iso_code' => 'SN', 'rs_code' => '14', 'country_iso_code' => 'DE'],
            ['name' => 'Sachsen-Anhalt', 'iso_code' => 'ST', 'rs_code' => '15', 'country_iso_code' => 'DE'],
            ['name' => 'Thüringen', 'iso_code' => 'TH', 'rs_code' => '16', 'country_iso_code' => 'DE'],
        ];

        foreach ($germanStates as $state) {
            DB::table('states')->insert($state);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn('rs_code');
        });
    }
};
