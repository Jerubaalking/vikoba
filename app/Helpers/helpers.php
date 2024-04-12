<?php

// use App\Models\Setting;
use App\Models\ShortUrl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberUtil;

if ( !function_exists( 'print_date' ) ) {

    function print_date( $date, $format = null, $emptyString = 'Unknown' )
 {

        $carbon = new Carbon();

        if ( $date && is_a( $date, '\Carbon\Carbon' ) ) {

            if ( $format ) {

                return $date->format( $format );
            }

            if ( $date->lt( $carbon->now()->yesterday() ) || $date->isFuture() ) {

                return $date->format( 'F j, Y' );

            }

            if ( $date->diffInMinutes() < 59 ) {

                return $date->diffInMinutes() < 1 ? 'Seconds ago' : $date->diffInMinutes() . ' minutes ago';

            }
            if ( $date->isToday() ) {

                $string = 'Today';

            } elseif ( $date->isYesterday() ) {

                $string = 'Yesterday';
            }

            return $string . ' at ' . $date->format( 'h:i A' );

        } else {

            return $emptyString;
        }
    }
}

if ( !function_exists( 'flash' ) ) {

    function flash( $message, $type = 'info', $options = array() )
 {

        Session::flash( 'alert', isset( $options[ 'alert' ] ) ? $options[ 'alert' ] : false );

        Session::flash( 'alert-message', $message );

        Session::flash( 'alert-level', $type );

        Session::flash( 'alert-options', $options );

        Session::flash( 'notify', true );

    }
}

/**
* @param $string
* @param array $delimiters
* @param array $exceptions
* @return string
*/

function titleCase( $string, $delimiters = array( ' ', '-', '.', "'", "O'", 'Mc', "'s'" ), $exceptions = array( 'wa', 'the', 'a', 'for', 'ya', 'kwa', 'cha', 'and', 'to', 'of', 'das', 'dos', 'is', 'or', 'I', 'II', 'III', 'IV', 'V', 'VI', 'la', 'za', 'na' ) )
 {
    /*
    * Exceptions in lower case are words you don't want converted
     * Exceptions all in upper case are any words you don't want converted to title case
    *   but should be converted to upper case, e.g.:
    *   king henry viii or king henry Viii should be King Henry VIII
    */
    $string = mb_convert_case( $string, MB_CASE_TITLE, 'UTF-8' );

    foreach ( $delimiters as $dlnr => $delimiter ) {

        $words = explode( $delimiter, $string );

        $newwords = array();

        foreach ( $words as $wordnr => $word ) {

            if ( in_array( mb_strtoupper( $word, 'UTF-8' ), $exceptions ) ) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtoupper( $word, 'UTF-8' );

            } elseif ( in_array( mb_strtolower( $word, 'UTF-8' ), $exceptions ) ) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtolower( $word, 'UTF-8' );

            } elseif ( !in_array( $word, $exceptions ) ) {
                // convert to uppercase ( non-utf8 only )
                $word = ucfirst( $word );

            }

            $newwords[] = $word;
        }

        $string = join( $delimiter, $newwords );

    }
    //foreach

    return $string;
}

if ( !function_exists( 'week_days' ) ) {

    function week_days()
 {
        $timestamp = strtotime( 'next Sunday' );
        $days = array();
        for ( $i = 0; $i < 7; $i++ ) {
            $days[] = strftime( '%A', $timestamp );
            $timestamp = strtotime( '+1 day', $timestamp );
        }
        return $days;
    }

}

if ( !function_exists( 'carbon' ) ) {

    function carbon( $format = null )
 {
        if ( $format ) {
            Carbon::setToStringFormat( $format );
        }
        return new Carbon();
    }
}

if ( !function_exists( 'agent' ) ) {

    function agent()
 {
        return new Jenssegers\Agent\Agent();
    }
}

/**
* Generate random
*
* @param int $length
* @param string $type
* @return string
*/

function rand_crypto( $length = 10, $type = 'nozero' )
 {
    switch ( $type ) {
        case 'alnum':
        $pool = 'ABCDEFGHklmnIJK123456789abcdefNOPQRSTghijopqrLMUVWXYZstuvwxyz';
        break;
        case 'alpha':
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        break;
        case 'hexdec':
        $pool = '0123456789abcdef';
        break;
        case 'numeric':
        $pool = '0123456789';
        break;
        case 'nozero':
        $pool = '123456789';
        break;
        case 'distinct':
        $pool = '123456789ACDEFHJKLMNPRSTUVWXYZ';
        break;
        default:
        $pool = ( string )$type;
        break;
    }

    $crypto_rand_secure = function ( $min, $max ) {
        $range = $max - $min;
        if ( $range < 0 )
        return $min;
        // not so random...
        $log = log( $range, 2 );
        $bytes = ( int )( $log / 8 ) + 1;
        // length in bytes
        $bits = ( int )$log + 1;
        // length in bits
        $filter = ( int )( 1 << $bits ) - 1;
        // set all lower bits to 1
        do {
            $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
            $rnd = $rnd & $filter;
            // discard irrelevant bits
        }
        while ( $rnd >= $range );

        return $min + $rnd;
    }
    ;

    $token = '';
    $max = strlen( $pool );
    for ( $i = 0; $i < $length; $i++ ) {
        $token .= $pool[ $crypto_rand_secure( 0, $max ) ];
    }

    return $token;
}

