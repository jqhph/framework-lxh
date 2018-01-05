<?php

namespace App\Http\Middleware;

use Lxh\Auth\Auth;

use Closure;

class ScopeBouncer
{
    /**
     * The Bouncer instance.
     *
     * @var \Lxh\Auth\Auth
     */
    protected $bouncer;

    /**
     * Constructor.
     *
     * @param \Lxh\Auth\Auth  $bouncer
     */
    public function __construct(Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     * @param  \Lxh\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Here you may use whatever mechanism you use in your app
        // to determine the current tenant. To demonstrate, the
        // $tenantId is set here from the user's account_id.
        $tenantId = $request->user()->account_id;

        $this->bouncer->scope()->to($tenantId);

        return $next($request);
    }
}
