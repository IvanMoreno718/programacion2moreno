<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\JwtAuthentication;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ .  '/../src/db.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->add(new HttpBasicAuthentication([
	"path" => "/api",
	"secure" => false,
	"users" => [
		"username" => "password"
	]
]));

class AuthMiddleware {
    public function __invoke(Request $request, Handler $handler): Response {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token requerido']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key('your_secret_key', 'HS256'));
            $request = $request->withAttribute('username', $decoded->data->username);
            $request = $request->withAttribute('role', $decoded->data->role);
        } catch (\Exception $e) {

            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token inválido']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
};

class RoleMiddleware {
    private array $allowedRoles;
                                                                                                                                      
    public function __construct(array $allowedRoles) {
        $this->allowedRoles = $allowedRoles;
    }

    public function __invoke(Request $request, Handler $handler): Response {
        $username = $request->getAttribute('username');
        $role = $request->getAttribute('role');
        if (!$role || !in_array($role, $this->allowedRoles)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Acceso denegado']));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');}
        return $handler->handle($request);
    }
};

$app->get('/public', function ($req, $res) {
    $res->getBody()->write("Acceso libre");
    return $res;
});

$app->get('/admin', function ($req, $res) {
    $username = $req->getAttribute('username');
    $role = $req->getAttribute('role');
    $res->getBody()->write("Hola Admin, $username");
    return $res;
})->add(new RoleMiddleware(['admin']))->add(new AuthMiddleware());

class PermissionMiddleware {
    private array $requiredPermissions;

    public function __construct(array $requiredPermissions) {
        $this->requiredPermissions = $requiredPermissions;
    }

    public function __invoke($request, $handler) {
        $username = $request->getAttribute('username');
        $permissions = $username['permissions'] ?? [];
        foreach ($this->requiredPermissions as $perm) {
            if (!in_array($perm, $permissions)) {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(['error' => 'Permiso insuficiente']));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }
        }
        return $handler->handle($request);
    }
};


