# Rankings

## Cómo instalar

Para instalar la aplicación necesitaremos seguir los siguientes pasos [1]:

  1. Instalar las dependencias con los siguientes comandos:
  
   ```bash
   sudo apt-get install apache2
   sudo apt-get install php5 php5-cli php5-mysql
   sudo apt-get install php5-curl
   sudo apt-get install mysql-client mysql-server
   ```
   
      Durante la instalación de la última dependencia se nos pedirá la contraseña del usuario `root` de la base de datos MySQL.

  2. Creación de un nuevo usuario para la base de datos. Entramos en MySQL con como root con el comando
  
    ```bash
    mysql -u root -p
    ```
    
      Nos pedirá la contraseña del usuario `root` que hemos configurado durante el paso anterior.
      
      2.1. Escribimos el siguiente comando para crear un nuevo usuario:
      
      ```sql
      CREATE USER 'rankings'@'localhost' IDENTIFIED by '1234';
      ```
      
      donde `1234` es la contraseña del usuario `rankings`.
      
  3. Habilitar el módulo `mod_rewrite` de Apache [2]
      
      3.1. Escribimos los siguientes comandos

      ```bash
      sudo a2enmod rewrite
      sudo service apache2 restart
      ```
    
      3.2. Ejecutamos los comandos
      
      ```bash
     sudo nano /etc/apache2/sites-available/default
      ```
      
      y buscamos en el archivo `<Directory /var/www/>`.
      
      3.3. Sustituimos el contenido de `<Directory /var/www/>` por lo siguiente
    
      ```
        <Directory /var/www/>
              Options Indexes FollowSymLinks MultiViews
              # changed from None to FileInfo
              AllowOverride FileInfo
              Order allow,deny
              allow from all
        </Directory>
      ```
      
      3.4. Reiniciamos Apache con 
      
      ```bash
      sudo service apache2 restart
      ```
  
  4. Instalación de Bower

      4.1. Descargar node desde su página oficial https://nodejs.org
      
      4.2. Instalar npm (si no viene instalado junto a node)
      
      4.3. Ejecutar el comando `npm install -g bower`
      
      4.4. Para comprobar que tenemos instalado Bower ejecutar `bower -v`. Si nos aparece el número de versión, está instalado.
      
      4.5. Ir a la carpeta `web` y ejecutar el comando `bower install`. Nos instalará todas las dependencias necesarias.
      
  5. Instalación de Composer
  
      5.1. Ejecutar los siguientes comandos

      ```bash
      curl -sS https://getcomposer.org/installer | php
      mv composer.phar /usr/local/bin/composer
      ```
      
      5.2. Ir a la carpeta `web` y ejecutar el comando `composer install`. Nos instalará todas las dependencias necesarias.
      
  6. Creación de las tablas de la base de datos y carga de los datos
  
    6.1. Vamos a la carpeta `parser` y escribimos 

    ```
    mysql -u rankings -p
    source rankings.sql
    source datos.sql
    ```
  7. Movemos la carpeta de la aplicación hacia la carpeta `var/www/html`.
  8. Abrimos un navegador y escribimos `localhost`. Si todo ha ido bien, tendremos la aplicación funcionando.




[1]: De ahora en adelante, supondremos que estamos en un entorno Linux (en particular un entorno Ubuntu o derivados) y que se dispone de un gestor de paquetes.
[2]: http://askubuntu.com/questions/48362/how-to-enable-mod-rewrite-in-apache
