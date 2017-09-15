Apache configuration

NameVirtualHost *:80
NameVirtualHost *:443

<VirtualHost *:80>
    ServerName allenell.is
    SetEnv HOST allenell.is
    DocumentRoot "/volume1/web"
    <IfModule dir_module>
        DirectoryIndex  index.html index.htm index.cgi index.php index.php5
    </IfModule>
    <Directory "/volume1/web">
        Options MultiViews FollowSymLinks ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    <IfModule fastcgi_module>
        AddHandler default-handler .htm .html .shtml
        AddHandler php5-fastcgi .php .php3 .php4 .php5 .phtml
        AddType text/html .php .php3 .php4 .php5 .phtml
        Action php5-fastcgi /php-fpm-handler.fcgi
        ScriptAlias /php-fpm-handler.fcgi /php-fpm-handler
    </IfModule>

    <Proxy *>
        Order deny,allow
        Allow from all
    </Proxy>

        <Location /test>
                ProxyPass !
        </Location>
        <Location /clients>
                ProxyPass !
        </Location>

    ProxyRequests Off
    ProxyPreserveHost On
    ProxyPass / http://10.10.10.30:80/
    ProxyPassReverse / http://10.10.10.30:80/

#       <Location /clients>
#               ProxyPass !
#       </Location>

</VirtualHost>

<VirtualHost *:443>
    ServerName allenell.is
    SetEnv HOST allenell.is
    DocumentRoot "/volume1/web"
    <IfModule dir_module>
        DirectoryIndex  index.html index.htm index.cgi index.php index.php5
    </IfModule>
    <Directory "/volume1/web">
        Options MultiViews FollowSymLinks ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    <IfModule fastcgi_module>
        AddHandler default-handler .htm .html .shtml
        AddHandler php5-fastcgi .php .php3 .php4 .php5 .phtml
        AddType text/html .php .php3 .php4 .php5 .phtml
        Action php5-fastcgi /php-fpm-handler.fcgi
        ScriptAlias /php-fpm-handler.fcgi /php-fpm-handler
    </IfModule>

    <Proxy *>
        Order deny,allow
        Allow from all
    </Proxy>

    SSLProxyEngine On
            SSLProxyCheckPeerCN on
                    SSLProxyCheckPeerExpire on

        <Location /php-fpm-handler.fcgi>
                ProxyPass !
        </Location>
        <Location /php-fpm-handler>
                ProxyPass !
        </Location>
        <Location /test>
                ProxyPass !
        </Location>
        <Location /clients>
                ProxyPass !
        </Location>

    ProxyRequests Off
    ProxyPreserveHost On
    ProxyPass / https://10.10.10.30:443/
    ProxyPassReverse / https://10.10.10.30:443/


</VirtualHost>

LoadModule ssl_module modules/mod_ssl.so
#LoadModule xsendfile_module modules/mod_xsendfile.so
XSendFile On
XSendFilePath "/volume1/"

