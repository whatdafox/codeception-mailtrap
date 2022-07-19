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

## Setup

You can use this module as any other Codeception module, by adding 'Mailtrap' to the enabled modules in your Codeception suite configurations.

### Add Mailtrap to your list of modules

```yml
modules:
    enabled: [Filesystem, FunctionalHelper, Db, Mailtrap]
 ```  

### Setup the configuration variables

- The `client_id` token can be found on the page `https://mailtrap.io/public-api`
- The `inbox_id` can be found in the url when visiting the website: *https://mailtrap.io/inboxes/`12345`/messages*.

```yml
modules:
    enabled: [Filesystem, FunctionalHelper, Db, Mailtrap]
    config:
        Mailtrap:
            client_id: ADD_YOUR_TOKEN_HERE
            inbox_id: ADD_YOUR_INBOX_NAME_HERE
 ```     
 
### Update Codeception build
  
  ```bash
  codecept build
  ```

You're all set up!

## Usage Examples

### Waiting for mail to arrive

If your test depends on your application sending an email, you can instruct the test to wait for something to arrive in your configured Mailtrap inbox before proceeding. These methods all have an optional timeout that can be passed as an additional parameter.

```php

// wait for any email to arrive in your inbox...
$I->waitForEmail();

// ...or wait with an optional 10 second timeout (default is 5 seconds)
$I->waitForEmail(10);
```

You can also wait for emails that match certain conditions. 

```php
// wait for an email with the given subject
$I->waitForEmailWithSubject("Subscription Confirmation");

// wait for an email to arrive with the given string in the HTML part of the body
$I->waitForEmailWithTextInHtmlBody("Thanks for joining!");

// wait for an email to arrive with the given string in the text part of the body
$I->waitForEmailWithTextInTextBody("Thanks for joining!");
```

### Confirming email contents

To confirm specific parts of your email, you can instruct the test to fetch the _last email received_, and run equality assertions on the email details.

The parameters available for checking are those provided by the Mailtrap API [/api/v1/inboxes/inbox_id/messages](https://mailtrap.docs.apiary.io/#reference/message/apiv1inboxesinboxidmessagesid/get) action.

```php
// returns true if all these parameters are exact matches
$I->receiveEmail([
    'subject' => 'Great Savings On Hamburgers',
    'to_email' => 'k.bacon@example.com',
    'to_name' => 'Kevin Bacon',
    'from_email' => 'noreply@astro-burger.com',
    'from_name' => 'Kate From Astronomical Burgers',
]);
```

There are methods for checking the most common parameters of a message, which may make your test more legible. These methods will look for exact equivalency.

```php
// check last email was sent from the correct address
$I->receiveAnEmailFromEmail('noreply@astro-burger.com');

// check that the sender name is correct
$I->receiveAnEmailFromName('Kate From Astronomical Burgers');

// check email recipient email is correct
$I->receiveAnEmailToEmail('k.bacon@example.com');

// check email recipient name is correct
$I->receiveAnEmailToName('Kevin Bacon');

// check email has correct subject
$I->receiveAnEmailWithSubject('Great Savings On Hamburgers');

// will check to see that the email's text body matches the provided string
$I->receiveAnEmailWithTextBody("the email's complete text body");

// will check to see that the email's html body matches the provided string
$I->receiveAnEmailWithHtmlBody("<strong>the email's complete html body</strong>");
```

You can also check the last received email for partial matches. For example, looking for an occurrence of 'Great Savings' in 'Great Savings on Hamburgers'.

```php

// check the provided string is somewhere in the subject
$I->seeInEmailSubject('Great Savings');

// check the provided string is somewhere in the email's text body
$I->seeInEmailTextBody("subset of text body");

// check the provided string is somewhere in the email's text body
$I->seeInEmailHtmlBody("<strong>subset of html body</strong>");
```

### Checking Attachments

You can check if your has attachments, or a specific number of attachments. If you want to run further checks on the contents of the attachments you'll have to fetch the message, then interact with the Mailtrap API yourself, though.

```php

// check that there are three attachments on the last message
$I->seeAttachments(3);

// check that there is at least 1 attachment on the last message
$I->seeAnAttachment();

```

### Directly fetching emails

If you need to perform more complex tests, you can directly fetch received emails. Messages are returned as instances of `Codeception\Module\MailtrapMessage`.

```php

// returns the contents of your Mailtrap inbox
$lastMessages = $I->fetchMessages();

// return teh most recent message received
$lastMessage = $I->fetchLastMessage();

// return the five most recent messages received
$last5Messages = $I->fetchLastMessages(5);
```
