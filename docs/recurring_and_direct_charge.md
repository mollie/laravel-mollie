![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Mollie Recurring

Here you can see an example of how easy it is to use [Mollie recurring](https://docs.mollie.com/payments/recurring) payments.

## Create a customer

First of all you need to [create a new customer](https://docs.mollie.com/payments/recurring#payments-recurring-first-payment) (step 1), this is pretty straight forward

```php
$customer = Mollie::api()->customers()->create([
    'name'  => 'John Doe',
    'email' => 'john@doe.com',
]);
```

## Initial Payment

After creating the user, you can [start a payment](https://docs.mollie.com/payments/recurring#payments-recurring-first-payment) (step 3), it's important to set `sequenceType` to `first`, this will generate a mandate on Mollie's end that can be used to do direct charges. Without setting the `method` the payment screen of Mollie will display your methods that support recurring payments.

```php
$payment = Mollie::api()->payments()->create([
    'amount' => [
        'currency' => 'EUR',
        'value'    => '25.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    'customerId'   => $customer->id,
    'sequenceType' => 'first',
    'description'  => 'My Initial Payment',
    'redirectUrl'  => 'https://domain.com/return',
    'webhookUrl'   => route('webhooks.mollie'),
]);

// Redirect the user to Mollie's payment screen.
return redirect($payment->getCheckoutUrl(), 303);
```

## Direct Charge

After doing the initial payment, you may [charge the users card/account directly](https://docs.mollie.com/payments/recurring#payments-recurring-charging-on-demand). Make sure there's a valid mandate connected to the customer. In case there are multiple mandates at least one should have `status` set to `valid`. Checking mandates is easy:

```php
$mandates = Mollie::api()->mandates()->listFor($customer);
```

If any of the mandates is valid, charging the user is a piece of cake. Make sure `sequenceType` is set to `recurring`.


```php
 $payment = Mollie::api()->payments()->create([
    'amount' => [
        'currency' => 'EUR',
        'value'    => '25.00', // You must send the correct number of decimals, thus we enforce the use of strings
    ],
    'customerId'   => $customer->id,
    'sequenceType' => 'recurring',
    'description'  => 'Direct Charge',
    'webhookUrl'   => route('webhooks.mollie'),
]);
```

Like any other payment, Mollie will call your webhook to register the payment status so don't forget to save the transaction id to your database.
