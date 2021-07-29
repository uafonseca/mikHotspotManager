#MikHostpotManager

Poceso de instlación
Asumiendo que existen todas las dependencias necesarias para la ejecucción del sistema(Basado en Symfony v5.3.3)

1 Clonar el repositorio
```sh
$ git clone https://github.com/uafonseca/mikHotspotManager.git
```

2 Insalar las dependencias con composer
```sh
$ composer install
```
3 Insalar las dependencias de yarn
```sh
$ yarn install
```

4 Compilar las dependencias
```sh
$ yar build
```

5 Generar el archivo de rutas
```sh
$ php bin/console fos:js-routing:dump --target /public/bundles/fosjsrouting/js/fos_js_routing.js
```

5 Modificar la configuración de BD en el archivo .env 
`DATABASE_URL="mysql://<USUARIO-BASE-DE-DEDATOS>:<CONTRASEÑA-BASE-DE-DATOS>@127.0.0.1:3306/<NOMBRE-BASE-DE-DATOS>"`

6 Actualizar la base de datos
```sh
$ php bin/console do:s:u --force
```
  
7 Generar datos por defecto para acceder al sistema
```sh
$ php bin/console doctrine:fixtures:load
```
  
7 Configurar un servidor web para acceder al sistema(Recomendacion Nginx https://www.nginx.com/resources/wiki/start/topics/recipes/symfony/)
