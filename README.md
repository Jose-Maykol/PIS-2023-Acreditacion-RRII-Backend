# Acreditacion-RRII-Backend


### Configuracion e installacion
1. Instalar php version 8.0.0 o superior, pagina oficial de descarga [Link]('https://www.php.net/downloads')
2. Instalar composer click [aqui]('https://getcomposer.org/doc/00-intro.md')
3. Clonar el proyecto del repositorio
```
git clone github_link_repository
```
4. Ejecutar el comando para instalar todas las dependencias necesarias 
```
composer install
```
5. copiar y renombrar el template de `.env.template` a `.env`
6. Cambiar valores de las variables de entorno
7. Levantar la base de datos
```
docker-compose up -d
```
8. Generar la clave de la aplicación
```
php artisan key:generate
```
9. Ejecutar las migraciones
```
php artisan migrate
```
10. Cargar los seeders
```
php artisan db:seed
```
11. levantar la aplicacion
```
php artisan serve
```

<h3 style='color:red;'>IMPORTANTE !!!!</h3>

Posibles problemas al ejecutar los comandos anteriores, forma de solucionar modificando el `php.ini` y descomentando estas lineas

- extension=fileinfo
- extension=pdo_pgsql
- extension=pgsql