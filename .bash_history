sudo yum update -y
sudo yum install -y httpd mariadb-server php php-mysqlnd
sudo yum install -y httpd mysql-server php php-mysqlnd
sudo yum install -y httpd mariadb105-server php php-mysqlnd
sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl start mariadb
sudo systemctl enable mariadb
cd /var/www/html
sudo wget https://wordpress.org/latest.tar.gz
sudo tar -xzf latest.tar.gz
sudo mv wordpress/* .
sudo rm -rf wordpress latest.tar.gz
sudo chown -R apache:apache /var/www/html
sudo chmod -R 755 /var/www/html
sudo mysql_secure_installation
sudo mysql -u root -p
cd /var/www/html
sudo cp wp-config-sample.php wp-config.php
sudo nano wp-config.php
sudo systemctl status mariadb
SHOW GRANTS FOR 'wp_user'@'localhost';
sudo mysql -u root -p
sudo nano /var/www/html/wp-config.php
sudo mysql -u root -p
cd /var/www/html
sudo git init
sudo yum install git -y
git --version
sudo git init
sudo nano .gitignore
sudo git add .
sudo git config --global --add safe.directory /var/www/html
sudo git add .
sudo git commit -m "Initial commit of WordPress site"
sudo git remote add origin https://github.com/KLEPIO/Password-Gen.git
sudo git branch -M main
sudo git push -u origin main
sudo tail -n 100 /var/log/httpd/error_log
sudo systemctl restart httpd
sudo nano /etc/php.ini
sudo systemctl restart httpd
sudo chown -R ec2-user:apache /var/www/html
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo nano /var/www/html/.htaccess
sudo nano /etc/php-fpm.d/www.conf
sudo systemctl restart php-fpm
sudo nano /var/www/html/wp-config.php
mv /var/www/html/wp-content/plugins /var/www/html/wp-content/plugins-bak
mv /var/www/html/wp-content/plugins-bak /var/www/html/wp-content/plugins
mv /var/www/html/wp-content/plugins /var/www/html/wp-content/plugins-bak
mv /var/www/html/wp-content/plugins-bak /var/www/html/wp-content/plugins
cd /var/www/html/wp-content/plugins
ls
mkdir ../plugins-bak
mv akismet hello.php index.php my-custom-plugin password-generator ultimate-member wpide ../plugins-bak/
mv ../plugins-bak/akismet .
mv ../plugins-bak/hello.php .
mv ../plugins-bak/index.php .
mv ../plugins-bak/my-custom-plugin .
mv ../plugins-bak/password-generator .
mv ../plugins-bak/ultimate-member .
mv akismet hello.php index.php my-custom-plugin password-generator ultimate-member wpide ../plugins-bak/
ls /var/www/html/wp-content/plugins
ls /var/www/html/wp-content/plugins-bak
sudo mv /var/www/html/wp-content/plugins-bak/akismet /var/www/html/wp-content/plugins/
sudo mv /var/www/html/wp-content/plugins-bak/hello.php /var/www/html/wp-content/plugins/
sudo mv /var/www/html/wp-content/plugins-bak/index.php /var/www/html/wp-content/plugins/
sudo mv /var/www/html/wp-content/plugins-bak/my-custom-plugin /var/www/html/wp-content/plugins/
sudo mv /var/www/html/wp-content/plugins-bak/password-generator /var/www/html/wp-content/plugins/
sudo mv /var/www/html/wp-content/plugins-bak/wpide /var/www/html/wp-content/plugins/
sudo chmod -R 755 /var/www/html/wp-content/plugins
sudo chown -R apache:apache /var/www/html/wp-content/plugins
ls -l /var/www/html/wp-content/plugins
sudo tail -n 100 /var/log/httpd/error_log
cd /var/www/html
sudo nano fix-plugins.php
sudo rm /var/www/html/fix-plugins.php
sudo nano fix-plugins.php
sudo rm /var/www/html/fix-plugins.php
sudo nano reset-admin-capabilities.php
sudo rm /var/www/html/reset-admin-capabilities.php
brew install duck
cd /var/www/html/wp-content/themes/twentytwentythree
sudo nano functions.php
cd /var/www/html/wp-content/themes/
ls -l
cd /var/www/html/wp-content/themes/twentytwentytwo
sudo nano functions.php
cd /var/www/html/wp-content/themes/twentytwentyfour
sudo nano functions.php
cd /var/www/html/wp-content/themes/twentytwentytwo
sudo nano functions.php
cd /var/www/html/wp-content/themes/twentytwentyfour
sudo nano functions.php
nano functions.php
cd /var/www/html/wp-content/themes/twentytwentytwo
nano functions.php
cd /var/www/html/wp-content/themes/twentytwentythree
nano functions.php
cd /var/www/html/wp-content/themes/index.php
cd /var/www/html/wp-content/themes/hello-elementor
nano functions.php
cd /var/www/html/wp-content/themes
ls -l
cd /var/www/html/wp-content/themes/twentytwentythree
nano functions.php
cd /var/www/html/wp-content/themes/hello-elementor
nano functions.php
cd /var/www/html/wp-content/themes/twentytwentytwo
nano functions.php
cd /var/www/html/wp-content/themes/twentytwentyfour
nano functions.php
cd /var/www/html/wp-content/themes/twentytwentytwo
nano functions.php
cd /var/www/html/wp-content/themes/hello-elementor
nano functions.php
cd /var/www/html/wp-content/themes/
ls -l
cat theme-directory/style.css
cat hello-elementor/style.css 
mysql -u wp_user -p
cd /var/www/html/wp-content/themes/hello-elementor
sudo nano functions.php
cd /var/www/html
sudo nano wp-config.php
sudo adduser ftpuser
sudo passwd ftpuser
sudo usermod -aG apache ftpuser
sudo chown -R ftpuser:apache /var/www/html
sudo chmod -R 775 /var/www/html
sudo yum install vsftpd
sudo systemctl start vsftpd
sudo systemctl enable vsftpd
sudo nano /etc/vsftpd/vsftpd.conf
sudo systemctl restart vsftpd
sudo systemctl status vsftpd
sudo nano /var/www/html/wp-config.php
sudo chown -R ftpuser:apache /var/www/html
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo nano /var/www/html/wp-config.php
sudo chown -R apache:apache /var/www/html
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo nano /var/www/html/wp-config.php
tail -f /path/to/your/wordpress/wp-content/debug.log
sudo nano /var/www/html/wp-config.php
git add .
cd /var/www/html
ls
cd wp-content/
ls
cd plugins
ls
cd Password\ Generator/
ls
nano test.txt 
ls
