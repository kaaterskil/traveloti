# Traveloti Application
=====================

## Introduction
------------
The Traveloti application uses a conventional XAMP infrastructure, where 'X' denotes 
the operating system (e.g. Linux or Windows); 'A' denotes an Apache web server; 'M'
denotes a MySQL relational database server; and 'P' denotes the PHP scripting language.
All systems should be latest version, and PHP should be at least version 5.4.8 to
permit closures, namespaces, annotations and the like.

You must ensure that your web server is configured to handle URL rewriting. Traveloti
uses basic .htaccess files, although it can be reconfigured to respond to a 
Windows-based IIS web server. In addition, you should familiarize  yourself with the 
Zend ZF2 system, particularly in setting up a skeleton application and Apache 
virtual host.

You must configure your own php.ini, httpd.conf and .htaccess files.
Kaaterskil Management, LLC takes no responsibility for configuration issues.


## Chat Server
-----------
Like all Instant Messaging (IM) systems, Traveloti's chat functions require a 
separate chat server. Traveloti is configured to use Openfire, a standard 
ejabber-based chat server. However, other chat servers can be used. If an
alternate chat server is used, or if Traveloti's chat functions are to be disabled,
you must write a custom initialization file (with the following path: 
module/Application/src/Application/Service/), and change the configuration in
module/Application/Module.php to point to it.

Chat servers require their own open ports to handle incoming requests. If Traveloti 
is installed in a shared hosting environment, you must ensure that your host will 
enable the server and provide the necessary open ports. Because of potential security 
threats associated with these open ports, many hosts will not enable chat services.


## Installation
------------
Other than the XAMP installation needed to create and run the web and chat servers, 
all required files and applications are included with the exception of Zend Framework 
(ZZF2) and Doctrine.

To install Traveloti, simply clone the entire directory to your web root and configure
Apache (or IIS) to point to the /public directory. The main index.php page resides 
there and all URL rewriting must point to it. You will also need to modify the 
configuration files in the /config directory to access the database and social networks.


## The Domain Layer
----------------
Traveloti uses latest best practices in object oriented programming (OOP). It strives
for loosely-coupled objects using dependency injection over composition.

The Traveloti application employs a number of common open-source, third party PHP 
sub-applications, the most significant being the framework that handles incoming
URL requests and outgoing responses. This is the Zend Framework version 2, or ZF2, 
released in the summer 0f 2012. Zend created the PHP language and is arguably the 
ultimate source for all things PHP. ZF2 is the latest incarnation of Zend's MVC 
framework.

ZF2 is a robust module-based MVC framework and is significantly faster and lighter 
weight than its previous version. Like most third party open source products, it 
requires significant configuration. However, its directory structure is extremely 
straightforward and all global configuration files reside in one directory. Module 
specific configuration files reside in their own module, thus creating a loosely 
coupled system allowing for continual upgrades and modifications without breaking.

Note that PHP is a scripting language which requires that the entire environment
be recreated on each page request. ZF2 is an event-driven framework system that
bootstraps the creation process in a modular fashion. Traveloti utilizes hooks
built into the ZF2 bootstrap process to customize event handling. This includes the 
login process, particularly with social networks. You should familiarize yourself
with all configuration and Module.php files and their integration so as to have an
overview of how Traveloti bootstraps.


## The Presentation Layer
----------------------
Traveloti uses the standard Zend ZF2 presentation logic of embedded PHP code within
standard HTML. All presentation layer files have a .phtml extension.


## The Database Layer
------------------
Traveloti utilizes Doctrine version 2 as its lowest data source layer ORM. Doctrine 
is the PHP analog to Java's Hibernate ORM system and behaves in a very similar way.
You should familiarize yourself with Hibernate and Doctrine, as Traveloti is
dependent on various inheritance systems in constructing its core objects. 


## File Structure
--------------
To install and run Traveloti, you must familiarize yourself with ZF2 and its required
directory structure. The Traveloti basic structure is as follows:

/config             Global configurations, including RDBMS and social network access
/data               Database-specific files, including the Traveloti MySQL schema 
/module
    /Application    Application-level and public page MVC objects 
    /Base           Traveloti user-specific MVC objects
    /Config         Traveloti configuration-specific objects
/public             Publicly-accessible files (images, JavaScript, CSS, etc.)
/tests              Zend ZF2 specific test files
/vendor             All third-party files (e.g. Doctrine, ZF2, other PHP modules)


## Social Network Links
--------------------
Traveloti uses standard OAUTH1 and OATH2 social network login procedures as defined 
by the respective service (e.g. Facebook, Foursquare, etc.). These services typically 
require the creation of an 'app' within the service to negotiate between the service 
and Traveloti. Services assign access codes and secret keys to their 'app' for 
authentication purposes. Currently, all 'apps' are owned by Kaaterskil Management, LLC.
To change the authorizations, you must create a new 'app' for each service and
modify the configuration in Traveloti's /config directory. Refer to the developer 
instructions associated with each social network service.


