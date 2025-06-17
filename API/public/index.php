<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
	$response->getBody()->write("Hola desde Get");
	return $response;
});

$app->post('/', function (Request $request, Response $response, $args) {
	$data = $request->getParsedBody();
	$response->getBody()->write(json_encode($data));
	return $response;
});

$app->addErrorMiddleware(true, true, true);
$app->setBasePath("/API/public");
$app->run();


?>
