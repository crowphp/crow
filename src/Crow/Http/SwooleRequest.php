<?php declare(strict_types=1);

namespace Crow\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Swoole\Http\Request;

class SwooleRequest implements ServerRequestInterface
{
    private string $requestTarget;
    private UriInterface $uri;
    private string $method;
    private string $protocol;

    public function __construct(
        private Request $swooleRequest,
        private UriFactoryInterface $uriFactory,
        private StreamFactoryInterface $streamFactory
    )
    {
    }

    public function getRequestTarget(): string
    {
        return !empty($this->requestTarget)
            ? $this->requestTarget
            : ($this->requestTarget = $this->buildRequestTarget());
    }

    private function buildRequestTarget(): string
    {
        $queryString = !empty($this->swooleRequest->server['query_string'])
            ? '?' . $this->swooleRequest->server['query_string']
            : '';

        return $this->swooleRequest->server['request_uri']
            . $queryString;
    }

    public function withRequestTarget($requestTarget): RequestInterface
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    public function getMethod()
    {
        return !empty($this->method)
            ? $this->method
            : ($this->method = $this->swooleRequest->server['request_method']);
    }

    public function withMethod($method): RequestInterface
    {
        $validMethods = ['options', 'get', 'head', 'post', 'put', 'delete', 'trace', 'connect'];
        if (!in_array(strtolower($method), $validMethods)) {
            throw new \InvalidArgumentException('Invalid HTTP method');
        }

        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri(): UriInterface
    {
        if (!empty($this->uri)) {
            return $this->uri;
        }

        $userInfo = $this->parseUserInfo() ?? null;

        $uri = (!empty($userInfo) ? '//' . $userInfo . '@' : '')
            . $this->swooleRequest->header['host']
            . $this->getRequestTarget();

        return $this->uri = $this->uriFactory->createUri(
            $uri
        );
    }

    private function parseUserInfo(): bool|string|null
    {
        $authorization = $this->swooleRequest->header['authorization'] ?? '';

        if (str_starts_with($authorization, 'Basic')) {
            $parts = explode(' ', $authorization);
            return base64_decode($parts[1]);
        }

        return null;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        $new = clone $this;
        $new->uri = $uri;
        return $new;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocol ?? ($this->protocol = '1.1');
    }

    public function withProtocolVersion($version): SwooleRequest
    {
        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->swooleRequest->header;
    }

    public function hasHeader($name): bool
    {
        foreach ($this->swooleRequest->header as $key => $value) {
            if (strtolower($name) == strtolower($key)) {
                return true;
            }
        }

        return false;
    }

    public function getHeader($name): array
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        foreach ($this->swooleRequest->header as $key => $value) {
            if (strtolower($name) == strtolower($key)) {
                return is_array($value)
                    ? $value
                    : [$value];
            }
        }
    }

    public function getHeaderLine($name): string
    {
        return \implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value): RequestInterface
    {
        $new = clone $this;
        $new->swooleRequest->header[$name] = $value;
        return $new;
    }

    public function withAddedHeader($name, $value): RequestInterface
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $new = clone $this;
        if (is_array($new->swooleRequest->header[$name])) {
            $new->swooleRequest->header[$name][] = $value;
        } else {
            $new->swooleRequest->header[$name] = [
                $new->swooleRequest->header[$name],
                $value
            ];
        }

        return $new;
    }

    public function withoutHeader($name): RequestInterface
    {
        $new = clone $this;

        if (!$new->hasHeader($name)) {
            return $new;
        }

        foreach ($new->swooleRequest->header as $key => $value) {
            if (strtolower($name) == strtolower($key)) {
                unset($new->swooleRequest->header[$key]);
                return $new;
            }
        }
    }

    public function getBody(): StreamInterface
    {
        return $this->body ?? $this->streamFactory->createStream($this->swooleRequest->rawContent());
    }

    public function withBody(StreamInterface $body): RequestInterface
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    public function getServerParams()
    {
        // TODO: Implement getServerParams() method.
    }

    public function getCookieParams()
    {
        // TODO: Implement getCookieParams() method.
    }

    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
    }

    public function getQueryParams()
    {
        // TODO: Implement getQueryParams() method.
    }

    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
    }

    public function getUploadedFiles()
    {
        // TODO: Implement getUploadedFiles() method.
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
    }

    public function getParsedBody()
    {
        // TODO: Implement getParsedBody() method.
    }

    public function withParsedBody($data)
    {
        // TODO: Implement withParsedBody() method.
    }

    public function getAttributes()
    {
        // TODO: Implement getAttributes() method.
    }

    public function getAttribute($name, $default = null)
    {
        // TODO: Implement getAttribute() method.
    }

    public function withAttribute($name, $value)
    {
        // TODO: Implement withAttribute() method.
    }

    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }
}