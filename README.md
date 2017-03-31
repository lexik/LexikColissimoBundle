LexikColissimoBundle
====================

This bundle provides services to access and consume the WSColiPosteLetterService 
using the Lexik [WSColissimo](https://github.com/lexik/ws-colissimo) library.

![Project Status](http://stillmaintained.com/lexik/LexikColissimoBundle.png)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1249be2c-432e-452a-98ab-212b021af522/big.png)](https://insight.sensiolabs.com/projects/1249be2c-432e-452a-98ab-212b021af522)

This bundle is deprecated
=========================

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
    
    // $response looks like this
    object(WSColissimo\WSColiPosteLetterService\Response\ValueObject\ReturnLetter)[1102]
      protected 'file' => null
      protected 'parcelNumber' => string '13xc1v654d123' (length=13)
      protected 'PdfUrl' => string 'https://ws.colissimo.fr/path/to/pdf-file' (length=40)
      protected 'errorID' => int 0
      protected 'error' => string '' (length=0)
      protected 'signature' => null
      protected 'dateCreation' => null

} catch (InvalidRequestException $e) {
    // validation problems : you can iterate over errors with $e->getViolations()
} catch (FailedRequestException $e) {
    // webservice returned an error : you can get the error message with $e->getMessage()
}
```
