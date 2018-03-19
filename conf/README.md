# Conf

This is folder holds custom configuation files needed for various machines i am using. 
I will do my best in describing what each conf file does and whether or not they will
work for you how they have helped me.

## nginx
this hold nginx configuation file(s)
- nginx.conf.osx.homebrew
	- this has some changes. First was to enable php to execute through fast-cgi
	defualt config did not allow php and fastcgi_param  SCRIPT_FILENAME was not set correctly
