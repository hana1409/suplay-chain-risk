<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\Port;

class ImportPorts extends Command
{
    protected $signature = 'ports:import';

    protected $description = 'Import World Port Index CSV';

    public function handle()
    {
        $path = storage_path('app/imports/world_port_index.csv');

        if (!file_exists($path)) {
            $this->error('CSV file not found!');
            return;
        }

        $file = fopen($path, 'r');

        $header = fgetcsv($file);

        $totalImported = 0;
        $totalSkipped = 0;

        while (($row = fgetcsv($file)) !== false) {

            $data = array_combine($header, $row);

            if (!$data) {
                continue;
            }

            // Cari negara berdasarkan nama
            $country = Country::whereRaw(
                "LOWER(country_name)=?",
                [strtolower(trim($data['Country Code']))]
            )->first();

            if (!$country) {

                $this->warn("Country not found : ".$data['Country Code']);

                $totalSkipped++;

                continue;
            }

            // Skip jika koordinat kosong
            if (
                empty($data['Latitude']) ||
                empty($data['Longitude'])
            ) {

                $this->warn("Coordinate missing : ".$data['Main Port Name']);

                $totalSkipped++;

                continue;
            }

            Port::updateOrCreate(

                [
                    'port_name' => trim($data['Main Port Name'])
                ],

                [

                    'country_id' => $country->id,

                    'city' => null,

                    'port_type' => $data['Harbor Type'] ?: 'Sea Port',

                    'latitude' => $data['Latitude'],

                    'longitude' => $data['Longitude'],

                    'status' => 'Active'

                ]

            );

            $this->info("Imported : ".$data['Main Port Name']);

            $totalImported++;
        }

        fclose($file);

        $this->newLine();

        $this->info("==============================");
        $this->info("Port Import Completed!");
        $this->info("Imported : ".$totalImported." Ports");
        $this->warn("Skipped : ".$totalSkipped." Ports");
        $this->info("==============================");
    }
}