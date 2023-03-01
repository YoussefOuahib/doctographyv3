<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (Permission::pluck('name') as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->roles()->whereHas('permissions', function ($q) use ($permission) {
                    $q->where('name', $permission);
                })->count() > 0;
            });
        }

        $this->registerPolicies();

        //
    }
}
