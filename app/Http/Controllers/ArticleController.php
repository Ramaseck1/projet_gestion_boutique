<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



/**
 * @OA\Tag(
 *     name="Article",
 *     description="Les opérations liées aux articles"
 * )
 * 
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     required={"id", "libelle", "prix", "qteStock"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="libelle", type="string", example="Article Example"),
 *     @OA\Property(property="prix", type="number", format="float", example=19.99),
 *     @OA\Property(property="qteStock", type="integer", example=10),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */
class ArticleController extends Controller
{
    


 /**
     * @OA\Get(
     *     path="/api/v1/articles",
     *     tags={"Articles"},
     *     summary="List all articles with optional filter",
     *     operationId="listArticles",
     *     @OA\Parameter(
     *         name="disponible",
     *         in="query",
     *         description="Filter articles by availability",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")),
     *             @OA\Property(property="message", type="string", example="Liste des articles récupérée avec succès")
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/api/v1/articles",
     *     tags={"Articles"},
     *     summary="Create a new article",
     *     operationId="createArticle",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"libelle", "prix", "qteStock"},
     *                 @OA\Property(property="libelle", type="string", example="Article Example"),
     *                 @OA\Property(property="prix", type="number", format="float", example=19.99),
     *                 @OA\Property(property="qteStock", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="data", ref="#/components/schemas/Article"),
     *             @OA\Property(property="message", type="string", example="Article enregistré avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="object", @OA\Property(property="errors", type="object", additionalProperties=true)),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     )
     * )
     */


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



/**
     * @OA\Get(
     *     path="/api/v1/articles/{id}",
     *     tags={"Articles"},
     *     summary="Get an article by ID",
     *     operationId="getArticleById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Article"),
     *             @OA\Property(property="message", type="string", example="Article trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     )
     * )
     */

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


    /**
     * @OA\Post(
     *     path="/api/v1/articles/libelle",
     *     tags={"Articles"},
     *     summary="Get an article by libelle",
     *     operationId="getArticleByLibelle",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"libelle"},
     *                 @OA\Property(property="libelle", type="string", example="Article Example")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Article"),
     *             @OA\Property(property="message", type="string", example="Article trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Patch(
     *     path="/api/v1/articles/stock/{id}",
     *     tags={"Articles"},
     *     summary="Update stock quantity of an article",
     *     operationId="updateArticleStock",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"qteStock"},
     *                 @OA\Property(property="qteStock", type="number", format="float", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock quantity updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Article"),
     *             @OA\Property(property="message", type="string", example="Quantité en stock mise à jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=403),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Vous n'avez pas les autorisations nécessaires pour ajouter un article")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     )
     * )
     */

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


     /**
     * @OA\Patch(
     *     path="/api/v1/articles/multiplestock",
     *     summary="Mettre à jour les stocks de plusieurs articles",
     *     description="Met à jour les quantités en stock de plusieurs articles à la fois.",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"articles"},
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="qteStock", type="integer", example=100)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantités en stock mises à jour",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="success", type="array", @OA\Items(ref="#/components/schemas/Article")),
     *                 @OA\Property(property="error", type="array", @OA\Items(type="integer"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Quantités en stock mises à jour")
     *         )
     *     )
     * )
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
