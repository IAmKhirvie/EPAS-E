<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    protected AuditLogService $auditLog;

    protected array $auditableActions = [
        'POST', 'PUT', 'PATCH', 'DELETE'
    ];

    protected array $excludedRoutes = [
        'login',
        'logout',
        'api/*',
    ];

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users and specific HTTP methods
        if (
            auth()->check() &&
            in_array($request->method(), $this->auditableActions) &&
            !$this->isExcluded($request) &&
            $response->isSuccessful()
        ) {
            $this->logRequest($request);
        }

        return $response;
    }

    protected function isExcluded(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        foreach ($this->excludedRoutes as $pattern) {
            if (str_contains($pattern, '*')) {
                $regex = str_replace('*', '.*', $pattern);
                if (preg_match("/^{$regex}$/", $routeName)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }

    protected function logRequest(Request $request): void
    {
        $action = $this->getActionFromMethod($request->method());
        $routeName = $request->route()?->getName() ?? 'unknown';

        $this->auditLog->log(
            $action,
            "Action performed: {$routeName}",
            null,
            null,
            $request->except(['password', 'password_confirmation', '_token'])
        );
    }

    protected function getActionFromMethod(string $method): string
    {
        return match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'action',
        };
    }
}
