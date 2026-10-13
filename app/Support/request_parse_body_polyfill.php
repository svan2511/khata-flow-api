<?php

// Polyfill for PHP < 8.4.
//
// Symfony 8's Request::createFromGlobals() calls the PHP 8.4 native
// request_parse_body() for PUT / DELETE / PATCH / QUERY requests
// (see vendor/symfony/http-foundation/Request.php). Production runs
// PHP 8.3 where that function does not exist, so without this file
// every PUT request (e.g. customer edit → update) dies with a fatal
// error before Laravel even boots, and nginx returns "200 + HTML",
// which the mobile app shows as "Server is busy".
//
// The call inside Symfony is namespace-relative, so this global
// definition is picked up automatically via PHP's function fallback.
// On PHP >= 8.4 the native function wins and this file is a no-op.

if (! function_exists('request_parse_body')) {
    function request_parse_body(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');

        // JSON (all PUT bodies our app sends): Laravel reads these from the
        // raw content via $request->json(), so nothing belongs in $post here.
        if (stripos($contentType, 'application/json') !== false || stripos($contentType, '+json') !== false) {
            return [[], []];
        }

        // Classic form bodies sent with PUT / PATCH.
        if (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
            $post = [];
            parse_str(file_get_contents('php://input') ?: '', $post);

            return [$post, []];
        }

        // Anything else (e.g. multipart PUT): same fallback Symfony itself
        // uses when the native parser throws — PHP's superglobals.
        return [$_POST, $_FILES];
    }
}
