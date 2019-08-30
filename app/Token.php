<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    //
    const EXPIRATION_TIME = 15;
    protected $fillable = [

        'code',
        'user_id',
        'used'

    ];

    public function __construct(array $attribute = []){
    	if(! isset($attributes['code'])){
    		$attributes['code'] = $this->generateCode();
    	}

    	parent::__construct($attributes);
    }

    public function generateCode($codeLength = 4){

    	$min = pow(10, $codeLength);
    	$max = $min * 10 - 1;
    	$code = mt_rand($min, $max);

    	return $code;
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function isValid(){
    	return ! $this->isUsed() && ! $this->isExpired();
    }

    public function isUsed(){
    	return $this->used;
    }

    public function isExpired(){
    	return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }

    public function sendCode(){
    	if(!$this->user){
    		throw new \Exception("No user attached to this token.");
    	}

    	if(! $this->code){
    		$this->code = $this->generateCode();
    	}

    	try{
    		app('twilio')->messeges->create($this->user->getPhoneNumber(),
            ['from' => env('TWILIO_NUMBER'), 'body' => "Your verification code is {$this->code}"]);
    	} catch (\Exception $ex){
    		return false; // enable to send sms
    	}

    	return true;
    }
}
