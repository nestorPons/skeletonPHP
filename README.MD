# Skeleton AXAJ-PHP

Sistema de carga rapido por módulos. 
Carga modular de cualquier contenido sin tener que repetir la total carga de todo el contenido de la aplicación.

├── app                     → Cuerpo de la aplicación
│   ├── config              → Archivos de configuración editables 
│   ├── core
│   ├── db
│   ├── helpers
│   └── libs          
├── composer.json
├── composer.lock
├── htdocs                  → Raiz de los archivos públicos 
│   ├── index.php
│   ├── package.json
│   ├── package-lock.json
│   └── www                 → Contenedor de los archivos públicos compilados 
├── README.MD
├── src                     → Codigo de nuestra aplicación modelo MVC
│   ├── controllers
│   ├── js
│   ├── models
│   ├── mycomponents         → Componentes  
│   ├── styles
│   └── views
└── vendor


### Configuración 
Puede configurar la aplicación en:
```
config/config.ini
```
