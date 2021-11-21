<?php namespace libs;
use \Firebase\JWT\JWT;

class Auth
{
    private static $secret_key = 'DÑFVJdkpfs32rlsdkf34Ç';
    private static $encrypt = ['HS256']; 

    public static function SignIn(array $data, int $minutes) : string
    {
        $time = self::exp($minutes);

        $token = array(
            'exp' => $time,
            'aud' => self::Aud(),
            'data' => $data
        );

        return JWT::encode($token, self::$secret_key);
    }

    public static function exp(int $min = 60) : int{
        return time() + (60 * $min); 
    }

    public static function Check($token)
    {
        if(empty($token))
        {
            throw new \Exception("Invalid token supplied.");
        }

        $decode = JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        );

        if($decode->aud !== self::Aud())
        {
            throw new \Exception("Invalid user logged in.");
        }
    }

    public static function GetData($token)
    {
        return JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}