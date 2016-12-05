# Simple starter for eZ Platform projects

Some basic examples of how to use core functionalities like QueryTypes and other basic functions for <a href="https://ezplatform.com/">eZ Platform</a> projects with relatively good practises...

## Features

 - Integration example with a ready theme (https://github.com/BlackrockDigital/startbootstrap)
 - Example use of <a href="https://www.symfony.fi/entry/introducing-the-kaliop-migrations-bundle-for-ez-platform-and-ez-publish">Kaliop Migrations Bundle</a>
 - Simple blog functionality (blog + blog post)
 - Menu created with query types and a controller
 - eZ Studio layout management

And possibly something random....

## Install

 - Install eZ Platform see [INSTALL.md](https://github.com/ezsystems/ezstudio/blob/master/INSTALL.md)
 - If you don't want to bother setting up Nginx / Apache, then the PHP built in server is fine for testing:
 
 ```
 php app/console server:run
 ```

 - Run migrations:
 
 ```
 php app/console kaliop:migration:migrate
 ```
 
## License and support

http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2

There is no support or guarantees of any sorts.