$app->post('/login', function (Request $request, Response $response) {
    $data = $request->getHeader('Authorization');
    $substr = substr($data[0], 6, 30);
    $decode = base64_decode($substr);
    $user = substr($decode, 0, 8);
    $pass = substr($decode, 9, 17);
    $username = $user;
    $password = $pass;

    if ($username === 'username' && $password === 'password') {
        $key = "your_secret_key";
        $payload = [
            "iss" => "example.com",
            "aud" => "example.com",
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + 3600,
            "data" => [
                "username" => $username,
                "role" => 'admin'
            ]
        ];
        $token = JWT::encode($payload, $key, 'HS256');
        $response->getBody()->write(json_encode(["token" => $token]));
    } else {
        $response->getBody()->write("Credenciales inválidas");
        return $response->withStatus(401);
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->add(new JwtAuthentication([
    "secret" => "your_secret_key",
    "attribute" => "token",
    "path" => "/api/protected",
    "ignore" => ["/login"],
    "algorithm" => ["HS256"],
	"secure" => false
]));

$app->get('/api/protected', function (Request $request, Response $response) {
    $token = $request->getAttribute('token');
    $username = $token['data'] -> username;
    $response->getBody()->write("Hola, $username");
    return $response;
});

$app->get('/productos', function (Request $request, Response $response) use ($pdo){
    $username = $request->getAttribute('username');
    $role = $request->getAttribute('role');
	$stmt = $pdo->query("SELECT * FROM PRODUCTOS");
	$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$response->getBody()->write(json_encode($productos));
	return $response->withHeader('Content-Type', 'application/json');
})->add(new RoleMiddleware(['admin']))->add(new AuthMiddleware());

$app->post('/productos', function (Request $request, Response $response) use ($pdo){
    $username = $request->getAttribute('username');
    $role = $request->getAttribute('role');
	$data = $request->getParsedBody();
	$IDPRODUCTO = $data['IDPRODUCTO'] ?? '';
	$CODIGO = $data['CODIGO'] ?? '';
	$NOMBRE = $data['NOMBRE'] ?? '';
	$DESCRIPCION = $data['DESCRIPCION'] ?? '';
	$STOCK = $data['STOCK'] ?? '';
	$PRECIOCOMPRA = $data['PRECIOCOMPRA'] ?? '';
	$PRECIOVENTA = $data['PRECIOVENTA'] ?? '';
	$ESTADO = $data['ESTADO'] ?? '';
	$FECHAREGISTRO = $data['FECHAREGISTRO'] ?? '';
	$IDCATEGORIA = $data['IDCATEGORIA'] ?? '';
	$stmt = $pdo->prepare("INSERT INTO PRODUCTOS VALUES(:IDPRODUCTO, :CODIGO, :NOMBRE, :DESCRIPCION, :STOCK, :PRECIOCOMPRA, :PRECIOVENTA, :ESTADO, :FECHAREGISTRO, :IDCATEGORIA);");
	$stmt->bindValue(':IDPRODUCTO', $IDPRODUCTO);
	$stmt->bindValue(':CODIGO', $CODIGO);
	$stmt->bindValue(':NOMBRE', $NOMBRE);
	$stmt->bindValue(':DESCRIPCION', $DESCRIPCION);
	$stmt->bindValue(':STOCK', $STOCK);
	$stmt->bindValue(':PRECIOCOMPRA', $PRECIOCOMPRA);
	$stmt->bindValue(':PRECIOVENTA', $PRECIOVENTA);
	$stmt->bindValue(':ESTADO', $ESTADO);
	$stmt->bindValue(':FECHAREGISTRO', $FECHAREGISTRO);
	$stmt->bindValue(':IDCATEGORIA', $IDCATEGORIA);
	$stmt->execute();
	$response->getBody()->write("Datos guardados correctamente");
	return $response;
})->add(new RoleMiddleware(['admin']))->add(new AuthMiddleware());

$app->delete('/productos', function (Request $request, Response $response) use ($pdo){
    $username = $request->getAttribute('username');
    $role = $request->getAttribute('role');
	$data = $request->getParsedBody();
	$IDPRODUCTO = $data['IDPRODUCTO'] ?? '';
	$stmt = $pdo->prepare("DELETE FROM ALISBOOK_BD.PRODUCTOS WHERE IDPRODUCTO=:IDPRODUCTO;");
	$stmt->bindvalue(':IDPRODUCTO', $IDPRODUCTO);
	$stmt->execute();
	$response->getBody()->write("Datos eliminados correctamente");
	return $response;
})->add(new RoleMiddleware(['admin']))->add(new AuthMiddleware());

$app->patch('/productos', function (Request $request, Response $response) use ($pdo){
    $username = $request->getAttribute('username');
    $role = $request->getAttribute('role');
	$data = $request->getParsedBody();
	$IDPRODUCTO = $data['IDPRODUCTO'] ?? '';
	$CODIGO = $data['CODIGO'] ?? '';
	$NOMBRE = $data['NOMBRE'] ?? '';
	$DESCRIPCION = $data['DESCRIPCION'] ?? '';
	$STOCK = $data['STOCK'] ?? '';
	$PRECIOCOMPRA = $data['PRECIOCOMPRA'] ?? '';
	$PRECIOVENTA = $data['PRECIOVENTA'] ?? '';
	$ESTADO = $data['ESTADO'] ?? '';
	$FECHAREGISTRO = $data['FECHAREGISTRO'] ?? '';
	$IDCATEGORIA = $data['IDCATEGORIA'] ?? '';
	$stmt = $pdo->prepare("UPDATE ALISBOOK_BD.PRODUCTOS SET CODIGO=:CODIGO, NOMBRE=:NOMBRE, DESCRIPCION=:DESCRIPCION, STOCK=:STOCK, PRECIOCOMPRA=:PRECIOCOMPRA, PRECIOVENTA=:PRECIOVENTA, ESTADO=:ESTADO, FECHAREGISTRO=:FECHAREGISTRO, IDCATEGORIA=:IDCATEGORIA WHERE IDPRODUCTO=:IDPRODUCTO;");
	$stmt->bindValue(':IDPRODUCTO', $IDPRODUCTO);
	$stmt->bindValue(':CODIGO', $CODIGO);
	$stmt->bindValue(':NOMBRE', $NOMBRE);
	$stmt->bindValue(':DESCRIPCION', $DESCRIPCION);
	$stmt->bindValue(':STOCK', $STOCK);
	$stmt->bindValue(':PRECIOCOMPRA', $PRECIOCOMPRA);
	$stmt->bindValue(':PRECIOVENTA', $PRECIOVENTA);
	$stmt->bindValue(':ESTADO', $ESTADO);
	$stmt->bindValue(':FECHAREGISTRO', $FECHAREGISTRO);
	$stmt->bindValue(':IDCATEGORIA', $IDCATEGORIA);
	$stmt->execute();
	$response->getBody()->write("Datos actualizados correctamente");
	return $response;
})->add(new RoleMiddleware(['admin']))->add(new AuthMiddleware());


$app->addErrorMiddleware(true, true, true);
$app->run();

?>
