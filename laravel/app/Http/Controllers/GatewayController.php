<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use App\User;
use App\StorageGateway\Gateway;
use App\StorageGateway\RealGateway;

class GatewayController extends Controller
{
    private $s3;
    private $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
        $this->s3 = AWS::createClient('s3');
    }

    public function showAllGatewaysPage(Request $req, User $user, RealGateway $realGateway)
    {
        $gatewayArray = $user->find($req->session()->get('auth.user_id'))->gateways;

        $gateways = [];

        foreach($gatewayArray as $gateway){
            $gateways[] = [
                'name' => $gateway->name,
                'path' => $realGateway->getSMBAccessUrl($gateway->arn),
            ];
        }

        return view('gateway.list')->with('gateways', $gateways);
    }

    public function showGatewayActivationPage()
    {
        return view('gateway.activation');
    }

    public function activateGateway(Request $req)
    {
        $req->validate([
            'activation_key' => 'required|regex:/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/|between:1,50',
            'gateway_name' => 'required',
            'smb_password' => 'required|between:6,50',
        ]);

        $this->gateway->setRealGateway(new \App\StorageGateway\RealGateway(new \App\S3));

        $this->gateway->activateGateway(
            $req->activation_key,
            $req->gateway_name,
            $req->session()->get('auth.user_id'),
            $req->smb_password,
        );

        return redirect('/');
    }
}
