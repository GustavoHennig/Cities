#########################################
# PHPLiveX Library			#
# (C) Copyright 2006 Arda Beyazoğlu	#
# Version: 2.4.1			#
# Home Page: www.phplivex.com		#
# Contact: ardabeyazoglu@gmail.com	#
# Release Date: 25.09.2007		#
# License: LGPL				#
#########################################

PHPLiveX 2.4.1
------------

 This version has been released because of some important errors found in version 2.4:

    * The code is made XHTML compliant.
    * Fixed bug occured when passing arguments including some special characters to js functions.
    * By using the argument added to "run" method, only javascript functions created can be printed to page optionally. When you are using nested ajax requests, it will be useful not to call the same phplivex methods again.
    * "ExternalJS" can not take boolean values anymore. Instead, a file path is passed. So, the codes are printed to that file and it is included.
    * With an optimization over php codes, the performance is increased.

### Please inform me (ardabeyazoglu@gmail.com) if you catch a bug or request a new feature.