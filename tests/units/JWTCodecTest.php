<?php

namespace tests\units;

class JWTCodecTest extends \PHPUnit\Framework\TestCase
{
	private string $key = 'test';

	private \app\codecs\JWTCodec $codec;

	public function setUp(): void
	{
		$this->codec = new \app\codecs\JWTCodec($this->key);
	}

	public function testItExtendsAbstractCodec()
	{
		$this->assertTrue(is_subclass_of($this->codec, \app\codecs\AbstractCodec::class));
	}

	public function payloads()
	{
		return array(
			[
				[
					'email' => 'test@test.com'
				],
				[
					'email' => 'test@test.com',
					'exp' => time()
				],
				[
					'email' => 'sezmar@gmail.com',
					'exp' => time()
				],
			]
		);
	}

	/**
	 * @dataProvider payloads
	 */
	public function testItReturnsCorrectPayload(array $payload)
	{
		$token = $this->codec->encode($payload);

		$this->assertEquals($payload, $this->codec->decode($token));
	}

	public function testItThrowsInvalidTokenFormatExceptionIfTokenFormatIsInvalid()
	{
		$this->expectException(\app\exceptions\InvalidTokenFormatException::class);

		$token = 'invalid';

		$this->codec->decode($token);
	}

	public function testItThrowsInvalidSignatureExceptionIfSignatureIsInvalid()
	{
		$this->expectException(\app\exceptions\InvalidSignatureException::class);

		$token = 'invalid.invalid.invalid';

		$this->codec->decode($token);
	}
}
