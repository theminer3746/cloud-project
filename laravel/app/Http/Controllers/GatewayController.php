<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use App\StorageGateway\Gateway;

class GatewayController extends Controller
{
    private $s3;
    private $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
        $this->s3 = AWS::createClient('s3');
    }

    public function showAllGatewaysPage()
    {
        return view('gateway.list');
    }

    public function showGatewayActivationPage()
    {
        return view('gateway.activation');
    }

    public function activateGateway(Request $req)
    {
        $req->validate([
            'activation_key' => 'required|regex:/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/|between:1,50',
            'gateway_name' => 'required|alpha_dash|regex:/^[ -\.0-\[\]-~]*[!-\.0-\[\]-~][ -\.0-\[\]-~]*$/|between:2,255',
        ]);

        return $this->gateway->activateGateway(
            $req->activation_key,
            $req->gateway_name,
            $req->session()->get('customer_id')
        );
    }
}
