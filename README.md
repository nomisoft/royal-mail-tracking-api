# Royal Mail Tracking API

To be able to track items through the Royal Mail API you'll need to be a Royal Mail account holder and have approved access to the Tracking API through the Royal Mail developer portal at https://developer.royalmail.net/

## Installation

```
composer require nomisoft/royal-mail-tracking-api
```

## Usage

You'll need the tracking reference number of the item you wish to retrieve details of as well as your Client ID, Client Secret and App ID (all available from the Royal Mail developer portal)

### Single item summary

```php
use \Nomisoft\RoyalMailTrackingApi\RoyalMail;
use \Nomisoft\RoyalMailTrackingApi\RoyalMailException;

$rm = RoyalMail::init('CLIENT_ID', 'CLIENT_SECRET', 'APP_ID');
try {
    $response = $rm->getSingleItemSummary('XX12345678XX');
}catch (SoapFault $e) {
	echo $e->getMessage();
} catch(RoyalMailException $e) {
	echo $e->getMessage() . ' - ' . $e->getCause();
}
```

The response returned is an array containing a DateTime object, the status and summary text

```php
Array
(
    [datetime] => DateTime Object
        (
            [date] => 2016-06-01 12:30:00
            [timezone_type] => 3
            [timezone] => Europe/London
        )
    [status] => Delivered
    [summary] => Item XX12345678XX was collected and signed for by the addressee on the 2016-06-01 from Kidderminster DO.
)
```


### Single item full history

```php
use \Nomisoft\RoyalMailTrackingApi\RoyalMail;
$rm = RoyalMail::init('CLIENT_ID', 'CLIENT_SECRET', 'APP_ID');
$response = $rm->getSingleItemHistory('XX12345678XX');
```

The response returned is a multidimensional array. Each delivery status update contains a DateTime object, the location and the status

```php
Array
(
    [0] => Array
        (
            [datetime] => DateTime Object
                (
                    [date] => 2016-06-01 12:30:00
                    [timezone_type] => 3
                    [timezone] => Europe/London
                )
            [location] => Kidderminster DO
            [status] => Delivered
        )

    [1] => Array
        (
            [datetime] => DateTime Object
                (
                    [date] => 2016-05-31 15:30:00
                    [timezone_type] => 3
                    [timezone] => Europe/London
                )
            [location] => Kidderminster DO
            [status] => We have your item
        )

    [2] => Array
        (
            [datetime] => DateTime Object
                (
                    [date] => 2016-05-31 12:30:00
                    [timezone_type] => 3
                    [timezone] => Europe/London
                )
            [location] => Kidderminster DO
            [status] => Delivery attempted
        )

    [3] => Array
        (
            [datetime] => DateTime Object
                (
                    [date] => 2016-05-28 10:00:00
                    [timezone_type] => 3
                    [timezone] => Europe/London
                )
            [location] => Medway Mail Centre
            [status] => It's on its way.
        )

)
```

### Multiple item summaries

```php
use \Nomisoft\RoyalMailTrackingApi\RoyalMail;
$rm = RoyalMail::init('CLIENT_ID', 'CLIENT_SECRET', 'APP_ID');
$response = $rm->getMultiItemSummary(array('XX12345678XX','ZZ12345678ZZ'));
```

The response returned is a multidimensional array with the tracking reference number as the key. Each item contains a DateTime object, status and the summary

```php
Array
(
    [XX12345678XX] => Array
        (
            [datetime] => DateTime Object
                (
                    [date] => 2016-06-01 12:30:00
                    [timezone_type] => 3
                    [timezone] => Europe/London
                )
            [status] => Delivered
            [summary] => Item XX12345678XX was collected and signed for by the addressee on the 2016-06-01 from Kidderminster DO.
        )

    [ZZ12345678ZZ] => Array
        (
            [datetime] => DateTime Object
                (
                    [date] => 2016-06-01 12:30:00
                    [timezone_type] => 3
                    [timezone] => Europe/London
                )
            [status] => Delivered
            [summary] => We have a record of item ZZ12345678ZZ as being delivered from Fort William DO on 2016-06-01.
        )
)
```

### Proof of delivery

```php
use \Nomisoft\RoyalMailTrackingApi\RoyalMail;
$rm = RoyalMail::init('CLIENT_ID', 'CLIENT_SECRET', 'APP_ID');
$response = $rm->getProofOfDelivery('XX12345678XX');
```

The response returned is an array containing a DateTime object and the name of the person that signed for the parcel

```php
Array
(
    [datetime] => DateTime Object
        (
            [date] => 2016-06-01 12:30:00
            [timezone_type] => 3
            [timezone] => Europe/London
        )
    [name] => Smith
)
```

### Errors

A RoyalMailException Exception is thrown if an error response is received from the API. Calling the getMessage() function will return Royal Mail's error description. There's also an additional getCause() function which will return the value of the 'Cause' field returned by the API

```php
try {
    $response = $rm->getSingleItemSummary('XX12345678XX');
} catch(RoyalMailException $e) {
	echo $e->getMessage();
	echo $e->getCause();
}
```

If the item was delivered over 30 days ago you might see the following printed from the above sample code

```
Tracking data are not available for barcode reference XX12345678XX
It is not possible to provide information about events that occurred more than 30 days ago
```