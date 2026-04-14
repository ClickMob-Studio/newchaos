# Running ChaosCity Locally
This guide will share how to setup a development environment for ChaosCity locally on a MacBook.

Requirements:
- Homebrew

Alternatively you can follow this post on KittMedia: https://kittmedia.com/en/2021/macos-install-nginx-mysql-and-php-via-brew/

## Setup NGINX
First of all, we need to install nginx using brew, change the configuration to support PHP, and point it to a folder we can easily locate and later on will clone our Git repository into.

To install nginx simply run the following command:
```bash
brew install nginx
```

After installation it will tell you where the `nginx.conf` is located, you can open it up with VIM or Visual Studio Code, or whatever editor you prefer.

In my case I opened it up in VSCode simply by running this command:
```bash
code /opt/homebrew/etc/nginx/nginx.conf
```

First you can locate this block:
```conf
        location / {
            root   html;
            index  index.html index.htm;
        }
```

We need to add support for PHP, and point the server to a locatable path. 

In my case I simply created a folder called `nginx` in my user directory (root for my user).

Replace the above block with something like this:

```conf
        root /Users/mathias/nginx;
        index index.php index.html index.htm;
        location / {
            autoindex on;
            try_files $uri $uri/ /index.php?$args;
            proxy_buffer_size 128k;
            proxy_buffers 4 256k;
            proxy_busy_buffers_size 256k;
        }
        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
```

Notice that we also add rules for .php files, this is to prepare for when we install PHP later on.

One final thing we can do, nginx by default runs on port 8080, this port can usually only be accessed with administrator rights, so we can change it to run on port 80.

Change the port to 80 in this line:
```conf
listen	8080;
```

Now you can create a simple index.html file in the location you configured in root, run the nginx service with this command and double check it works:
```bash
brew services run nginx
```

## Setup MySQL
Leveraging brew once again, we can install MySQL simply by running these commands:
```bash
brew install mysql
brew services start mysql
mysql_secure_installation
```

Answer the questions to the secure installation, and once completed test your login with the following command:
```bash
mysql -u root -p
```

## Setup PHP
As our server is running PHP8.1 we will install the same version for compatibility.
Simply install and run using brew:
```bash
brew install php@8.1
``` 

## Setup Redis & PHP Redis
We rely on Redis for caching of long lived data, to optimise performance and lower our queries to the database for trivial fetching.

We can install and run redis using brew again:
```bash
brew install redis
brew services start redis
```

Next we need to install and add the PHP Redis extension.

Clone the phpredis repository and run the install command:
```bash
git clone https://www.github.com/phpredis/phpredis.git
cd phpredis
phpize && ./configure && make && sudo make install
```

Now we can configure the `php.ini` to add the extension, mine is located in `opt/homebrew/etc/php/8.1/php.ini`.

Add this line to the extensions section:
```
 extension=redis.so
```

Lastly we can restart php service and run the `make test` inside the phpredis directory.
```bash
brew services restart php@8.1
make test
```

## Clone Chaos City into Working Folder
Now we can simply clone our game repository into the working folder that you defined previously for the root of your nginx configuration.

Locate the folder and inside of it delete any file you might have created such as index.php or index.html when testing the configuration.

Simply run:
```bash
git clone https://github.com/ClickMob-Studio/newchaos .
```

And the game will be up and running.

## Creating MySQL User
I find it convenient to have a MySQL user with the same name as the one used in production (chaoscit_user).

We can run two queries in MySQL to set this up smoothly:
```bash
CREATE USER 'chaoscit_user'@'localhost' IDENTIFIED WITH caching_sha2_password BY ‘SOME_PASSWORD’;
GRANT ALL ON `chaoscit_game`.* TO 'chaoscit_user'@'localhost';
```

You can either use a tool such as MySQL Workbench, or simply run these commands inside mysql. 

You can connect to mysql using the following command:
```bash
mysql -u root -p
```

## Importing Database Tables
Simply run the `chaoscit_game.sql` which contains the database structure and test data.

Credentials for the test user:

Username: testuser
Password: password123 

