Test our Exercise
==================================
1. Open localhost/ex1/securecoding in Chrome

Setup if no VM used:
===================================
- Make database securecoding
- Make config.php
- Run setup.php
- Install sendmail via: sudo apt-get install  sendmail
- Install pdflib via  http://www.pdflib.com/download/pdflib-family/pdflib-lite-7/  then:
    - extract it
    - ./configure
     make
     sudo make install
     
    sudo apt-get install php-pear   
    sudo pecl install pdflib 
    
    Source: http://php.net/manual/de/pdf.installation.php
- Permission of the uploads folder must be set, such that everbody can write into it.