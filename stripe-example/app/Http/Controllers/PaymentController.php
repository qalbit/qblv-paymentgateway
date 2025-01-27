<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class PaymentController extends Controller
{
    public function index() {
        try {
            $stripe_public_key = getenv('STRIPE_PUBLIC_KEY');
            $stripe_secret_key = getenv('STRIPE_SECRET_KEY');
            $stripe = XgPaymentGateway::stripe();
            $stripe->setSecretKey($stripe_secret_key);
            $stripe->setPublicKey($stripe_public_key);
            $stripe->setCurrency("USD");
            $stripe->setEnv(true); //env must set as boolean, string will not work
            $stripe->setExchangeRate(88.04); // if INR not set as currency

            $redirect_url = $stripe->charge_customer([
                'amount' => 10,
                'title' => "TEST",
                'description' => "KCHBNS KLJCSN KLS NCLKCSC NLKCSKL",
                'ipn_url' => route('ipn'),
                'order_id' => "123456JKHBSC45",
                'track' => \Str::random(36),
                'cancel_url' => "http://localhost:8000/",
                'success_url' => "http://localhost:8000/",
                'email' => "tester@mailinator.com",
                'name' => "tester",
                'payment_type' => 'order',
            ]);
            session()->put('order_id', "123456JKHBSC45");
            return $redirect_url;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function ipn() {
        $stripe_public_key = getenv('STRIPE_PUBLIC_KEY');
        $stripe_secret_key = getenv('STRIPE_SECRET_KEY');

        $stripe = XgPaymentGateway::stripe();
        $stripe->setSecretKey($stripe_secret_key);
        $stripe->setPublicKey($stripe_public_key);
        $stripe->setEnv(true);
        dd($stripe->ipn_response());
    }
}
