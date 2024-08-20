<?php

namespace Services\PaymentGateway;

use Omnipay\Omnipay;

class Paystack
{
    private $gateway;

    CONST GATEWAY_NAME = 'Paystack';
    /**
     * @var array
     */
    private $transaction_data;

    public function __construct($gateway)
    {
        $this->gateway = $gateway;

    }

    public function storeAdditionalData()
    {
        return false;

    }

    private function createTransactionData($order_total, $order_email, $event)
    {

        $returnUrl = route('showEventCheckoutPaymentReturn', [
            'event_id' => $event->id,
            'is_payment_successful' => 1,
        ]);

        $this->transaction_data = [
            'amount' => $order_total,
            'currency' => "NGN",
            'description' => 'Order for customer: ' . $order_email,
            'email' => $order_email,
            'returnUrl' => $returnUrl,
        ];


        return $this->transaction_data;
    }

    public function startTransaction($order_total, $order_email, $event): \Omnipay\Common\Message\ResponseInterface
    {
        $this->createTransactionData($order_total, $order_email, $event);
        $request =  $this->gateway->purchase($this->transaction_data);
        $response = $request->send();
        return $response;
    }

    public function extractRequestParameters($request) {}



    public function getTransactionData() {
        return $this->transaction_data;
    }
    public function completeTransaction($transactionReference)
    {
        // Create a complete purchase request
        $response = $this->gateway->completePurchase([
            'reference' => $transactionReference,
        ])->send();

        // Return the response
        return $response;
    }
}
