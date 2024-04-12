<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortUrl extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->attributes['code'];
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'code';
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function newUrl($url)
    {

        $code = value(function() {
            $code = rand_crypto(4, 'distinct');
            while (self::where('code', $code)->exists()) {
                info($code. ' exists, generating new url code');
                $code = rand_crypto(4, 'distinct');
            }
            return $code;
        });

        return self::updateOrCreate([
            'url' => $url,
        ], [
            'code' => $code,
            'expire_at' => now()->addMonths(6)
        ]);
    }

    /**
     * @param $code
     * @return null
     */
    public static function url($code)
    {
        $url = self::where('code', $code)->first();
        if ($url && $url->expire_at != null && $url->expire_at->isFuture()) {
            return $url;
        }
        return null;
    }
}
