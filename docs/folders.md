# Estructura 
| Carpetas y archivos | Descripción |
| ----------- | ----------- |
**.server/**                    |**Servidor LAMP Docker para desarroyo** 
.server/conf/                   | Directorio de configuración del servidor docker
.server/conf/000-default.conf   | Archivo de configuración por defecto
.server/conf/apache2.conf       | Archivo de configuración servidor Apache
.server/conf/php.ini            | Archivo de configuración PHP
.server/docker-compose.yml      | Archivo de configuración docker-composer
.server/Dockerfile              | Archivo de configuración Docker
.server/logs                    | Carpeta donde se almacenan los históricos del servidor
.server/README.md               | Instrucciones para instalarr y lanzar el servidor
**app/**                        | **Cuerpo de la aplicación**  
app/config/                     | Archivos de configuración editables  
app/config/config.ini           | Archivo de configuración de la aplicación (EDITABLE)  
app/core/                       | Carpeta de las clases principales del proyecto
app/core/Component.php          | Clase creadora de los componentes Skeleton
app/core/Controller.php         | Clase padre de los controladore
app/core/Data.php               | Clase de tratamiento de datos 
app/core/Error.php              | Clase de errores 
app/core/Prepocessor.php        | Clase que preprocesa las plantillas y los componentes
app/core/Query.php              | Clase para la conexión a base de datos MYSQL
app/core/Router.php             | Clase enrutadora de las peticiones que se formulan al servidor
app/core/Security.php           | Clase que proporciona la seguridad a la aplicación 
app/core/Tag.php                | Clase para trabajar con los objetos html
app/core/ToolsComponents.php    | Clase de para el trabajo con patrones de sintaxis
app/db/                         | Carpeta contenedora de archivos sql
app/helpers                     | Carpeta contenedora de funciones auxiliares
app/libs                        | Librerias externas
**docs/**                       | **Documentación del proyecto en Marckdown**  
**site/**                       | **Sitio web autogenerado con la documentación**
**htdocs/**                     | **Raiz de los archivos públicos**   
htdocs/index.php                | Entrada principal de la aplicación
htdocs/package.json             | Archivo de configuración npm 
htdocs/www/                     | Contenedor de los archivos públicos autogenerados     
**src/**                        | **Nuestro código modelo MVC**    
src/controllers/                | Contendrá los controladores necesarios
src/js/                         | Contendrá los archivos js requeridos
src/models/                     | Contendrá los modelos
src/mycomponents/               | Carpeta contenedora de los componentes personalizados/   
src/styles/                     | Carpeta con los estilos de la aplicación
src/views/                      | Carpeta de las vistas
**vendor/**                     | Carpeta de composer
.gitignore                      | Archivo de configuración git 
composer.json                   | Archivo configuración composer
LICENSE.md                      | Archivo de la licencia
mkdocks.yml                     | Archivo de configuración de mkdocs para la documentación
README.md                       | Archivo de instrucciones