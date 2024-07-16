<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use L5Swagger\Generator;

class GenerateSwaggerJson extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Manually generate Swagger JSON';

    public function handle(Generator $generator)
    {
        $this->info('Generating Swagger JSON...');
        $generator->generateDocs();
        $this->info('Swagger JSON generated successfully.');

        $this->info('Starting Swagger documentation generation...');

        // Run the L5-Swagger generate command
        $this->call('l5-swagger:generate');
    
        // Move the generated file to the desired location
        $source = storage_path('api-docs/api-docs.json');
        $destination = public_path('docs/api-docs.json');
        
        if (file_exists($source)) {
            copy($source, $destination);
            $this->info("File copied to: $destination");
        } else {
            $this->error("Source file not found: $source");
        }
    }
}