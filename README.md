# Codeception Mailtrap Module

This package provides a Mailtrap module for Codeception. 

## Installation

You need to add the repository into your composer.json file

```bash
 "repositories": [
    {
     ....
    },
    {
      "type": "vcs",
      "url": "https://github.com/whatdafox/codeception-mailtrap"
    }
```
You can install this package through composer:
```bash
composer require whatdafox/codeception-mailtrap-module
```

## Usage
You can use this module as any other Codeception module, by adding 'Mailtrap' to the enabled modules in your Codeception suite configurations.
You must setup the configuration variables: `client_id` and `inbox`. 

Example of functional.suite.yml

```yml
class_name: FunctionalTester
modules:
    enabled: [Filesystem, FunctionalHelper, Laravel5, Db, Mailtrap]
    config:
        Laravel5:
            cleanup: true
        Mailtrap:
            client_id: ADD_YOUR_TOKEN_HERE
            inbox_id: ADD_YOUR_INBOX_NAME_HERE
 ```     

  You can find the token on the page https://mailtrap.io/public_api
  THe inbox name is on the home page.
  
  After that you can run a build for codeception to index your files properly and you're good to go.
