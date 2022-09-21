<?php
if (!function_exists('dataTable')) {
    function dataTable()
    {
        return app('dataTable');
    }
}

/*
 *  Used to write in .env file
 *  @param
 *  $data as array of .env key & value
 *  @return nothing
 */

function envu($data = array())
{
    foreach ($data as $key => $value) {
        if (env($key) === $value) {
            unset($data[$key]);
        }
    }

    if (!count($data)) {
        return false;
    }

    // write only if there is change in content

    $env = file_get_contents(base_path() . '/.env');
    $env = explode("\n", $env);
    foreach ((array)$data as $key => $value) {
        foreach ($env as $env_key => $env_value) {
            $entry = explode("=", $env_value, 2);
            if ($entry[0] === $key) {
                $env[$env_key] = $key . "=" . (is_string($value) ? '"'.$value.'"' : $value);
            } else {
                $env[$env_key] = $env_value;
            }
        }
    }
    $env = implode("\n", $env);
    file_put_contents(base_path() . '/.env', $env);
    return true;
}

//////////////////////////////////////////////////////////////////////// Date helper function starts

/*
 *  Used to check whether date is valid or not
 *  @param
 *  $date as timestamp or date variable
 *  @return true if valid date, else if not
 */

function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/*
 *  Used to get date with start midnight time
 *  @param
 *  $date as timestamp or date variable
 *  @return date with start midnight time
 */

function getStartOfDate($date)
{
    return date('Y-m-d', strtotime($date)).' 00:00';
}

/*
 *  Used to get date with end midnight time
 *  @param
 *  $date as timestamp or date variable
 *  @return date with end midnight time
 */

function getEndOfDate($date)
{
    return date('Y-m-d', strtotime($date)).' 23:59';
}

/*
 *  Used to get date in desired format
 *  @return date format
 */


function decode_html($str)
{
    return html_entity_decode($str, ENT_QUOTES | ENT_XHTML | ENT_HTML5, 'UTF-8');
}


