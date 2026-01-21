<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CleanUpSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan seluruh cache aplikasi, view, route, dan config.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembersihan sistem...');

        $this->call('cache:clear');
        $this->info('Application cache cleared.');

        $this->call('route:clear');
        $this->info('Route cache cleared.');

        $this->call('config:clear');
        $this->info('Configuration cache cleared.');

        $this->call('view:clear');
        $this->info('Compiled views cleared.');

        // Opsional: optimize:clear melakukan hampir semua hal di atas
        // $this->call('optimize:clear');

        $this->info('Sistem berhasil dibersihkan dan siap digunakan kembali!');
    }
}