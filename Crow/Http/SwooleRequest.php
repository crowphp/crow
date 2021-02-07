<?php

declare(strict_types=1);

namespace Crow\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Swoole\Http\Request;

class SwooleRequest implements ServerRequestInterface
{
    private Request $swooleRequest;
    private UriFactoryInterface $uriFactory;
    private StreamFactoryInterface $streamFactory;
    private UriInterface $uri;
    private StreamInterface $body;
    private string $requestTarget;
    private string $method;
    /**
     * @var array[]
     */
    private array $serverParams;
    /**
     * @var array[]
     */
    private array $cookies = [];
    /**
     * @var array[]
     */
    private array $queryParams = [];
    /**
     * @var array[]
     */
    private array $fileParams;
    private mixed $parsedBody;
    /**
     * @var array[]
     */
    private array $attributes;
    private string $protocol;

    public function __construct(
        Request $swooleRequest,
        UriFactoryInterface $uriFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->swooleRequest = $swooleRequest;
        $this->uriFactory = $uriFactory;
        $this->streamFactory = $streamFactory;
        $this->serverParams = $this->swooleRequest->server ?? [];
        $this->fileParams = $this->swooleRequest->files ?? [];
        $this->cookies = $this->swooleRequest->cookie ?? [];

        $query = $this->getUri()->getQuery();
        if ($query !== '') {
            \parse_str($query, $this->queryParams);
        }
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
        list($host) = explode(":", $this->swooleRequest->header['host']);
        $port = $this->swooleRequest->server['server_port'];
        $uri = (!empty($userInfo) ? '//' . $userInfo . '@' : '')
            . "$host:$port"
            . $this->getRequestTarget();

        return $this->uri = $this->uriFactory->createUri(
            $uri
        );
    }

    private function parseUserInfo(): string | null
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
        return $this->protocol ??
            ($this->protocol =
                $this->swooleRequest->server['server_protocol'] ?? '1.1');
    }

    public function withProtocolVersion($version): SwooleRequest
    {
        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    /**
     * @return array[]
     */
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

        foreach ($this->swooleRequest->header as $key => $value) {
            if (strtolower($name) == strtolower($key)) {
                return is_array($value)
                    ? $value
                    : [$value];
            }
        }
        return [];
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
        foreach ($new->swooleRequest->header as $key => $value) {
            if (strtolower($name) == strtolower($key)) {
                unset($new->swooleRequest->header[$key]);
                return $new;
            }
        }
        return $new;
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

    /**
     * @return array[]
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @return array[]
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * @param array[] $cookies
     * @return ServerRequestInterface
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookies = $cookies;
        return $new;
    }

    /**
     * @return array[]
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @param array[] $query
     * @return ServerRequestInterface
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * @return array[]
     */
    public function getUploadedFiles(): array
    {
        return $this->fileParams;
    }

    /**
     * @param array[] $uploadedFiles
     * @return ServerRequestInterface
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->fileParams = $uploadedFiles;
        return $new;
    }

    /**
     * @return mixed
     */
    public function getParsedBody(): mixed
    {
        return $this->parsedBody;
    }

    /**
     * @param mixed $data
     * @return ServerRequestInterface
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    /**
     * @return array[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null): mixed
    {
        if (!\array_key_exists($name, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return ServerRequestInterface
     */
    public function withAttribute($name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    /**
     * @param string $name
     * @return ServerRequestInterface
     */
    public function withoutAttribute($name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}
