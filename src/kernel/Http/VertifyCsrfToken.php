<?php

namespace Lxh\Http;

use Closure;
use Lxh\Exceptions\TokenMismatchException;

class VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    public function __construct()
    {
    }

    public function handle($params, Closure $next)
    {
        $request = request();

        if (
            $this->isReading($request) ||
            $this->runningUnitTests($request) ||
            $this->tokensMatch($request)
        ) {
            return $next($params);
        }

        throw new TokenMismatchException;
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function isReading(Request $request)
    {
        return in_array($request->getMethod(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function runningUnitTests(Request $request)
    {
        return $request->isCli();
    }


    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function tokensMatch(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        $sessionToken = csrf_token();

        return $sessionToken && is_string($token) && ($sessionToken == $token);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  Request  $request
     * @return string
     */
    protected function getTokenFromRequest(Request $request)
    {
        $token = I('_token') ?: $request->getHeaderLine('X_CSRF_TOKEN');

        if (! $token && $header = $request->getHeaderLine('X_XSRF_TOKEN')) {
            $token = $this->decrypt($header);
        }
        return $token;
    }

    protected function decrypt(&$header)
    {
        return $header;
    }

}
