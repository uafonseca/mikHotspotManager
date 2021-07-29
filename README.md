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
