<?php

namespace Apitte\Negotiation\Transformer;

use Apitte\Negotiation\Http\ArrayStream;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsonTransformer extends AbstractTransformer
{

	/**
	 * Encode given data for response
	 *
	 * @param ResponseInterface $response
	 * @param array $options
	 * @return ResponseInterface
	 */
	public function encode(ResponseInterface $response, array $options = [])
	{
		// Return immediately if response is not accepted
		if (!$this->acceptResponse($response)) return $response;

		/** @var ArrayStream $body */
		$body = $response->getBody();
		$originBody = $body->getOriginal()->getBody();
		$originBody->write(Json::encode($body->getData()));

		// Setup content type
		$response = $response
			->withBody($originBody)
			->withHeader('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Parse given data from request
	 *
	 * @param ServerRequestInterface $request
	 * @param array $options
	 * @return ServerRequestInterface
	 */
	public function decode(ServerRequestInterface $request, array $options = [])
	{
		try {
			// Try to decode pure JSON in body and set to parse body
			$body = clone $request->getBody();
			$request = $request->withParsedBody(Json::decode((string) $body->getContents()));
		} catch (JsonException $e) {
		}

		return $request;
	}

}
