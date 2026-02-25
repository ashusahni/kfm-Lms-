<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

/**
 * Serves the React SPA when SERVE_REACT_FROM_BACKEND is enabled.
 * Returns public/spa/index.html so the same origin serves both API and frontend.
 */
class SpaController extends Controller
{
    public function __invoke()
    {
        $path = public_path('spa/index.html');

        if (!File::exists($path)) {
            abort(404, 'React app not built. Run: cd frontend && npm run build && copy build to backend/public/spa/');
        }

        return new Response(File::get($path), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
