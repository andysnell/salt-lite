<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Cookie;

use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Math\Math;
use PhoneBurner\SaltLite\Time\Clock\Clock;
use PhoneBurner\SaltLite\Time\Clock\SystemClock;
use PhoneBurner\SaltLite\Time\TimeInterval\TimeInterval;
use PhoneBurner\SaltLite\Time\TimeUnit;
use Psr\Http\Message\ResponseInterface;

/**
 * @link https://datatracker.ietf.org/doc/html/rfc6265
 */
readonly class Cookie
{
    public const string INVALID_NAME_CHARS = '()<>@,;:\"/[]?={}';

    /**
     * While RFC 6265, which defines HTTP Cookies, does not set an upper limit
     * on the maximum lifetime of a cookie, modern browsers like Chrome limit
     * cookies to 400 days.
     */
    public const int MAX_AGE = 400 * TimeUnit::SECONDS_IN_DAY;

    /**
     * A cookie with a zero or negative age value expires immediately. We want
     * to clamp the value for consistency.
     */
    public const int MIN_AGE = -1;

    public function __construct(
        public string $name,
        public \Stringable|string $value,
        public \DateTimeInterface|\DateInterval|null $ttl = null,
        public string $path = '/',
        public string $domain = '',
        public bool $secure = true,
        public bool $http_only = true,
        public SameSite|null $same_site = SameSite::Lax,
        public bool $partitioned = false,
        public bool $raw = false,
        public bool $encrypt = false,
        private Clock $clock = new SystemClock(),
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Cookie name cannot be empty');
        }

        // The cookie name string must be a valid RFC 2616 "token" string,
        // and may contain any ASCII characters, except for control characters,
        // space, tab, and the following "separator" characters: ()<>@,;:\"/[]?={}
        // See: https://datatracker.ietf.org/doc/html/rfc2616.html#section-2.2
        if (! \ctype_graph($name) || \strpbrk($name, self::INVALID_NAME_CHARS) !== false) {
            throw new \InvalidArgumentException(\sprintf('The cookie name "%s" contains invalid characters.', $name));
        }

        if ($this->same_site === SameSite::None && $this->secure === false) {
            throw new \InvalidArgumentException('SameSite=None requires Secure Setting');
        }
    }

    public static function remove(
        string $name,
        string $path = '/',
        string $domain = '',
    ): self {
        return new self($name, '', null, $path, $domain);
    }

    public function withValue(\Stringable|string $value): self
    {
        return new self(
            $this->name,
            $value,
            $this->ttl,
            $this->path,
            $this->domain,
            $this->secure,
            $this->http_only,
            $this->same_site,
            $this->partitioned,
            $this->raw,
        );
    }

    public function value(): string
    {
        return (string)$this->value;
    }

    /**
     * This method added for convenience to set a one-off cookie on a response;
     * however, using the CookieManager to queue up and set cookies is preferred,
     * and safer, as it will handle encryption and decryption of cookies, as well
     * as preventing the loss of the cookie if a different response is returned
     * later in the middleware queue.
     */
    public function set(ResponseInterface $response, Clock $clock = new SystemClock()): ResponseInterface
    {
        return $response->withAddedHeader(HttpHeader::SET_COOKIE, $this->toString($clock));
    }

    public function toString(Clock $clock = new SystemClock()): string
    {
        return $this->name . '=' . \implode('; ', \array_filter([
            'value' => match (true) {
                $this->value === '' => 'deleted',
                $this->raw => (string)$this->value,
                default => \rawurlencode((string)$this->value),
            },
            'max-age' => match (true) {
                $this->value === '' => 'Max-Age=0',
                $this->ttl instanceof \DateInterval,
                $this->ttl instanceof \DateTimeInterface => \sprintf('Max-Age=%d', $this->calculateMaxAge($this->ttl)),
                default => null,
            },
            'path' => $this->path ? \sprintf('Path=%s', $this->path) : null,
            'domain' => $this->domain ? \sprintf('Domain=%s', $this->domain) : null,
            'secure' => $this->secure ? 'Secure' : null,
            'http_only' => $this->http_only ? 'HttpOnly' : null,
            'same_site' => $this->same_site ? \sprintf('SameSite=%s', $this->same_site->name) : null,
            'partitioned' => $this->partitioned ? 'Partitioned' : null,
        ]));
    }

    private function calculateMaxAge(\DateInterval|\DateTimeInterface $ttl): int
    {
        $now = $this->clock->now();

        return Math::iclamp(match (true) {
            $ttl instanceof TimeInterval => $ttl->seconds,
            $ttl instanceof \DateInterval => $now->add($ttl)->getTimestamp() - $now->getTimestamp(),
            $ttl instanceof \DateTimeInterface => $ttl->getTimestamp() - $now->getTimestamp(),
        }, self::MIN_AGE, self::MAX_AGE);
    }
}
