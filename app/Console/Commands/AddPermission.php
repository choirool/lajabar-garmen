<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new permission and add it to super admin role';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->option('name');
        if (!$name) {
            $name = $this->ask('Input permssion name?');
        }

        if ($name) {
            $permissionName = Str::of($name)->replace(' ', '-');

            if (Permission::whereName($permissionName)->count()) {
                $this->info('Permission ' . $name . ' already exists.');
            } else {
                $this->info('Creating permission...');
                $permission = Permission::create([
                    'display_name' => ucfirst($name),
                    'name' => $permissionName
                ]);

                $superAdminRole = Role::where('name', 'super-admin')->first();

                $this->info('Adding to super admin role...');
                $superAdminRole->attachPermission($permission);

                $this->info('Done...');
            }
        }
    }
}
