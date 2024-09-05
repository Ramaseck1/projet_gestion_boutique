<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Vérifie si la réponse est une instance de Response
        if ($response instanceof Response) {
            $content = $response->getContent();
            $statusCode = $response->getStatusCode();

            // Décoder le contenu JSON
            $data = json_decode($content, true);

           
            // Retourne la réponse formatée
            return $this->formatResponse($response, $data);
        }

        // Retourne la réponse telle quelle si ce n'est pas une instance de Response
        return $response;
    }

    /**
     * Format the response based on the status code.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function formatResponse(Response $response, $data)
    {
        $statusCode = $response->getStatusCode();

        $messages = [
            200 => 'Opération réussie',
            202 => 'trouvé',
            203 => 'client trouvé',
            201 => 'Création réussie',
            204 => 'Aucun contenu',
            400 => 'Requête incorrecte',
            401 => 'Non autorisé',
            403 => 'Interdit',
            404 => 'Non trouvé',
            422 => 'Erreur de validation',
            500 => 'Erreur serveur interne',
        ];

        $message = $messages[$statusCode] ?? 'Erreur inconnue';

        return response()->json([
            'status' => $statusCode,
            'data' => ($statusCode === 204) ? null : $data,
            'message' => $message,
        ], $statusCode);
    }
}
