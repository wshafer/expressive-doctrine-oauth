# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

@script = <<SCRIPT
# Fix for https://bugs.launchpad.net/ubuntu/+source/livecd-rootfs/+bug/1561250
if ! grep -q "ubuntu-xenial" /etc/hosts; then
    echo "127.1.0.1 ubuntu-xenial" >> /etc/hosts;
fi

# Update Apt
DEBIAN_FRONTEND=noninteractive apt-get update;
DEBIAN_FRONTEND=noninteractive apt-get install -y software-properties-common python-software-properties;
DEBIAN_FRONTEND=noninteractive apt-get install -y language-pack-en-base;

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root';
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root';


# Install Core packages
DEBIAN_FRONTEND=noninteractive apt-get update
DEBIAN_FRONTEND=noninteractive apt-get install -y \
    bash-completion \
    apt-transport-https \
    build-essential \
    mysql-server \
    curl \
    wget \
    nginx \
    libsodium-dev;

# Install PHP Core and Modules
DEBIAN_FRONTEND=noninteractive LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php 2>&1
DEBIAN_FRONTEND=noninteractive apt-get update
DEBIAN_FRONTEND=noninteractive apt-get install -y \
    php7.2-fpm \
    php7.2-cli \
    php7.2-dev \
    php-pear \
    php-memcached \
    php-redis \
    php7.2-snmp \
    php7.2-tidy \
    php7.2-xmlrpc \
    php7.2-bcmath \
    php7.2-bz2 \
    php7.2-curl \
    php7.2-intl \
    php7.2-json \
    php7.2-mbstring \
    php7.2-opcache \
    php7.2-soap \
    php7.2-xml \
    php7.2-zip \
    php7.2-mysql \
    php7.2-imap \
    php7.2-snmp \
    php-xdebug

# Prep Environment
chmod -R 777 /var/www/data;
sed -i 's/www-data/vagrant/g' /etc/nginx/nginx.conf;
sed -i 's/www-data/vagrant/g' /etc/php/7.2/fpm/pool.d/www.conf;
sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/7.2/fpm/php.ini;
sed -i 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/g' /etc/php/7.2/fpm/php.ini;
sed -i 's/127.0.0.1/0.0.0.0/g' /etc/mysql/mysql.conf.d/mysqld.cnf

echo "
xdebug.remote_enable = on
xdebug.remote_connect_back = 1

# Some Misc Settings that can help
;xdebug.collect_vars = on
;xdebug.collect_params = 4
;xdebug.dump_globals = on
;xdebug.show_local_vars = on

;xdebug.dump.SERVER = on
;xdebug.dump.POST = on
;xdebug.dump.GET = on
;xdebug.dump.SESSION = on
;xdebug.dump.COOKIE = on

;xdebug.var_display_max_depth = -1
;xdebug.var_display_max_children = -1
;xdebug.var_display_max_data = -1" >> /etc/php/7.2/mods-available/xdebug.ini

phpenmod xdebug

# Configure Opcache for production "like" settings
echo "
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable_cli=1
" >> /etc/php/7.2/mods-available/opcache.ini

# Now lets turn off opcache for development
phpdismod opcache;

# Create a default db to work with
echo "CREATE DATABASE local DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;" | mysql -u root -proot

# Create remote root user to work with
echo "CREATE USER 'vagrant'@'%' IDENTIFIED BY 'vagrant';" | mysql -u root -proot
echo "GRANT ALL PRIVILEGES ON *.* TO 'vagrant'@'%' WITH GRANT OPTION;" | mysql -u root -proot

# Configure Nginx
cat > /etc/nginx/sites-available/default << 'EOL'
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /var/www/public;

    # Add index.php to the list if you are using PHP
    index index.php index.html index.htm index.nginx-debian.html;

    server_name _;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.2-fpm.sock;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOL


# Restart services
service nginx restart;
service php7.2-fpm restart;
service mysql restart;


# Install Composer
if [ -e /usr/local/bin/composer ]; then
    /usr/local/bin/composer self-update;
else
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer;
fi

# Reset home directory of vagrant user
if ! grep -q "cd /var/www" /home/vagrant/.profile; then
    echo "cd /var/www" >> /home/vagrant/.profile;
fi

echo "** [ZF] Run the following command to install dependencies, if you have not already:"
echo "    vagrant ssh -c 'composer install'"
echo "** [ZF] Visit http://localhost:8080 in your browser for to view the application **"
SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = 'bento/ubuntu-16.04'
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 3306, host: 3306
  config.vm.network "private_network", type: "dhcp"
  config.vm.synced_folder '.', '/var/www'
  config.vm.provision 'shell', inline: @script

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    vb.customize ["modifyvm", :id, "--name", "Expressive Oauth - Ubuntu 16.04"]
  end
end
