<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user) {
        return $user->role_id === 1; // seul l'Administ peut voir tous les utilisateur
    }

    public function view(User $user, User $model) {
        return $user->id === $model->id || $user->role_id === 1; // seul l'Administ ou l'utilisateur lui même peut voir son propre profil
    }

    public function create(User $user) {
        return $user->role_id === 2; // seul le boutiquier peut créer un utilisateur client
    }

    public function update(User $user, User $model) {
        return $user->id === $model->id || $user->role_id === 1; // seul l'Administ ou l'utilisateur lui même peut modifier son propre profil
    }

    public function delete(User $user, User $model) {
        return $user->role_id === 1; // seul l'Administ peut supprimer un utilisateur
    }

}
