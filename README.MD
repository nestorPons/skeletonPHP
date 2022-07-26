# SkeletonPHP AXAJ-PHP

Sistema de carga rapido por módulos. 
Carga modular de cualquier contenido sin tener que repetir la total carga de todo el contenido de la aplicación.

## Instalación 
```
git clone https://github.com/nestorPons/skeletonPHP.git
cd skeletonPHP
```

## Instalación de dependencias php
Debe de instalar las dependencias con composer de PHP:
```composer install```   

## Inicio servidor de desarroyo
El proyecto dispone de un servidor LAMP Docker para pruebas de desarroyo:    
[README.md del servidor](.server/README.md)

## Manual de usurio 
Consulte nuestro manual de usuario:  
[Manual](https://nestorpons.github.io/skeletonPHP/)

## Configuración 
Puede configurar la aplicación en:  
```config/config.ini``` 

## Edita el archivo README.md 

## Cambiar el origin
### Eliminar viejo origen
```git remote rm origin```
### Agregar nuevo origen
```git remote add origin git@github.com:tu/proyecto.git```
### Configurarle como master
```git config master.remote origin```
### Configura el rastreo de la rama principal 
```git branch --set-upstream-to=origin/main main```
### Vincula los repositorios 
```git pull --allow-unrelated-histories```
### Sube los archivos Skeleton en el nuevo repositorio
```git push --set-upstream origin main```