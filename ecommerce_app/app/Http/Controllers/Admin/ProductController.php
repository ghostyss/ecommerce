<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller {

    /**
     * Display a listing of the products for the authenticated user's shop.
     */
    public function index() {
        // Autorizamos al usuario a ver cualquier producto de su tienda.
        // La política se encarga de filtrar por rol.
        $this->authorize('viewAny', Product::class);

        // Obtenemos la tienda del usuario autenticado.
        $shop = Auth::user()->shop;

        // Si el usuario no tiene una tienda, no hay productos que mostrar.
        if (!$shop) {
            return view('admin.products.index', ['products' => collect([])]);
        }

        // Cargamos los productos de esa tienda.
        $products = $shop->products;

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create() {
        $this->authorize('create', Product::class);

        // ... devolver la vista del formulario
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request) {
        $this->authorize('create', Product::class);

        // ... lógica de validación del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
                // ... otras reglas de validación
        ]);

        // Asignamos el producto a la tienda del usuario autenticado
        $shop = Auth::user()->shop;
        if (!$shop) {
            return back()->with('error', 'No tienes una tienda para agregar productos.');
        }

        $product = $shop->products()->create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            // ... otros campos
            'user_id' => Auth::id(), // Guardamos quién creó el producto
        ]);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product) {
        // Autorizamos la actualización. Esto fallará si el producto no pertenece a la tienda del usuario.
        $this->authorize('update', $product);

        // ... lógica de validación
        // ... lógica para actualizar el producto
        $product->update($request->validated());

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product) {
        // Autorizamos la eliminación. La política se encarga de verificar el rol y la propiedad.
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado.');
    }
}
