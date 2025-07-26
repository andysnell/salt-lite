<?php

declare(strict_types=1);

namespace App;

use App\Example\Middleware\ExampleRequestAuthenticator;
use PhoneBurner\SaltLite\Framework\HealthCheck\RequestHandler\HealthCheckRequestHandler;
use PhoneBurner\SaltLite\Framework\HealthCheck\RequestHandler\ReadyCheckRequestHandler;
use PhoneBurner\SaltLite\Framework\Http\Middleware\RestrictToNonProductionEnvironments;
use PhoneBurner\SaltLite\Framework\Http\RequestHandler\CspViolationReportRequestHandler;
use PhoneBurner\SaltLite\Framework\Http\RequestHandler\ErrorRequestHandler;
use PhoneBurner\SaltLite\Framework\Http\RequestHandler\LogoutRequestHandler;
use PhoneBurner\SaltLite\Framework\Http\RequestHandler\PhpInfoRequestHandler;
use PhoneBurner\SaltLite\Framework\Http\Session\Middleware\EnableHttpSession;
use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Routing\Definition\RouteDefinition;
use PhoneBurner\SaltLite\Http\Routing\Domain\StaticFile;
use PhoneBurner\SaltLite\Http\Routing\RouteProvider;

use function PhoneBurner\SaltLite\Framework\path;

/**
 * @codeCoverageIgnore
 */
class ApplicationRouteProvider implements RouteProvider
{
    #[\Override]
    public function __invoke(): array
    {
        return [
            RouteDefinition::file('/', new StaticFile(
                path('/resources/views/welcome.html'),
                ContentType::HTML,
            ))->withMiddleware(EnableHttpSession::class),

            RouteDefinition::all('/logout')
                ->withHandler(LogoutRequestHandler::class)
                ->withMiddleware(EnableHttpSession::class)
                ->withName('logout'),

            RouteDefinition::get('/phpinfo')
                ->withHandler(PhpInfoRequestHandler::class)
                ->withMiddleware(RestrictToNonProductionEnvironments::class),

            RouteDefinition::file('/docs', new StaticFile(
                path('/resources/views/openapi.html'),
                ContentType::HTML,
            ))->withMiddleware(ExampleRequestAuthenticator::class),

            RouteDefinition::file('/openapi.json', new StaticFile(
                path('/resources/views/openapi.json'),
                ContentType::JSON,
            ))->withMiddleware(ExampleRequestAuthenticator::class),

            RouteDefinition::post('/csp')
                ->withHandler(CspViolationReportRequestHandler::class),

            RouteDefinition::get('/errors[/{error}]')
                ->withHandler(ErrorRequestHandler::class),

            RouteDefinition::get(ReadyCheckRequestHandler::DEFAULT_ENDPOINT)
                ->withHandler(ReadyCheckRequestHandler::class),

            RouteDefinition::get(HealthCheckRequestHandler::DEFAULT_ENDPOINT)
                ->withHandler(HealthCheckRequestHandler::class)
                ->withMiddleware(ExampleRequestAuthenticator::class),
        ];
    }
}
