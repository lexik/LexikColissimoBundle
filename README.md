LexikColissimoBundle
====================

This bundle provides services to access and consume the WSColiPosteLetterService 
using the Lexik [WSColissimo](https://github.com/lexik/ws-colissimo) library.

# WSColiPosteLetterService

Use the WSColiPosteLetterService to generate shipping labels for Colissimo Parcels.

## Installation

### Download using composer

Add the bundle to your `composer.json` :

```
{
    "require": {
        "lexik/colissimo-bundle": "dev-master"
    }
}
```
Download it by running the command :

```
php composer.phar update lexik/colissimo-bundle
```

### Enable the bundle

Enable the bundle in your `app/AppKernel.php` :

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Lexik\Bundle\ColissimoBundle\LexikColissimoBundle(),
    );
}
```

## Configuration

Below is a minimal example of the configuration necessary to use the LexikColissimoBundle
in your application:

```yml
lexik_colissimo:
    ws_coliposte_letter_service:
        contract_number:         123456               # mandatory
        password:                'abcdef'             # mandatory
        service_call_context:
            commercial_name:     'ACME COMPANY'
        sender:                                       # you can overwrite this part from your code
            company_name:        'ACME COMPANY'
            line_0:              null   
            line_1:              null
            line_2:              'Place de la Comedie'
            line_3:              null
            postal_code:         '34000'
            city:                'Montpellier'
```

## Usage

Get the service from the container, call the `getLabel` method with your parcel and recipient informations as arguments :

```php
// use Lexik\Bundle\ColissimoBundle\Exception\InvalidRequestException;
// use Lexik\Bundle\ColissimoBundle\Exception\FailedRequestException;

$colissimo = $this->container->get('lexik_colissimo.ws_coliposte_letter_service.service');

try {

    $response = $colissimo->getLabel(
        array('weight' => 1.780),
        array(
            'name'       => 'Client Name',
            'surname'    => 'Client Surname',
            'email'      => 'client@email.com',
            'line2'      => 'Client Address',
            'city'       => 'Client City',
            'postalCode' => 'Client Postal Code'
        )
	// you can overwrite the sender configured in you app/config.yml by passing an array as 3rd argument
	// you can disable validation by setting the 4th argument to false
    );
    
    // do something with the response

} catch (InvalidRequestException $e) {
    // validation problems : you can iterate over them with $e->getViolations()
} catch (FailedRequestException $e) {
    // webservice responded with an error : you can the error message with $e->getMessage()
}
```
