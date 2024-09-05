<?php

namespace App\Providers;
use App\Models\User;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;
use App\Extensions\CustomPersonalAccessTokenFactory;


// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
            
        Passport::tokensCan([

            'admin' => 'Administrateur',
            'boutiquier' => 'Utilisateur',
            // Ajouter les rôles supplémentaires si nécessaire
           
        ]);

/*         Passport::personalAccessTokens()->setFactory(CustomPersonalAccessTokenFactory::class);
 */    
        
        
        
    
    }
}
