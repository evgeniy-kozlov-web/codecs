<?php

namespace app\codecs;

class JWTCodec extends AbstractCodec
{
	public function encode(array $payload): string
	{
		$header = json_encode([
			'typ' => 'JWT',
			'alg' => 'HS256'
		]);

		$payload = json_encode($payload);

		$signature = hash_hmac(
			'sha256',
			$header . '.' . $payload,
			$this->key,
			true
		);

		return $this->base64urlEncode($header) . '.' . $this->base64urlEncode($payload) . '.' . $this->base64urlEncode($signature);
	}

	public function decode(string $token): array
	{
		if (!preg_match('/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/', $token, $matches)) throw new \app\exceptions\InvalidTokenFormatException();

		$header = $this->base64urlDecode($matches['header']);
		$payload = $this->base64urlDecode($matches['payload']);
		$signatureFromToken = $this->base64urlDecode($matches['signature']);

		$signature = hash_hmac(
			'sha256',
			$header . '.' . $payload,
			$this->key,
			true
		);

		if (!hash_equals($signature, $signatureFromToken)) throw new \app\exceptions\InvalidSignatureException();

		$payload = json_decode($this->base64urlDecode($matches['payload']), true);

		return $payload;
	}

	private function base64urlEncode(string $url): string
	{
		return str_replace(
			['+', '/', '='],
			['-', '_', ''],
			base64_encode($url)
		);
	}

	private function base64urlDecode(string $url): string
	{
		return base64_decode(str_replace(
			['-', '_'],
			['+', '/'],
			$url
		));
	}
}
