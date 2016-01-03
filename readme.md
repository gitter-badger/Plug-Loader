#About

[![Join the chat at https://gitter.im/Karabow-Inc/Plug-Loader](https://badges.gitter.im/Karabow-Inc/Plug-Loader.svg)](https://gitter.im/Karabow-Inc/Plug-Loader?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
 > A PSR4 Implementation of an autoloader for the ~Plug Microframework.

#How-TO
> The configuration file for this autoloader can be __supplied in two different formats__:
> It can be provided as a json file or as an xml file.

The Autoloader first checks for a json file before xml since thats the default config format.
You must create an xml autoload configuration file in the document root in order to use xml for the config format.
You can rename the autoload.xml.template file to autoload.xml after the autoload.json file has been deleted or renamed to
test this.

