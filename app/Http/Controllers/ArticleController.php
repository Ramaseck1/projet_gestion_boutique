<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class ArticleController extends Controller
{
    


 

    public function index(Request $request)
    {
        // Récupérer le paramètre 'disponible' de la requête
        $disponible = $request->query('disponible');

        // Initialiser la requête de base pour récupérer les articles
        $query = Article::query();

        // Appliquer le filtre de disponibilité si le paramètre est présent
        if ($disponible === 'oui') {
            $query->where('qteStock', '>', 0);
        } elseif ($disponible === 'non') {
            $query->where('qteStock', '=', 0);
        }
    


        // Exécuter la requête et récupérer les articles
        $articles = $query->get();

        // Retourner la réponse JSON avec les articles
        return response()->json([
            'status' => 200,
            'data' => $articles,
            'message' => 'Liste des articles récupérée avec succès',
        ], 200);
    }




    public function store(Request $request)
    {
        // Définition des règles de validation
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|unique:articles,libelle',
            'prix' => 'required|numeric|min:0',
            'qteStock' => 'required|integer|min:0',
        ]);

        // Si la validation échoue, renvoie les erreurs
        if ($validator->fails()) {
            return response()->json([
                'status' => 411,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation',
            ], 411);
        }

        // Crée un nouvel article
        $article = Article::create([
            'libelle' => $request->libelle,
            'prix' => $request->prix,
            'qteStock' => $request->qteStock,
        ]);

        // Réponse de succès
        return response()->json([
            'status' => 201,
            'data' => $article,
            'message' => 'Article enregistré avec succès',
        ], 201);
    }




public function showById($id)
    {
        // Recherche l'article par ID
        $article = Article::find($id);

        // Vérifie si l'article existe
        if ($article) {
            return response()->json([
                'status' => 200,
                'data' => $article,
                'message' => 'Article trouvé',
            ], 200);
        }

        // Réponse si l'article n'est pas trouvé
        return response()->json([
            'status' => 411,
            'data' => null,
            'message' => 'Objet non trouvé',
        ], 411);
    }


  
    public function showByLibelle(Request $request)
    {
        // Validation de la requête
        $request->validate([
            'libelle' => 'required|string',
        ]);

        // Recherche l'article par libellé
        $article = Article::where('libelle', $request->libelle)->first();

        // Vérifie si l'article existe
        if ($article) {
            return response()->json([
                'status' => 200,
                'data' => $article,
                'message' => 'Article trouvé',
            ], 200);
        }

        // Réponse si l'article n'est pas trouvé
        return response()->json([
            'status' => 411,
            'data' => null,
            'message' => 'Objet non trouvé',
        ], 411);
    }


   

    public function updateStock(Request $request, $id)
    {

        $authUser = Auth::user();
        // Vérifie si l'utilisateur authentifié est un boutiquier
        if ($authUser->role_id !== 2) {
            return response()->json([
                'status' => 403,
                'data' => null,
                'message' => 'Vous n\'avez pas les autorisations nécessaires pour ajouter un article',
            ], 403);
        }
        $id = (int) $id; // Forcer l'ID en entier
        
        // Validation de la requête
        $request->validate([
            'qteStock' => 'required|numeric|min:0',
        ]);

        // Trouver l'article par ID
        $article = Article::find($id);

        // Vérifier si l'article existe
        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé',
            ], 411);
        }

        // Mettre à jour la quantité en stock
        $article->qteStock = $request->qteStock;
        $article->save();

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Quantité en stock mise à jour',
        ], 200);
    }

    /**
     * Met à jour les quantités en stock de plusieurs articles.
     */



    public function updateMultipleStocks(Request $request)
    {
        // Validation de la requête
        $request->validate([
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.qteStock' => 'required|numeric|min:0',
        ]);

        $updatedArticles = [];
        $notFoundArticles = [];

        foreach ($request->articles as $item) {
            $article = Article::find($item['id']);
            
            if ($article) {
                $article->qteStock = $item['qteStock'];
                $article->save();
                $updatedArticles[] = $article;
            } else {
                $notFoundArticles[] = $item['id'];
            }
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'success' => $updatedArticles,
                'error' => $notFoundArticles,
            ],
            'message' => 'Quantités en stock mises à jour',
        ], 200);
    }
}
