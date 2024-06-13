<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shape;
use geoPHP;

class ImportShapefile extends Command
{
    protected $signature = 'import:shapefile {file}';
    protected $description = 'Import shapefile into database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $file = $this->argument('file');
        $shapefile = geoPHP::load(file_get_contents($file), 'shp');

        foreach ($shapefile->features as $feature) {
            Shape::create([
                'name' => $feature->attributes['name'], // Sesuaikan dengan atribut pada shapefile
                'geometry' => $feature->geometry->out('wkt')
            ]);
        }

        $this->info('Shapefile imported successfully.');
    }
}


