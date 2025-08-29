<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'vendedor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        // Un usuario puede ver un producto si el producto pertenece a la tienda de su admin.
        // Primero, encontramos la tienda del usuario.
        $userShopId = $user->shop->id ?? null;

        // Luego, verificamos si el producto pertenece a esa tienda.
        return $product->shop_id === $userShopId;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'vendedor']) && $user->shop()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        $userShopId = $user->shop->id ?? null;

        return $product->shop_id === $userShopId;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->hasRole('admin')) {
            $userShopId = $user->shop->id ?? null;
            return $product->shop_id === $userShopId;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
