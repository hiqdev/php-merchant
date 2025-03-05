<?php

namespace hiqdev\php\merchant\merchants\stripe;

use DateTime;
use hiqdev\php\merchant\exceptions\InsufficientFundsException;
use hiqdev\php\merchant\exceptions\MerchantException;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\Site\Merchant\Service\Exception\MeaningfulForUserMerchantException;
use Money\Currency;
use Money\Money;
use Omnipay\Stripe\Message\PaymentIntents\Response;
use Omnipay\Stripe\PaymentIntentsGateway;
use Stripe\Webhook;
use UnexpectedValueException;

class ConfirmationStrategy
{
    public function __construct(
        private PaymentIntentsGateway $gateway,
        private string $webhookSecret,
    )
    {
    }

    /**
     *
     * @param array $data
     * @return CompletePurchaseResponse
     * @throws MeaningfulForUserMerchantException
     * @throws MerchantException
     * @throws InsufficientFundsException
     */
    public function confirm(array $data): CompletePurchaseResponse
    {
        if (isset($data['payment_intent']) && ($response = $this->confirmByPaymentIntent($data['payment_intent']))) {
            return $this->createPurchaseResponse($response);
        }

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $this->webhookSecret
            );
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        $pi = $paymentIntent->id;
        $request = $this->confirmByPaymentIntent($pi);
        if ($request) {
            return $this->createPurchaseResponse($request);
        }

        throw new MerchantException("Skip");
    }

    private function confirmByPaymentIntent(mixed $paymentIntent): Response
    {
        $response = $this->gateway->fetchPaymentIntent(['paymentIntentReference' => $paymentIntent])->send();
        if ($response->getData()['confirmation_method'] === 'manual' && $response->getData()['status'] === 'requires_confirmation') {
            $response = $this->gateway->confirm([
                'paymentIntentReference' => $paymentIntent,
            ])->send();
        }

        return $response;
    }

    private function createPurchaseResponse(Response $response): CompletePurchaseResponse
    {
        if ($response->isSuccessful()) {
            return (new CompletePurchaseResponse())
                ->setIsSuccessful(true)
                ->setAmount(new Money($response->getData()['amount'], new Currency(strtoupper($response->getData()['currency']))))
                ->setFee(new Money(0, new Currency(strtoupper($response->getData()['currency']))))
                ->setTransactionReference($response->getTransactionReference())
                ->setTransactionId($response->getTransactionId())
                ->setPayer($response->getData()['customer'] ?? '')
                ->setTime(new DateTime());
        }

        if (
            isset($response->getData()['last_payment_error'])
            && isset($response->getData()['last_payment_error']['code'])
            && isset($response->getData()['last_payment_error']['decline_code'])
            && $response->getData()['last_payment_error']['code'] === 'card_declined'
            && $response->getData()['last_payment_error']['decline_code'] === 'insufficient_funds'
        ) {
            $message = $response->getData()['last_payment_error']['message'] ?? 'Insufficient funds';
            throw (new InsufficientFundsException($message))->withContextData($response->getData());
        }

        if (isset($response->getData()['error']['message']) || isset($response->getData()['last_payment_error']['message'])) {
            $message = $response->getData()['error']['message'] ?? $response->getData()['last_payment_error']['message'];
            throw new MeaningfulForUserMerchantException("Failed to charge card:\n" . $message);
        }

        throw new MerchantException('Failed to charge card');
    }
}
