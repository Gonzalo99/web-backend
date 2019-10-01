<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

require_once 'vendor/autoload.php';
require_once 'piramide-uploader/PiramideUploader.php';

$app = new \Slim\Slim();

$db = new mysqli('127.0.0.1', 'root', 'gj140699', 'inmueblesbd');

//Configuración cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

//Prueba
$app->get('/prueba', function() use($db, $app){

	echo "Prueba realizada con exito";
	
});	

//INMUEBLES

//Devuelve el id del ultimo inmueble de la BD
$app->get('/last-inmueble-id', function() use($db, $app){

	$sql = 'SELECT id FROM inmuebles ORDER BY id DESC LIMIT 1;';

	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Id no disponible'
	);

	if ($query->num_rows == 1) {
		$id = $query->fetch_assoc();

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $id
		);
	}

	echo json_encode($result);

});	

//Subir imagen inmueble
$app->post('/upload-file', function() use($db, $app){

	$nombre_imagen = $_FILES['imagen']['name'];

	$tipo_imagen = $_FILES['imagen']['type'];

	$tamagno_imagen = $_FILES['imagen']['size'];

	$result = array(
		'status' => 'success',
		'code' => 200,
		'message' => 'Foto subida correctamente'
	);

	//No puede superar 3GB
	if ( $tamagno_imagen <= 3000000 ) {

		//Solo imagenes
		if ($tipo_imagen == "image/jpeg" || $tipo_imagen == "image/jpg" || $tipo_imagen == "image/png" || $tipo_imagen == "image/gif") {

			//Ruta de la carpeta destino
			$carpeta_destino = './uploads/'.$nombre_imagen;

			$archivo = $_FILES['imagen']['tmp_name'];

			move_uploaded_file($archivo, $carpeta_destino);

		} else {
			
			$result = array(
				'status' => 'success',
				'code' => 404,
				'message' => 'Formato de archivo incorrecto'
			);
		}

	} else {

		$result = array(
			'status' => 'success',
			'code' => 404,
			'message' => 'El size es demasiado grande'
		);

	}

	echo json_encode($result);

});

//Actualizar visitas
$app->get('/update-visitas/:id/:num', function($id, $num) use($db, $app){

	$sql = "UPDATE inmuebles SET visitas = $num WHERE id = ".$id;

	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Visitas actualizado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Visitas no se ha podido actualizar'
		);
	}

	echo json_encode($result);

});	

//Actualizar inmueble
$app->post('/update-inmueble/:id', function($id) use($db, $app){

	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$sql = "UPDATE inmuebles SET ".
			"titulo = '{$data["titulo"]}',".
			"contenido = '{$data["contenido"]}',".
			"tipo = '{$data["tipo"]}',".
			"metros = '{$data["metros"]}',".
			"visitas = '{$data["visitas"]}',".
			"direccion = '{$data["direccion"]}',".
			"precio = '{$data["precio"]}',".
			"estado = '{$data["estado"]}',".
			"oferta = {$data["oferta"]},".
			"antiguedad = '{$data["antiguedad"]}',".
			"planta = '{$data["planta"]}',".
			"ascensor = {$data["ascensor"]},".
			"garaje = {$data["garaje"]} WHERE id = {$id};";

	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'El inmueble se ha actualizado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'El inmueble no se ha podido actualizar'
		);
	}

	echo json_encode($result);

});

//Update firstImg inmueble
$app->get('/update-first_img-inmueble/:id/:ruta', function($id, $ruta) use($db, $app){

	$sql = "UPDATE inmuebles SET first_img = '$ruta' WHERE id = $id;";

	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'First_img se ha actualizado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'First_img no se ha podido actualizar'
		);
	}

	echo json_encode($result);

});	

//Eliminar inmueble
$app->get('/eliminar-inmueble/:id', function($id) use($db, $app){

	$sql = 'DELETE FROM inmuebles WHERE id = '.$id;
	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'El inmueble se ha eliminado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'El inmueble no se ha podido eliminar'
		);
	}

	echo json_encode($result);

});	

//Devolver un inmueble
$app->get('/inmueble/:id', function($id) use($db, $app){

	$sql = 'SELECT * FROM inmuebles WHERE id = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Inmueble no disponible'
	);

	if ($query->num_rows == 1) {
		$inmueble = $query->fetch_assoc();

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $inmueble
		);
	}

	echo json_encode($result);

});	

//Devolver inmuebles en oferta
$app->get('/inmuebles-oferta', function() use($db, $app){

	$sql = "SELECT * FROM inmuebles WHERE inmuebles.oferta = true;";
	$query = $db->query($sql);

	$inmuebles = array();
	while ( $inmueble = $query->fetch_assoc()) {
		$inmuebles[] = $inmueble;
	}

	if (sizeof($inmuebles) != 0) {

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $inmuebles
		);

	} else {

		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Error al conseguir lista de inmuebles con oferta'
		);

	}

	echo json_encode($result);

});

//Devolver todos los inmuebles
$app->get('/inmuebles', function() use($db, $app){

	$sql = "SELECT * FROM inmuebles ORDER BY id DESC;";
	$query = $db->query($sql);

	$inmuebles = array();
	while ( $inmueble = $query->fetch_assoc()) {
		$inmuebles[] = $inmueble;
	}

	if (sizeof($inmuebles) != 0) {

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $inmuebles
		);

	} else {

		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Error al conseguir lista inmuebles'
		);

	}

	echo json_encode($result);

});

