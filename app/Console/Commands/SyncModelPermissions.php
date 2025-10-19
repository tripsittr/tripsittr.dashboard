<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncModelPermissions extends Command
{
    protected $signature = 'permissions:sync {--dry : List changes without applying}';
    protected $description = 'Generate canonical CRUD(+restore,forceDelete) permissions per model and assign to Admin role';

    protected array $verbs = ['view','create','update','delete','restore','force-delete'];

    protected array $exclude = [
        'ActivityLog','Action','UserAction','Permission','Role','MediaUpload'
    ];

    public function handle(): int
    {
        $dry = $this->option('dry');
        $modelFiles = glob(app_path('Models/*.php'));
        $created = 0; $existing = 0;
        foreach ($modelFiles as $file) {
            $name = basename($file,'.php');
            if (in_array($name,$this->exclude)) continue;
            $slug = Str::kebab(Str::snake($name,'-')); // e.g. OrderItem -> order-item
            foreach ($this->verbs as $verb) {
                $permName = $slug.'.'.$verb;
                if (Permission::where('name',$permName)->exists()) {
                    $existing++;
                    continue;
                }
                if ($dry) {
                    $this->line("Would create permission: {$permName}");
                } else {
                    Permission::firstOrCreate(['name'=>$permName,'guard_name'=>'web']);
                    $created++;
                }
            }
        }

        if(!$dry) {
            $admin = Role::firstOrCreate(['name'=>'Admin','guard_name'=>'web']);
            $all = Permission::all();
            $admin->syncPermissions($all->pluck('name')); // Ensure Admin always has all
        }

        $this->info("Permissions sync complete. Created: {$created}, Existing: {$existing}");
        if ($dry) $this->warn('Dry run only; re-run without --dry to apply.');
        return Command::SUCCESS;
    }
}
