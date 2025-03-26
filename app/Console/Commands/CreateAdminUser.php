<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an admin user with predefined credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $existingAdmin = User::where('email', 'admin@karen-culture.com')->first();

        if ($existingAdmin) {
            $this->info('Admin user already exists.');
            
            // Update the existing admin to ensure it has admin privileges
            $existingAdmin->usertype = 'admin';
            $existingAdmin->save();
            
            $this->info('Admin privileges have been set for the existing admin user.');
            $this->info('Email: admin@karen-culture.com');
            $this->info('Password: (unchanged)');
            
            return;
        }

        // Create a new admin user
        $user = new User();
        $user->name = 'Admin User';
        $user->email = 'admin@karen-culture.com';
        $user->password = Hash::make('admin123');
        $user->usertype = 'admin';
        $user->save();

        $this->info('Admin user created successfully!');
        $this->info('Email: admin@karen-culture.com');
        $this->info('Password: admin123');
        $this->info('Please login and change the password immediately for security reasons.');
    }
}