//Guardar inmueble
$app->post('/inmueble', function() use($app, $db){

	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$oferta;
	$ascensor;
	$garaje;

	if ($data['oferta'] == false) {
		$oferta = "false";
	} else {
		$oferta = "true";
	}

	if ($data['ascensor'] == false) {
		$ascensor = "false";
	} else {
		$ascensor = "true";
	}

	if ($data['garaje'] == false) {
		$garaje = "false";
	} else {
		$garaje = "true";
	}

	$query = "INSERT INTO inmuebles VALUES(NULL,".
			"'{$data['titulo']}',".
			"'{$data['contenido']}',".
			"'{$data['tipo']}',".
			"'{$data['metros']}',".
			"'{$data['visitas']}',".
			"'{$data['direccion']}',".
			"'{$data['precio']}',".
			"'{$data['estado']}',".
			"$oferta,".
			"'{$data['antiguedad']}',".
			"'{$data['planta']}',".
			"$ascensor,".
			"$garaje,".
			"'{$data['first_img']}'".
			");";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Inmueble no se ha podido crear'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Inmueble creado con exito'
		);
	}

	echo json_encode($result);

});

//IMAGENES
//Guardar imagen inmueble
$app->post('/imagen', function() use($app, $db){

	$json = $app->request->post('json');
	$data = json_decode($json, true);

	if(!isset($data['code'])){
		$data['code'] = null;
	}

	if(!isset($data['ruta'])){
		$data['ruta'] = null;
	}

	$query = "INSERT INTO imagenes VALUES(NULL,".
			"'{$data['code']}',".
			"'{$data['ruta']}'".
			");";

	$insert = $db->query($query);

	var_dump($query);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Imagen no se ha podido crear'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Imagen creada con exito'
		);
	}

	echo json_encode($result);

});

//Devolver todas las imagenes de un inmueble
$app->get('/imagenes/:code', function($code) use($db, $app){

	$sql = "SELECT * FROM imagenes WHERE code = '$code';";
	$query = $db->query($sql);

	$imagenes = array();
	while ( $imagen = $query->fetch_assoc()) {
		$imagenes[] = $imagen;
	}

	if (sizeof($imagenes) != 0) {

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $imagenes
		);

	} else {

		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Error al conseguir lista de imagenes'
		);

	}

	echo json_encode($result);

});

//Elimina una imagen
$app->get('/delete-imagen/:id', function($id) use($db, $app){

	$sql = 'DELETE FROM imagenes WHERE id = '.$id;
	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Imagen BD eliminada correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'La imagen BD no se han podido eliminar'
		);
	}

	echo json_encode($result);

});	

//Elimina las imagenes del inmueble de la BD
$app->get('/delete-imagenes/:code', function($code) use($db, $app){

	$sql = 'DELETE FROM imagenes WHERE code = '.$code;
	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Imagenes BD eliminadas correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Las imagenes BD no se han podido eliminar'
		);
	}

	echo json_encode($result);

});	

//Elimina imagenes del directorio
$app->get('/delete-imagenes-dir/:name', function($name) use($db, $app){

	unlink('uploads/'.$name);

	$result = array(
		'status' => 'success',
		'code' => 200,
		'message' => 'Imagen eliminada del directorio'
	);

	echo json_encode($result);

});	

//ADMIN
//Devuelve los valores del admin
$app->get('/admin', function() use($db, $app){

	$sql = 'SELECT * FROM admin WHERE id = 1;';
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Admin no disponible'
	);

	if ($query->num_rows == 1) {
		$admin = $query->fetch_assoc();

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $admin
		);
	}

	echo json_encode($result);

});	

//Update active Admin
$app->get('/update-active-admin/:estado', function($estado) use($db, $app){

	$sql = "UPDATE admin SET active = $estado;";

	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Active se ha actualizado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Active no se ha podido actualizar'
		);
	}

	echo json_encode($result);

});

//Encriptar
$app->get('/encriptar', function() use($db, $app){

	$options = [
		'cost' => 11,
	];

	// Get the password from post
	$passwordFromPost = 'inmobiliaria123';
	
	$hash = password_hash($passwordFromPost, PASSWORD_BCRYPT, $options);

	var_dump($hash);

});	

$app->get('/verifyPasswd/:user/:passwdPost', function($user, $passwdPost) use($db, $app){

	$sql = 'SELECT * FROM admin WHERE id = 1;';
	$query = $db->query($sql);

	
	$admin = $query->fetch_assoc();

	if (password_verify($passwdPost, $admin['passwd']) && $user == $admin['usuario']) {
		
		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => true
		);

		echo json_encode($result);

	} else {
		
		$result = array(
			'status' => 'error',
			'code' => 404,
			'data' => false
		);

		echo json_encode($result);

	}

});	


//CORREO
//Enviar correo gmail
$app->post('/send-email', function() use($db, $app){

	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$mail = new PHPMailer(true);

	try {
		//Server settings
		$mail->SMTPDebug = 2;                                       // Enable verbose debug output
		$mail->isSMTP();                                            // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->Username   = 'victorvalenciahi@gmail.com';                     // SMTP username
		$mail->Password   = 'inmobiliaria123';                               // SMTP password
		$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = 587;                                    // TCP port to connect to

		//Recipients
		$mail->setFrom($data['correo'], $data['nombre'].' '.$data['apellidos']);
		$mail->addAddress('victorvalenciahi@gmail.com', 'Victor');     // Add a recipient

		// Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $data['asunto'];
		$mail->Body    = $data['mensaje'] . '<br><br><br>' . $data['correo'];

		$mail->send();

		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Mensaje enviado con exito'
		);

		echo json_encode($result);

	} catch (Exception $e) {

		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'Message could not be sent. Mailer Error: {$mail->ErrorInfo}'
		);

		echo json_encode($result);
	}

});	

$app->run();
