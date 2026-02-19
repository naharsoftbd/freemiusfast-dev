<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin
                            {--name= : Admin name}
                            {--email= : Admin email}
                            {--password= : Admin password}
                            {--force : Update existing user without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update an admin user and assign the Admin role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = (string) ($this->option('name') ?: $this->ask('Admin name'));
        $email = (string) ($this->option('email') ?: $this->ask('Admin email'));
        $password = (string) ($this->option('password') ?: $this->secret('Admin password'));

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser && ! $this->option('force')) {
            $shouldContinue = $this->confirm('A user with this email already exists. Update name/password and ensure Admin role?', false);
            if (! $shouldContinue) {
                $this->warn('Command cancelled.');

                return self::FAILURE;
            }
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'status' => true,
            ]
        );

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $user->assignRole($adminRole);

        $action = $existingUser ? 'updated' : 'created';

        $this->info("Admin user {$action} successfully.");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");

        File::put(storage_path('installed'), now()->toDateTimeString());
        
        return self::SUCCESS;
    }
}
