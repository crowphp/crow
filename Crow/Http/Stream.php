<?php

namespace Crow\Http;

use Psr\Http\Message\StreamInterface;
use Laminas\Diactoros\Stream as LaminasStream;

class Stream extends LaminasStream implements StreamInterface
{

}
