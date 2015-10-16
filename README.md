[![Latest Stable Version](https://img.shields.io/packagist/v/whatdafox/codeception-mailtrap.svg)](https://packagist.org/packages/whatdafox/codeception-mailtrap) 
[![Total Downloads](https://img.shields.io/packagist/dt/whatdafox/codeception-mailtrap.svg)](https://packagist.org/packages/whatdafox/codeception-mailtrap) 
[![Latest Unstable Version](https://poser.pugx.org/whatdafox/codeception-mailtrap/v/unstable.svg)](https://img.shields.io/packagist/vpre/whatdafox/codeception-mailtrap.svg) 
[![License](https://img.shields.io/packagist/l/whatdafox/codeception-mailtrap.svg)](https://packagist.org/packages/whatdafox/codeception-mailtrap)
[![Get help on Codementor](https://cdn.codementor.io/badges/get_help_github.svg)](https://www.codementor.io/foxted)

# Codeception Mailtrap Module

This package provides a Mailtrap module for Codeception. 

## Installation

You need to add the repository into your composer.json file

```bash
    composer require --dev whatdafox/codeception-mailtrap
```

## Usage

You can use this module as any other Codeception module, by adding 'Mailtrap' to the enabled modules in your Codeception suite configurations.

*You must setup the configuration variables: `client_id` and `inbox_id`.*

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
  The `inbox_id` can be found in the url when visiting the website: *https://mailtrap.io/inboxes/`12345`/messages*.
  
  After that you can run a build for Codeception to index your files properly and you're good to go.