// if ( !function_exists( 'setting' ) ) {

//     /**
//     * @param $name
//     * @param null $col
//     * @return mixed
//     * @throws \Psr\SimpleCache\InvalidArgumentException
//     */

//     function setting( $name, $col = null )
//  {
//         return Setting::get( $name, $col );
//     }
// }

if ( !function_exists( 'short_url' ) ) {

    /**
    * @param $url
    * @return mixed
    * @throws \Psr\SimpleCache\InvalidArgumentException
    */

    function short_url( $url )
 {
        return ShortUrl::newUrl( $url );
    }
}

if ( !function_exists( 'get_url' ) ) {

    /**
    * @param $code
    * @return mixed
    */

    function get_url( $code )
 {
        return ShortUrl::url( $code );
    }
}

if ( !function_exists( 'get_seed' ) ) {

    function get_seed()
 {
        if ( request()->session()->has( 'session_rand' ) ) {
            if ( ( time() - request()->session()->get( 'session_rand' ) ) > 3600 ) {
                request()->session()->put( 'session_rand', time() );
            }
        } else {
            request()->session()->put( 'session_rand', time() );
        }
    }
}

if ( !function_exists( 'str_starts_with' ) ) {
    function str_starts_with( $haystack, $needle )
 {
        return ( string )$needle !== '' && strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
    }
}

if ( !function_exists( 'write' ) ) {

    function write( $text, array $data = null, $level = 'info' )
 {

        try {

            if ( in_array( $level, [ 'info', 'error', 'alert', 'warning' ], true ) ) {

                Log::write( $level, $text, !empty( $data ) ? array_merge( $data, [
                    'ip' => request()->ip(),
                    'url' => request()->fullUrl(),
                    'user' => request()->user() ? optional( request()->user() )->name . ' ( ' . optional( request()->user() )->id . ' )' : null,
                    'request' => request()->toArray(),
                    'session' => session()->all(),
                    'agent' => agent()->getUserAgent(),
                    'mobile' => agent()->isMobile(),
                    'phone' => agent()->isPhone(),
                    'desktop' => agent()->isDesktop(),
                    'browser' => agent()->browser(),
                    //  'locale' => get_user_locale(),
                ] ) : [
                    'ip' => request()->ip(),
                    'url' => request()->fullUrl(),
                    'user' => request()->user() ? optional( request()->user() )->name . ' ( ' . optional( request()->user() )->id . ' )' : null,
                    'request' => request()->toArray(),
                    'session' => session()->all(),
                    'agent' => agent()->getUserAgent(),
                    'mobile' => agent()->isMobile(),
                    'phone' => agent()->isPhone(),
                    'desktop' => agent()->isDesktop(),
                    'browser' => agent()->browser(),
                    //  'locale' => get_user_locale(),
                ] );
            } else {

                Log::error( 'Unknown Log ' . $level . ' level provided', [
                    'text' => $text,
                    'level' => $level,
                    'data' => $data,
                    'request' => request()->toArray(),
                    'session' => session()->all(),
                    'agent' => agent()->getUserAgent(),
                ] );

            }
        } catch ( Exception $exception ) {

            Log::error( 'Failed to log a message ' . $exception->getMessage() );
        }
    }

}

if ( !function_exists( 'get_international_number' ) ) {

    function get_international_number( $number, $country = 'TZ' ): string
 {

        info( 'Parsing phone '. $number );

        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phone = $phoneUtil->parse( $number, $country );

            info( 'Parsed phone number '. $number, [
                //  'instance' => $phone,
                'response' =>  $response = $phoneUtil->format( $phone, \libphonenumber\PhoneNumberFormat::E164 )
            ] );

            return str_replace( '+', '', $response );

        } catch ( Exception $exception ) {
            Log::error( 'Phone Number Parse Failed:: ' . $exception->getMessage(), [
                'phone' => $number
            ] );

            return $number;
        }
    }

    /**
    * @param $sms
    */

    function reportThis( $sms )
 {
        \App\Models\User::where( 'phone', 255755277133 )->first()->notify( ( new \App\Notifications\SendSMSNotification( $sms ) )->delay( now()->addSeconds( 2 ) ) );

    }
}

if ( !function_exists( 'generate_random_number' ) ) {
    function generate_random_number() {
       return $randomNumber = random_int(100000, 999999);
    }
}
