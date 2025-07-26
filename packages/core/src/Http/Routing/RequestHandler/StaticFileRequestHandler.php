<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing\RequestHandler;

use PhoneBurner\SaltLite\Filesystem\File;
use PhoneBurner\SaltLite\Filesystem\FileMode;
use PhoneBurner\SaltLite\Filesystem\FileStream;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Psr7;
use PhoneBurner\SaltLite\Http\Response\Exceptional\FileNotFoundResponse;
use PhoneBurner\SaltLite\Http\Response\Exceptional\ServerErrorResponse;
use PhoneBurner\SaltLite\Http\Response\StreamResponse;
use PhoneBurner\SaltLite\Http\Routing\Domain\StaticFile;
use PhoneBurner\SaltLite\Http\Routing\Match\RouteMatch;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class StaticFileRequestHandler implements RequestHandlerInterface
{
    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route_attributes = Psr7::attribute(RouteMatch::class, $request)?->getAttributes() ?? [];
        $file = $route_attributes[StaticFile::class] ?? null;
        if (! $file instanceof StaticFile) {
            return new ServerErrorResponse();
        }

        $stream = File::stream($file->path, FileMode::Read);
        if (! $stream instanceof FileStream) {
            return new FileNotFoundResponse();
        }

        return new StreamResponse($stream, headers: [
            HttpHeader::CONTENT_TYPE => $file->content_type,
            HttpHeader::CONTENT_LENGTH => $stream->getSize() ?? 0,
            HttpHeader::CONTENT_DISPOSITION => $route_attributes[HttpHeader::CONTENT_DISPOSITION] ?? 'inline',
        ]);
    }
}
