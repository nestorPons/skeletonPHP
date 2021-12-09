# docker-lamp

Ejemplo de Docker con Apache, MySql 8.0, PhpMyAdmin y PHP.  
Puede usar MySql 5.7 si utiliza la etiqueta 'mysql5.7'.  
Se utiliza docker-compose como herramienta para facilitar el uso de Docker.  

Acceda a esta carpeta:  
```cd su_proyecto\.server```  

Asegurese de que el demonio Docker esta activado:  
Arch-linux  
```systemctl status docker```  

Si fuera necesario activelo:   
```systemctl start docker```  

Para ejecutar los contenedores:  
``` sudo docker-compose up -d ```    

Abrimos phpmyadmin en [http://localhost:8080](http://localhost:8080)  
La pagina web la encontramos en [http://localhost](http://localhost)  

Ejecute el cliente mysql:  
``` sudo docker-compose exec db mysql -u root -p ```  


usuario: root  
contraseña: test   
 
Puede encontrar en .htaccess.local la configuración para el servidor Apache.   

¡A Disfrutar!
