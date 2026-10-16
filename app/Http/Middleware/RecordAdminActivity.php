<?php

namespace App\Http\Middleware;

use App\Models\AdminActivity;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Writes Admin > Activity log. Runs on every admin route, and records a row
 * when an admin action succeeded — i.e. it changed something (not a GET) and
 * flashed its usual green confirmation ('status'), which becomes the row's
 * description. Failed actions (they flash 'adminError' or validation
 * errors instead), chat replies and typing pings flash nothing, so they're
 * left out.
 */
class RecordAdminActivity
{
    /**
     * Route parameters whose model belongs to a customer through ->user.
     */
    private const CUSTOMER_OWNED = ['kycVerification', 'addressVerification', 'cardRequest', 'bill'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethodSafe() || ! $request->hasSession() || ! $request->user()) {
            return $response;
        }

        $session = $request->session();
        $flashedNow = (array) $session->get('_flash.new', []);
        $message = $session->get('status');

        if (! in_array('status', $flashedNow, true) || ! is_string($message) || $message === '') {
            return $response;
        }

        AdminActivity::create([
            'admin_id' => $request->user()->id,
            'user_id' => $this->customerFor($request)?->id,
            'action' => (string) $request->route()?->getName(),
            'description' => $message,
            'ip_address' => $request->ip(),
        ]);

        return $response;
    }

    /**
     * The customer the action was about, from the route: a {user} directly,
     * or the owner of a KYC/address submission, card request or bill.
     */
    private function customerFor(Request $request): ?User
    {
        $route = $request->route();

        if (! $route) {
            return null;
        }

        $user = $route->parameter('user');

        if ($user instanceof User) {
            return $user;
        }

        foreach (self::CUSTOMER_OWNED as $name) {
            $model = $route->parameter($name);

            if ($model instanceof Model && $model->getAttribute('user_id')) {
                return User::find((int) $model->getAttribute('user_id'));
            }
        }

        return null;
    }
}
