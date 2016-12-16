#Steps to integrate blueimp in codeigniter.

* **1. change path of controller where upload code is writen.**
           
           public->assets->js->main->main.js
           $('#fileupload').fileupload({ 
              url: 'http://localhost/codeigniter/index.php/uploads/upload'
           });

* **2. create upoloads folder in main directory.**

            MY-Folder/uploads

* **3. create thumbnail folder in uploads folder.**

            My-Folder/uploads/thumbnail

* **4. Define URL and path for upload in constants under**

            application->config->constants.php
            /*
            * Define file upload url and file upload path.
            *
            */
            define('UPLOAD_URL','http://localhost/codeigniter/uploads/');
            define('UPLOAD_PATH','/var/www/html/codeigniter/uploads/');
            define('UPLOAD_THUMBNAIL_URL','http://localhost/codeigniter/uploads/thumbnail/');
            define('UPLOAD_THUMBNAIL_PATH','/var/www/html/codeigniter/uploads/thumbnail/');
            define('UPLOAD_DELETE_URL','http://localhost/codeigniter/index.php/uploads/delete/');
