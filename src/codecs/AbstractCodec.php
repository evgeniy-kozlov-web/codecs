<?php

namespace app\codecs;

abstract class AbstractCodec
{
	public function __construct(protected string $key)
	{
	}

	abstract public function encode(array $payload);
	abstract public function decode(string $token);
}
