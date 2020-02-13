# P7-project
**Openclassrooms Symfony REST API project**

Demo API base URL: https://cunvergenza.org/api/  
API auto-generated documentation: https://cunvergenza.org/api/doc  
API comprehensive user manual: https://cunvergenza.org/files/User_manual.pdf
    
Demo API OAuth client connection details:  
&nbsp; &nbsp; *Method:* POST  
&nbsp; &nbsp; *URL:* https://cunvergenza.org/oauth/v2/token  
&nbsp; &nbsp; *Body:*  
        
        {
          "grant_type": "password",
          "client_id": "1_zlnvh5uocons8epn5yuc",
          "client_secret": "vpkemq2bfdwkmxmdq1cypnqko04ueb10fyfhgppn",
          "username": "myshop1",
          "password": "myshop1"
        }

Codacy and Codeclimate code quality analysis are accessible here:  
- https://app.codacy.com/manual/ericc06/P7-project/dashboard  
- https://codeclimate.com/github/ericc06/P7-project  

# To install the application on a production server from Github:

1. Being in the root directory of the API web site, clone the Github repository:

        git clone -b master git@github.com:ericc06/p7-project.git .

2. Install all packages:

        composer install

3. Edit the ".env" file located in the root directory of the project to enter your database details. Example:

        DATABASE_URL=mysql://user:pwd@127.0.0.1:3306/db_name
        DATABASE_USER=...
        DATABASE_PWD=...
        DATABASE_NAME=...
        DATABASE_HOST=127.0.0.1

4. Always in the ".env" file, ensure that "APP_ENV" is set to "dev" if you plan to use the provided fixtures to load data into the database.

5. Ensure that no table already exists in the database, and generate the database schema:

        php bin/console doctrine:schema:create

6. Create your database content (products, resellers...) or load the provided fixtures:

        php bin/console doctrine:fixtures:load

7. Make the "/public" folder the root directory of your web site (through cPanel or your usual web site manager).

8. Install an SSL certificate on your domain.

9. Force HTTPS redirection, for example by adding the following lines at the beginning of the .htaccess file located in the "/public" folder of the web site (replace domaine.tld with your domain name and extention):

        # Redirecting to HTTPS
        RewriteCond %{SERVER_PORT} ^80$ [OR]
        RewriteCond %{HTTPS} =off
        RewriteRule ^(.*)$ https://domaine.tld/$1 [R=301,L]

        # Redirecting www to non-www on HTTPS
        RewriteCond %{HTTP_HOST} ^www\.domaine\.tld [NC]
        RewriteRule ^(.*)$ https://domaine.tld/$1 [R=301,L]

10. In order to use the API in production mode, edit the ".env" file and set "APP_ENV" to "prod", and "APP_DEBUG" to 0.

11. To check that the web site is responding well, go to the API documentation page to see it appear:

        https://<domain>/api/doc

12. A comprehensive user manual in English can be found here:

        https://<domain>/files/User_manual.pdf