function base64url_decode($data)
{
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function base64url_encode($data, $pad = null)
{
    $data = str_replace(['+', '/'], ['-', '_'], base64_encode($data));
    if (!$pad) {
        $data = rtrim($data, '=');
    }
    return $data;
}

function getDateFormat()
{
    if (config('config.date_format') === 'DD-MM-YYYY') {
        return 'd-m-Y';
    } elseif (config('config.date_format') === 'MM-DD-YYYY') {
        return 'm-d-Y';
    } elseif (config('config.date_format') === 'DD-MMM-YYYY') {
        return 'd-M-Y';
    } elseif (config('config.date_format') === 'MMM-DD-YYYY') {
        return 'M-d-Y';
    } else {
        return 'd-m-Y';
    }
}

/*
 *  Used to convert date for database
 *  @param
 *  $date as date
 *  @return date
 */

function toDate($date)
{
    if (!$date) {
        return;
    }

    return date('Y-m-d', strtotime($date));
}

/*
 *  Used to convert date in desired format
 *  @param
 *  $date as date
 *  @return date
 */

function showDate($date)
{
    if (!$date) {
        return;
    }

    $date_format = getDateFormat();
    return date($date_format, strtotime($date));
}

/*
 *  Used to convert time in desired format
 *  @param
 *  $datetime as datetime
 *  @return datetime
 */

function showDateTime($time = '')
{
    if (!$time) {
        return;
    }

    $date_format = getDateFormat();
    if (config('config.time_format') === 'H:mm') {
        return date($date_format.',H:i', strtotime($time));
    } else {
        return date($date_format.',h:i a', strtotime($time));
    }
}

/*
 *  Used to convert time in desired format
 *  @param
 *  $time as time
 *  @return time
 */

function showTime($time = '')
{
    if (!$time) {
        return;
    }

    if (config('config.time_format') === 'H:mm') {
        return date('H:i', strtotime($time));
    } else {
        return date('h:i a', strtotime($time));
    }
}
//////////////////////////////////////////////////////////////////////// Date helper function ends

//////////////////////////////////////////////////////////////////////// String helper function starts

/*
 *  Used to convert slugs into human readable words
 *  @param
 *  $word as string
 *  @return string
 */

function toWord($word)
{
    $word = str_replace('_', ' ', $word);
    $word = str_replace('-', ' ', $word);
    $word = ucwords($word);
    return $word;
}

/*
 *  Used to generate random string of certain lenght
 *  @param
 *  $length as numeric
 *  $type as optional param, can be token or password or username. Default is token
 *  @return random string
 */

function randomString($length, $type = 'token')
{
    if ($type === 'password') {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    } elseif ($type === 'username') {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    } else {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    $token = substr(str_shuffle($chars), 0, $length);
    return $token;
}

/*
 *  Used to whether string contains unicode
 *  @param
 *  $string as string
 *  @return boolean
 */

function checkUnicode($string)
{
    if (strlen($string) != strlen(utf8_decode($string))) {
        return true;
    } else {
        return false;
    }
}

/*
 *  Used to generate slug from string
 *  @param
 *  $string as string
 *  @return slug
 */

function createSlug($string)
{
    if (checkUnicode($string)) {
        $slug = str_replace(' ', '-', $string);
    } else {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    }
    return $slug;
}

/*
 *  Used to remove script tag from input
 *  @param
 *  $string as string
 *  @return slug
 */

function scriptStripper($string)
{
    return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $string);
}

//////////////////////////////////////////////////////////////////////////////////// String helper function ends

//////////////////////////////////////////////////////////////////////////////////// Select helper function starts

/*
 *  Used to generate select option for vue.js multiselect plugin
 *  @param
 *  $data as array of key & value pair
 *  @return select options
 */

function generateSelectOption($data)
{
    $options = array();
    foreach ($data as $key => $value) {
        $options[] = ['name' => $value, 'id' => $key];
    }
    return $options;
}

/*
 *  Used to generate translated select option for vue.js multiselect plugin
 *  @param
 *  $data as array of key & value pair
 *  @return select options
 */

function generateTranslatedSelectOption($data)
{
    $options = array();
    foreach ($data as $key => $value) {
        $options[] = ['name' => trans('list.'.$value), 'id' => $value];
    }
    return $options;
}

/*
 *  Used to generate select option for default select box
 *  @param
 *  $data as array of key & value pair
 *  @return select options
 */

function generateNormalSelectOption($data)
{
    $options = array();
    foreach ($data as $key => $value) {
        $options[] = ['text' => $value, 'value' => $key];
    }
    return $options;
}

/*
 *  Used to generate select option for default select box where value is same as text
 *  @param
 *  $data as array of key & value pair
 *  @return select options
 */

function generateNormalSelectOptionValueOnly($data)
{
    $options = array();
    foreach ($data as $value) {
        $options[] = ['text' => $value, 'value' => $value];
    }
    return $options;
}

//////////////////////////////////////////////////////////////////////////////////// Select helper function ends

/*
 *  Used to round number
 *  @param
 *  $number as numeric value
 *  $decimal_place as integer for round precision
 *  @return number
 */

function formatNumber($number, $decimal_place = 2)
{
    return round($number, $decimal_place);
}

////////////////////////////////////////////////////////////////////////////////////// IP helper function starts

/*
 *  Used to check whether IP is in range
 */

function ipRange($network, $ip)
{
    $network=trim($network);
    $orig_network = $network;
    $ip = trim($ip);
    if ($ip === $network) {
        return true;
    }
    $network = str_replace(' ', '', $network);
    if (strpos($network, '*') != false) {
        if (strpos($network, '/') != false) {
            $asParts = explode('/', $network);
            $network = @ $asParts[0];
        }
        $nCount = substr_count($network, '*');
        $network = str_replace('*', '0', $network);
        if ($nCount === 1) {
            $network .= '/24';
        } elseif ($nCount === 2) {
            $network .= '/16';
        } elseif ($nCount === 3) {
            $network .= '/8';
        } elseif ($nCount > 3) {
            return true;
        }
    }

    $d = strpos($network, '-');
    if ($d === false) {
        $ip_arr = explode('/', $network);
        if (!preg_match("@\d*\.\d*\.\d*\.\d*@", $ip_arr[0], $matches)) {
            $ip_arr[0].=".0";
        }
        $network_long = ip2long($ip_arr[0]);
        $x = ip2long($ip_arr[1]);
        $mask = long2ip($x) === $ip_arr[1] ? $x : (0xffffffff << (32 - $ip_arr[1]));
        $ip_long = ip2long($ip);
        return ($ip_long & $mask) === ($network_long & $mask);
    } else {
        $from = trim(ip2long(substr($network, 0, $d)));
        $to = trim(ip2long(substr($network, $d+1)));
        $ip = ip2long($ip);
        return ($ip>=$from and $ip<=$to);
    }
}

/*
 *  Used to check whether IP is valid or not
 *  @return boolean
 */

function validateIp($wl_ips)
{

    // $ip = getClientIp();
    $ip = '192.168.1.1';

    $allowedIps = array();
    foreach ($wl_ips as $wl_ip) {
        if ($wl_ip->end_ip) {
            $allowedIps[] = $wl_ip->start_ip.'-'.$wl_ip->end_ip;
        } else {
            $allowedIps[] = $wl_ip->start_ip;
        }
    }

    foreach ($allowedIps as $allowedIp) {
        if (strpos($allowedIp, '*')) {
            $range = [
                str_replace('*', '0', $allowedIp),
                str_replace('*', '255', $allowedIp)
            ];
            if (ipExistsInRange($range, $ip)) {
                return true;
            }
        } elseif (strpos($allowedIp, '-')) {
            $range = explode('-', str_replace(' ', '', $allowedIp));
            if (ipExistsInRange($range, $ip)) {
                return true;
            }
        } else {
            if (ip2long($allowedIp) === ip2long($ip)) {
                return true;
            }
        }
    }
    return false;
}

function ipExistsInRange(array $range, $ip)
{
    if (ip2long($ip) >= ip2long($range[0]) && ip2long($ip) <= ip2long($range[1])) {
        return true;
    }
    return false;
}

/*
 *  Used to get IP address of visitor
 *  @return date
 */

function getRemoteIPAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

/*
 *  Used to get IP address of visitor
 *  @return IP address
 */

function getClientIp()
{
    $ips = getRemoteIPAddress();
    $ips = explode(',', $ips);
    return !empty($ips[0]) ? $ips[0] : \Request::getClientIp();
}

////////////////////////////////////////////////////////////////////////////////////////// IP helper function ends

/*
 *  Used to check mode
 *  @return boolean
 */

function isTestMode()
{


    if (env('APP_DEMO')) {
        return true;
    } else {
        return false;
    }
}

/*
 * get Maximum post size of server
 */

function getPostMaxSize()
{
    if (is_numeric($postMaxSize = ini_get('post_max_size'))) {
        return (int) $postMaxSize;
    }

    $metric = strtoupper(substr($postMaxSize, -1));
    $postMaxSize = (int) $postMaxSize;

    switch ($metric) {
        case 'K':
            return $postMaxSize * 1024;
        case 'M':
            return $postMaxSize * 1048576;
        case 'G':
            return $postMaxSize * 1073741824;
        default:
            return $postMaxSize;
    }
}

/*
 *  Used to get value-list json
 *  @return array
 */

function getVar($list)
{
    $file = resource_path('var/'.$list.'.json');

    return (\File::exists($file)) ? json_decode(file_get_contents($file), true) : [];
}

function getDefaultCurrencyCode() {
    $currency = \App\Currency::find(config('config.currency'));
    if (!$currency) {
        $currency = \App\Currency::create([
            'code'=>'USD',
            'name'=>'US Dollars',
            'symbol'=>'$',
            'symbol_position'=>'before',
        ]);
    }
    return $currency;
}

function formatMoney($number, $decimal_place = 2) {
    $currency = \App\Currency::find(config('config.currency'));
    if (!$currency) {
        $currency = \App\Currency::create([
            'code'=>'USD',
            'name'=>'US Dollars',
            'symbol'=>'$',
            'symbol_position'=>'before',
        ]);
    }

    $symbol = $currency->symbol;
    $symbol_position = $currency->symbol_position;

    return ($symbol_position == 'before' ? $symbol : '') . number_format($number,$decimal_place) . ($symbol_position == 'after' ? $symbol : '');
}

function getXMin($im, $w, $h) {
    for($x=0;$x<$w;++$x) {
        for($y=0;$y<$h;++$y) {
            if(imagecolorat($im, $x, $y) != 0xFFFFFF) {
                return $x;
            }
        }
    }
}

function getYMin($im, $w, $h) {
    for($y=0;$y<$h;++$y) {
        for($x=0;$x<$w;++$x) {
            if(imagecolorat($im, $x, $y) != 0xFFFFFF) {
                return $y;
            }
        }
    }
}

function getXMax($im, $w, $h) {
    for($x=($w-1);$x>=0;--$x) {
        for($y=0;$y<$h;++$y) {
            if(imagecolorat($im, $x, $y) != 0xFFFFFF) {
                return $x;
            }
        }
    }
}

function getYMax($im, $w, $h) {
    for($y=($h-1);$y>=0;--$y) {
        for($x=0;$x<$w;++$x) {
            if(imagecolorat($im, $x, $y) != 0xFFFFFF) {
                return $y;
            }
        }
    }
}

function getImage($filename) {
    $type = exif_imagetype($filename);

    switch($type) {
        case IMAGETYPE_GIF:
            return imagecreatefromgif($filename);
            break;
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($filename);
            break;
        case IMAGETYPE_PNG:
            return imagecreatefrompng($filename);
            break;
        case IMAGETYPE_BMP:
            return imagecreatefromwbmp($filename);
            break;
        default:
            print 'UNKNOWN IMAGE TYPE: ' . image_type_to_mime_type($type) . "\n";
            return FALSE;
            break;
    }

}


function remove_whitespace($img)
{

    /* Get image */
    $im = getImage($img);
    $cropped = imagecropauto($im, IMG_CROP_WHITE);
    if ($cropped !== false) { // in case a new image resource was returned
        imagedestroy($im);    // we destroy the original image
        $im = $cropped;       // and assign the cropped image to $im
    }

    $cropped = imagecropauto($im, IMG_CROP_DEFAULT);
    if ($cropped !== false) { // in case a new image resource was returned
        imagedestroy($im);    // we destroy the original image
        $im = $cropped;       // and assign the cropped image to $im
    }

    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
    die();
}

function getCardBalance($number)
{
    if ($card = \App\Voucher::where('card_no', $number)->first()) {
        return $card->balance;
    }
    return 0;
}


function getCustomFieldByRepairID($id, $model_type = 'App\Repair') {
            
    $fields = \App\CustomField::where('model_type', $model_type)->orderBy('order')->where('deleted_at', null)->get();
    $res = [];
    foreach ($fields as $field) {
        $field_resp = DB::table('custom_field_responses')->selectRaw('custom_field_responses.model_id, IF(custom_field_responses.value_int IS NOT NULL, custom_field_responses.value_int, IF(custom_field_responses.value_str IS NOT NULL, custom_field_responses.value_str, custom_field_responses.value_text)) as value')
            ->where('custom_field_responses.model_id', $id)
            ->where('field_id', $field->id)
            ->where('custom_field_responses.model_type', $model_type)->first();
        $res[] = [
            'name' => $field->title,
            'value' => $field_resp ? $field_resp->value : '',
        ];
    }
    return $res;
}


function pos_boot($ul, $pt, $lc, $em, $un, $type = 1)
{
    $ch = curl_init();
    $request_url = base64_decode(config('author.lic1'));
    $curlConfig = [CURLOPT_URL => $request_url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS     => [
            'url' => $ul,
            'path' => $pt,
            'license_code' => $lc,
            'email' => $em,
            'username' => $un,
            'product_id' => config('author.pid')
        ]
    ];
    curl_setopt_array($ch, $curlConfig);
    $result = curl_exec($ch);

    


    if (curl_errno($ch)) {
        $error_msg = 'C'.'U'.'RL '.'E'.'rro'.'r: ';
        $error_msg .= curl_errno($ch);
        $error_msg .= curl_errno($ch);
        

        return redirect()->back()
            ->with('error', $error_msg);
    }
    curl_close($ch);

    if ($result) {
        $result = json_decode($result, true);
        if ($result['status'] == 'success') {
            
        } else {
            return redirect()->back()->with('error', "I"."nvali"."d "."Lic"."ense Det"."ails");
        }
    }
}

/**
 * Check if an Application 
   is installed or not
 *
 */
function isAppInstalled()
{
    return file_exists(base_path('.env'));
}
