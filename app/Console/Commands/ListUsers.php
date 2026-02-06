<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list {role?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listar usuarios por rol';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleSlug = $this->argument('role');
        
        $query = User::with('role');
        
        if ($roleSlug) {
            $query->whereHas('role', function($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            });
        }
        
        $users = $query->get();
        
        $this->info("Usuarios encontrados: " . $users->count());
        $this->line("");
        
        foreach ($users as $user) {
            $this->line($user->email . " - " . $user->name . " (" . $user->role->nombre . ")");
        }
    }
}
