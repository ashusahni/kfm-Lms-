<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serve React app from Laravel (single URL)
    |--------------------------------------------------------------------------
    |
    | When true, the backend URL (e.g. http://127.0.0.1:8000) serves the
    | React app for the main site and student panel. /api and /admin stay
    | Laravel. Set SERVE_REACT_FROM_BACKEND=true in .env and build+copy
    | the React app to public/spa/ (see FRONTEND-BACKEND-INTEGRATION.md).
    |
    */

    'serve_react' => env('SERVE_REACT_FROM_BACKEND', false),

    /*
    |--------------------------------------------------------------------------
    | Frontend (React) application URL
    |--------------------------------------------------------------------------
    |
    | When serve_react is false: used for CORS and redirects (e.g. after
    | login from admin "Login as user"). When serve_react is true, can be
    | null; the same origin serves both API and React.
    |
    */

    'url' => env('FRONTEND_URL', null),

];
