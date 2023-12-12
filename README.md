<h1 align="center">MDCRYPT</h1>

# Requisitos

- PHP >= 7.4
- Laravel >= 8

# Instalación

Vía composer
```shell
composer require onamanzi/mdcrypt
```
Para publicar la configuración
```shell
php artisan vendor:publish --provider="Onamanzi\Mdcrypt\MdcryptServiceProvider" --tag=mdcrypt-config
```
# Configuración
Una vez publicado el archivo de configuración, se deben registrar los vectores de inicialización (iv) y las contraseñas (pass) de los proyectos dentro de la llave `keys`

```
'keys' => [
	'pass' => [
		'mi_proyecto' => env('MI_PROYECTO_PASS')
	],
	'iv' => [
		'mi_proyecto' => env('MI_PROYECTO_IV')
	]
]
```
La longitud de `iv` debe de ser de 16 caracteres.

El método de encriptación es por defecto `AES-128-CBC` pero puede ser modificado a través de la variable de entorno `CRYPT_METHOD` en el archivo .env
```
CRYPT_METHOD="AES-256-CTR"
```
# Uso
Para encriptar o desencriptar es necesario crear una nueva instancia de Mdcrypt y pasarle el nombre del proyecto.

```
use Onamanzi\Mdcrypt\Mdcrypt;

$crypt = new Mdcrypt('mi_proyecto');
```

## Encriptar
El método `encrypt` encriptara la cadena recibida.
```
$stringToEncrypt = "Soy una cadena de texto";

$crypt->encrypt($stringToEncrypt);
```
Para obtener la cadena encriptada hay que usar el método `getResult`.
```
$string = $crypt->getResult(); //"g9CllWBEg7Bw-cTom6GvbHF0DKXSVD3u3zeuCU0jGpI,"
```
## Desencriptar
La desencriptación de se realiza con el método `decrypt`.
```
$crypt = new Mdcrypt('mi_proyecto');

$crypt->decrypt("g9CllWBEg7Bw-cTom6GvbHF0DKXSVD3u3zeuCU0jGpI,");
```
De igual manera que en la [encriptacion](#encriptar) el resultado se obtendria con `getResult`.
```
$crypt->getResult(); //"Soy una cadena de texto"
```
## Manejo de Arrays
Es posible encriptar arrays con el metodo `encrypt`, para eso solo hay que convertir el `array` a JSON.
```
$arrayToEncrypt = array(
	"mensaje1" => "Soy una cadena de texto",
	"mensaje2" => "Soy otra cadena de texto"
);

$crypt->encrypt(json_encode($arrayToEncrypt));

$string = $crypt->getResult();
```
La desencriptación se realiza igual que lo descrito en [desencriptar](#desencriptar). Para obtener el resultado como un `array`, el método `getResult` acepta como parámetro "array" con lo cual devolverá la cadena desencriptada como un `array`.

```
 $crypt->decrypt($string);

 $crypt->getResult("array");
```
## Manejo de errores
Al encriptar o desencriptar es posible que surja algún error, es posible obtener los errores con el método `getErrors`.
Algunos de los posibles errores son los siguientes:

1. <strong>Error 003:</strong> Fallo al decodificar json
2. <strong>Error 004:</strong> Fallo al encriptar
3. <strong>Error 005:</strong> Fallo al desencriptar

## Validación del Request
Mdcrypt es capaz de validar el `request` recibido antes de desencriptar el contenido, para ello se utiliza el método `validateRequest`.
Si el `request` recibido no es valido `validateRequest` retornara `false`.
```
$crypt = new Mdcrypt('mi_proyecto');

$isValid = $crypt->validateRequest($request);
```
Para obtener el `request` validado.
```
$string = $crypt->getValidRequest();
```
El método `getValidRequest` devolverá la cadena encriptada lista para ser procesada como se vio en [desencriptar](#desencriptar).

También es posible validar el contenido desencriptado si este es un `array`, para ello Mdcrypt hace uso de la clase `Validator` de Laravel y es posible acceder a la función con el método `validator` de Mdcrypt.
```
$result = $crypt->getResult('array');
$rules = array(
	'mensaje1' => 'required'
);
$isValid = $crypt->validator($result,$rules);
```
Si los datos recibidos son validos `validator` retornara `true` de lo contrario `false`.<br>
Si `validator` retorna `false` los errores pueden ser obtenidos con `getRequestErrors` el cual retornara un `array` con los errores obtenidos durante la validación de los datos recibidos.
### Manejo de errores
A continuación se muestras los posibles errores al validar el `request`.

1. <strong>Error 000:</strong> Faltan parámetros
2. <strong>Error 001:</strong> Mas de un parámetro
3. <strong>Error 002:</strong> Request vacío
4. <strong>Errores de validación:</strong> Dependen de las reglas aplicadas.