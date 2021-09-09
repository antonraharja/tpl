CHANGELOG
=========

Version 1.0.8
-------------

* fix bug over sanitize causing javascript issue

Version 1.0.7
-------------

* [security] remove unwanted characters from content before compile

Version 1.0.6
-------------

* [security] remove unwanted templates from final result

Version 1.0.5
-------------

* [security] allows only a PHP variable on dynamic variables

Version 1.0.4
-------------

* fix #1 loop data contains regex syntax chars displayed incorrectly

Version 1.0.3
-------------

* remove variable echo, dir_template and dir_cache
* add config array
* add setConfig() getConfig()
* add option to configure template file extension

Version 1.0.2
-------------

* Change tpl tags from { ... } to {{ ... }}
* Change dynamic vars to allow only ready variables for an echo
* update _compile()
* update namespace from Antonraharja to Playsms
* add setName() setVars() setIfs() setLoops() setInjects()
* add return $this on most public methods

Version 1.0.1
-------------

* Rename apply() to compile()
* add setTemplate() and setContent()
* add more examples

Version 1.0.0
-------------

* first commit
