<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCreditRequest;
use App\Http\Requests\StoreCreditRequest;
use App\Http\Requests\UpdateCreditRequest;
use App\Models\Credit;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class CreditsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('credit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $credits = Credit::where('email', Auth::user()->email)->get();

        return view('frontend.credits.index', compact('credits'));
    }

    public function buy(Request $request){

        $payments = new Payment;
        $amount = 30;
        if($request->plan_type == 'free'){
            $amount =env('BASIC_CREDIT');
        }elseif($request->plan_type == 'standard'){
            $amount =env('STANDARD_CREDIT');
        }elseif($request->plan_type == 'premium'){
            $amount =env('PREMIUM_CREDIT');
        }

        $payments->email = Auth::user()->email;
        $payments->stripe_transaction = $request->transaction_id;
        $payments->amount = $amount;
        $payments->save();

        //if email does not exist in credit table create a new record with 3 credits
        $credits = Credit::where('email', Auth::user()->email)->first();

        if($credits == null){
            $credit = new Credit();
            $credit->user_id = Auth::user()->email;
            $credit->points = 3;
            $credit->save();
        }

        if($request->plan_type == 'free'){
            $credits->points = $credits->points + ($amount/10);
        }elseif($request->plan_type == 'standard'){
            $credits->points = $credits->points  + ($amount/10);
        }elseif($request->plan_type == 'premium'){
            $credits->points = $credits->points  + ($amount/10);
        }
        $credits->save();

        return redirect()->route('frontend.credits.index');
    }

    public function create()
    {
        abort_if(Gate::denies('credit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('frontend.credits.create');
    }

    public function store(StoreCreditRequest $request)
    {
        $credit = Credit::create($request->all());

        return redirect()->route('frontend.credits.index');
    }


    public function update(UpdateCreditRequest $request, Credit $credit)
    {
        $credit->update($request->all());

        return redirect()->route('frontend.credits.index');
    }

 
    public function CreditBalance(){
        $credit = Credit::where('user_id', Auth::id())->sum('points');
        return $credit;
    }
}
