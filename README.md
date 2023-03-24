# trical-server-api
<br>
This is a server rest api for trical electronic system
<br>
How to run the server
<br>
<br>
#Step 1
<br>
<br>
---Windows----
<br>
<br>
Download Xampp on windows this will come with php , apache and mysql preconfigured
Add the filepath of "C://Xampp/php" and "C://Xampp/mysql/bin" to the environment  variable path
Download "vscode" or any editor of your choice
<br>
<br>
----linux----
<br>
<br>
Download "php 8.0 and mysql and configure apache server"
Download "vcode" or any editor of your choice
<br>
<br>
#Step 2
<br>
<br>
---windows---
<br>
<br>
Copy the project to "C://xampp/htdocs"
<br>
<br>
----linux-----
<br>
<br>
You can use the directory of your choice
<br>
<br>
#Step 3
<br>
<br>
----windows---
<br>
<br>
Run the "Xampp as an Administrator" and start apache and mysql
Click on Admin next to Mysql to open Admin panel
Create a "database name called "trical""
import "trical.sql" from the project to the database
<br>
<br>
-----linux-----
<br>
<br>
From the terminal,
Start your apache server by running "sudo systemctl start apache2.service" on debian, other distro do research
Login to your mysql with "mysql -u root -p"
Create a database with "CREATE DATABASE trical;"
Import "trical.sql" to to your database from the project
<br>
<br>
#Step 4
<br>
<br>
-----windows-----
<br>
<br>
From command prompt running as administrator from the project directory, run 
"php -S 127.0.0.1:8080" (use another port if 3000 is taken)
Use thunder-client a vscode extension or insominia to test the api endpoint
<br>
<br>
----linux----
<br>
<br>
Run from terminal in the project directory, run
"sudo php -S 127.0.0.1:8080" (use another port if 3000 is taken)
Use thunder-client a vscode extension or insominia to test the api endpoint
<br>
<br>
