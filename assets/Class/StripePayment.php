<?php

namespace App;

use Stripe\Stripe;

class StripePayment
{
    private $clientSecret;

    public function __construct($clientSecret)
    {
        $this->clientSecret = $clientSecret;
        Stripe::setApiKey($this->clientSecret);
        Stripe::setApiVersion('2020-08-27');
    }

    public function createCheckoutSession($cart, $domain)
    {
        try {
            $line_items = [];

            foreach ($cart as $item) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item['name'],
                        ],
                        'unit_amount' => $item['price'] * 100, // Convertir en centimes
                    ],
                    'quantity' => 1,
                ];
            }

            if (empty($line_items)) {
                throw new \Exception('Le panier est vide.');
            }

            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => $domain . '/success.html',
                'cancel_url' => $domain . '/cancel.html',
            ]);

            return $checkout_session->url;
        } catch (\Exception $e) {
            throw new \Exception('Erreur: ' . $e->getMessage());
        }
    }
}
?>
