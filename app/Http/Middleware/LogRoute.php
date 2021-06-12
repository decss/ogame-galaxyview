<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogRoute
{
    /** request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $query = "INSERT INTO ovg_hits (type, action, ip) VALUES (:type, :action, :ip)";
        $params = [
            ':type' => stristr($request->getRequestUri(), '/ui/') ? 1 : 2,
            ':action' => $request->getRequestUri(),
            ':ip' => $_SERVER['REMOTE_ADDR'],
        ];
        DB::insert($query, $params);

        return $response;
    }
}
