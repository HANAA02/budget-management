<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::orderBy('nom')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie.
     *
     * @param  \App\Http\Requests\Admin\CategoryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche les détails d'une catégorie.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Affiche le formulaire de modification d'une catégorie.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie.
     *
     * @param  \App\Http\Requests\Admin\CategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Vérifier si la catégorie est utilisée dans des budgets
        $usedInBudgets = $category->budgets()->exists();
        
        if ($usedInBudgets) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée dans des budgets.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
    
    /**
     * Active ou désactive une catégorie.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Category $category)
    {
        // Note: Il faudrait ajouter un champ 'active' dans la table categories pour cette fonctionnalité
        // Pour l'instant, on simule avec le pourcentage_defaut
        $category->update([
            'pourcentage_defaut' => ($category->pourcentage_defaut > 0) ? 0 : 5,
        ]);
        
        $status = ($category->pourcentage_defaut > 0) ? 'activée' : 'désactivée';
        
        return redirect()->route('admin.categories.index')
            ->with('success', "Catégorie $status avec succès.");
    }
}