# README

## License

This piece of software is released under the terms of the following license:

The MIT License (MIT)

Copyright (c) 2013-2020 XQueue GmbH

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

## Package contents

Upon unpacking the distributed .zip file, you will see the following elements in the extracted folder:

	client/                   - the client code
	doc/                      - the API documentation
	test/                     - the testing app and the phphunit tests
	CHANGELOG                 - The changelog file
	LICENSE                   - a copy of the MIT license this software is released under
	README.md                 - this file
	build.properties          - the build configuration
	build.xml                 - the ant build file
	phpunit-configuration.xml - the phpunit configuration file

## Accessing the documentation

Point your browser to `doc/index.html`.

## Installing the tester application

You will need a web server with a working PHP installation. If you are developing on Windows, [XAMPP](https://www.apachefriends.org) is an easy way to provide such an environment.

Next, open `build.properties` in your favorite text editor. Adjust the build settings so that `dir.deploy` points somewhere into your webservers document directory (ignore the phpunit setting for now):

	# point this to where you want to deploy the api tester
	dir.deploy=C:\\xampp\\htdocs\\maileon-api-tester
	
	# your phpunit executable
	phpunit=phpunit.bat

Now enter your Maileon API key inside the file `test/conf/config.include`.

For the next step, you will need a working installation of [Apache Ant](http://ant.apache.org/). Make sure to put the directory that contains the `ant` executable on your system's `PATH`.

Once ant is installed, run it inside the directory where you unpacked the API client:

	C:\projects\maileon-php-client-1.6.2>ant
	Buildfile: C:\projects\eagle_kunde\php-api-client\target\maileon-php-client-0.10.3\build.xml
	
	deploy:
	     [echo] [deploying project to C:\xampp\htdocs\maileon-api-tester]
	
	BUILD SUCCESSFUL
	Total time: 0 seconds

Now, you should be able to access the tester app using your browser. Given the example configuration above, just point your Browser to [http://localhost/maileon-api-tester/](http://localhost/maileon-api-tester/).

To verify that your installation works, check all the checkboxes in the "Ping - Tests" section and click the button "Run tests" on the bottom of the page. In order to perform the other tests, you will have to look at the source code and adjust the input data to match the data that is available inside your account.


## Installing the API client into your PHP application

The client is distributed using [Packagist](https://packagist.org/packages/xqueue/maileon-api-client) and thus, can easily be included using composer.

A sample composer file would be

    {
        "name" : "myvendor/myapplication",
        "description" : "Some sample application",
        "version" : "1.0.0",
        "keywords" : [
            "XQueue",
            "Maileon"
	],
        "homepage" : "https://www.maileon.de",
        "type" : "library",
        "license" : "MIT",
        "authors" : [{
            "name" : "XQueue GmbH, Max Mustermann",
            "email" : "max.mustermann@xqueue.com"
        }],
        "require" : {
            "xqueue/maileon-api-client/" : "@dev"
        },
        "require-dev" : {
            "phpunit/phpunit" : "^4"
        }
    }

The client can then be included by adding the autoloader.

	// Include the Maileon API Client classloader 
	require "vendor/autoload.php";
	

## Running the PHPUnit tests

Download [PHPUnit](http://phpunit.de/) and put its executable on your `PATH`. If you're on a system other than Windows, adjust the name of the `phpunit` executable inside `build.properties`. Make sure that your Maileon API key is entered correctly inside the file `test/conf/config.include`.

Now run `ant test` inside the directory where you unzipped the distributable.

## Connecting to the API using HTTPS

Since it is likely that you will be transmitting sensible customer data over the API, you should use the SSL-enabled API endpoint `https://api.maileon.com/1.0` instead of the plain HTTP version. In order to do so, you will have to install a root certificate bundle for cURL by following these steps:

1. Download the bundle from http://curl.haxx.se/ca/cacert.pem .
2. Copy the bundle to a directory that can be accessed by PHP.
3. Add the following entry to your php.ini (remember to change the path to where you put the cert bundle):

	curl.cainfo="C:\xampp\php\cacert.pem"

You should now be able to connect to the SSL-enabled API endpoint.
