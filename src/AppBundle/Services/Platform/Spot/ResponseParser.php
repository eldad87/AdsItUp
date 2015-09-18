<?php

namespace AppBundle\Services\Platform\Spot;

class ResponseParser {
	/**
	 * @param Request $req
	 * @param $rawResponse
	 * @return response
	 * @throws ResponseException
	 */
	static public function parseResponse(Request $req, $rawResponse)
	{
		try {
			$response = json_decode($rawResponse, true);
		} catch(\Exception $e) {
			throw new ResponseException('Unknown response given', $rawResponse, 0, $e);
		}

		return self::parse($req, $response, $rawResponse);
	}

	/**
	 * @param BatchRequest $request
	 * @param $rawResponse
	 * @return BatchResponse
	 * @throws ResponseException
	 */
	static public function parseBatchResponse(BatchRequest $request, $rawResponse)
	{
		try {
			$response = json_decode($rawResponse, true);
		} catch(\Exception $e) {
			throw new ResponseException('Unknown response given', $rawResponse, 0, $e);
		}

		if(!isSet($response['connection_status'])) {
			throw new ResponseException('Connection status is missing', $rawResponse);
		}

		$connectionStatus = $response['connection_status'];
		unset($response['connection_status']);
		$response = array_values($response);

		$parsedResponse = new BatchResponse();
		foreach($response AS $key=>$res) {
			$res['connection_status'] = $connectionStatus;
			$parsedResponse->add(
				self::parse($request->getIterator()->offsetGet($key), $res, $rawResponse),
				$key
			);
		}

		return $parsedResponse;
	}

	/**
	 * Parse Spot's raw API response ano normalize it
	 * @param Request $req
	 * @param $response
	 * @param $rawResponse
	 * @return response
	 * @throws ResponseException
	 */
	static private function parse(Request $req, array $response, $rawResponse)
	{
		//Check that request for validation errors
		if(isset($response['errors'])) {
			if(Request::COMMAND_VIEW == $req->getCommand() &&
				isset($response['errors']['error']) &&
				'noResults'==$response['errors']['error']) {
				return new Response(true); //No records
			}
			return new Response(false, $response['errors']);
		}

		// Check that request is OK
		if(	!isset($response['connection_status']) ||
			!isset($response['operation_status']) ||
			'successful' != $response['connection_status'] ||
			'successful' != $response['operation_status']
		) {
			return new Response(false, $response);
		}

		//Spot may return the results as a nested array of requested Model
		if(isset($response[$req->getModel()])) {
			$response = $response[$req->getModel()];
		}

		//Normalize response data
		switch($req->getCommand()) {
			case Request::COMMAND_VIEW:
				return new Response(true, array_values($response));
				break;

			case Request::COMMAND_ADD:
			case Request::COMMAND_EDIT:
				//Spot may return different fields for ID, lets normalize it to be simply 'id'
				if(!isset($response['id'])) {
					$idCamelCaps = ucfirst($req->getModel()) . 'Id'; //CamelCaps -> CustomerId
					$idCamelCase = lcfirst($req->getModel()) . 'Id'; //camelCase -> customerId

					//Check if ID key can be found in response
					if(isset($response[$idCamelCaps])) {
						$response['id'] = $response[$idCamelCaps];
					} else if(isset($response[$idCamelCase])) {
						$response['id'] = $response[$idCamelCase];
					} else {
						//In case ID still not exists, check if exists in parameters
						$parameters = $req->getParameters();
						if(isset($parameters[$idCamelCaps])) {
							$response['id'] = $parameters[$idCamelCaps];
						} else if(isset($parameters[$idCamelCase])) {
							$response['id'] = $parameters[$idCamelCase];
						} else if(isset($parameters['id'])) {
							$response['id'] = $parameters['id'];
						}
					}
				}

				return new Response(true, $response);
				break;
		}

		throw new ResponseException('Unknown response given', $rawResponse);
	}
}