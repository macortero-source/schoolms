<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:seed {--fresh : Run fresh migration before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the school management system database with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->warn('⚠️  This will delete all existing data!');
            
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('Operation cancelled.');
                return;
            }

            $this->info('Running fresh migration...');
            Artisan::call('migrate:fresh');
            $this->info('✅ Migration completed!');
        }

        $this->info('🌱 Seeding database...');
        Artisan::call('db:seed');
        
        $this->newLine();
        $this->info('✅ Database seeded successfully!');
        $this->newLine();
        
        $this->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@school.com', 'password'],
                ['Teacher', 'sarah.teacher@school.com', 'password'],
                ['Student', 'student1@school.com', 'password'],
                ['Parent', 'parent1@school.com', 'password'],
            ]
        );
    }
}