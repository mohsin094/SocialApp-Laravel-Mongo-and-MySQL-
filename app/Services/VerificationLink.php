<?php
namespace App\Services;

class VerificationLink
{
    protected $conn;
    public function __construct($name, $email){
        $details = [
            'name' =>$name,
            'Link'=>url('user/verifyemail/'.$email)
        ];
       \Mail::to($email)->send(new \App\Mail\MyTestMail($details));
    }
}
