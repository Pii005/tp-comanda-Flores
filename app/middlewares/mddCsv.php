<?php


// use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response as SlimResponse;


class MddCsv
{
    function __invoke(Request $request, RequestHandler $handler): Response
    {
        $body = (string)$request->getBody();

        if (empty($body)) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['error' => 'No file content uploaded.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        return $handler->handle($request);
    }   
}

