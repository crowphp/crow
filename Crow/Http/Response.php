<?php

declare(strict_types=1);

namespace Crow\Http;

use Laminas\Diactoros\Response as LaminasResponse;
use Psr\Http\Message\ResponseInterface;

class Response extends LaminasResponse implements ResponseInterface
{

}
