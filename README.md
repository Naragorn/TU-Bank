Installation
==================================
1. Put folder TU-Bank into your /var/www/ Apache Webapp folder
2. Make sure you have PHP5 (TU Bank currently doesnt support PHP > 5):

    sudo add-apt-repository ppa:ondrej/php

    sudo apt-get update

    sudo apt-get install php5.6 php5.6-mysql php-gettext php5.6mbstring php-xdebug libapache2-mod-php5.6

3. If you have multiple PHP versions activate only PHP5:

    # Apache
    sudo a2dismod php7.0    # (7.0 or your current PHP version)
    sudo a2enmod php5.6
    sudo service apache2 restart

    # CLI
    sudo ln -sfn /usr/bin/php5.6 /etc/alternatives/php 
4. Edit config.php for your database logins etc
5. edit setup/setup.php to your preferences (the users that get created, emails...))
6. exec setup/setup.php that installs the actual database data
7. if you want email functionaility edit functions.php and enter your email smtp login
8. Install pdflib via  http://www.pdflib.com/download/pdflib-family/pdflib-lite-7/  then:
    - extract it
    - ./configure
     make
     sudo make install
     
    sudo apt-get install php-pear   
    sudo pecl install pdflib 
    
    Source: http://php.net/manual/de/pdf.installation.php
9. Permission of the uploads folder must be set, such that everbody can write into it.
