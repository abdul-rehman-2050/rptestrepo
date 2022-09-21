<?php
namespace App\Repositories;

use Illuminate\Validation\ValidationException;

use App\Repositories\ConfigurationRepository;

class NotificationRepository
{
    protected $config;
    protected $configData;

    public function __construct(ConfigurationRepository $config) {
        $this->config = $config;
        $this->configData = $config->getAll();
    }

    public function sendSms($number, $text){
        switch ($this->configData['sms_provider']) {
            case 'nexmo':
                try {
                     $message = \Nexmo::message()->send([
                        'to' => $number,
                        'from' => env('NEXMO_NUMBER'),
                        'text' => $text
                    ]);
                } catch (\Exception $e) {
                    return ['success'=>false, 'msg' => $e->getMessage()];
                }

                if ($message['status'] == 0) {
                    return ['success' => true, 'msg'=>null];
                }

                break;
            case 'chat-api':
                $data = [
                    'phone' => $number, // Receivers phone
                    'body' => $text, // Message
                ];
                $json = json_encode($data); // Encode data to JSON
                // URL for request POST /message
                $token = $this->configData['chat_api_token'];
                $instanceId = $this->configData['chat_api_instance_id'];
                $url = 'https://api.chat-api.com/instance'.$instanceId.'/message?token='.$token;
                // Make a POST request
                $options = stream_context_create(['http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/json',
                        'content' => $json
                    ]
                ]);
                // Send a request
                try {
                    $result = file_get_contents($url, false, $options);
                    $result = json_decode($result);
                    if($result->sent){
                        return ['success'=>true, 'msg' => $result->message];
                    }else{
                        return ['success'=>false, 'msg' => $result->message];
                    }
                } catch (\Throwable $th) {
                    return ['success'=>false, 'msg' => $th->getMessage()];
                }

                break;
            case 'twilio':

                $accountSid = $this->configData['twilio_sid'];
                $authToken  = $this->configData['twilio_token'];
                $twilioNumber  = $this->configData['twilio_phone_number'];
                $client = new \Twilio\Rest\Client($accountSid, $authToken);
                

                try {
                    $message = $client->messages->create(
                        $number,
                        array(
                            'from' => $twilioNumber,
                            'body' => $text
                        )
                    );
                } catch (\Exception $e) {
                    return ['success'=>false, 'msg' => $e->getMessage()];
                }
                if ($message->sid) {
                    return ['success'=>true, 'msg'=>null];
                }

                break;
            case 'sms_gateway_me':


                $array_fields['phone_number'] = $number;
                $array_fields['message'] = $text;
                $array_fields['device_id'] = $this->configData['sms_gateway_me_device_id'];
                
                $token = $this->configData['sms_gateway_me_token'];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://smsgateway.me/api/v4/message/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "[  " . json_encode($array_fields) . "]",
                    CURLOPT_HTTPHEADER => array(
                        "authorization: $token",
                        "cache-control: no-cache"
                    ),
                ));

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                if ($err) {
                    return ['success'=>false, 'msg' => (is_array($err) ? json_encode($err) : $err)];
                } else {
                    try {
                        return ['success'=>true, 'msg'=> @json_decode($response)[0]->status];
                    } catch (Exception $e) {
                        
                    }
                }
                break;
            case 'sms_gateway':
                $api = \App\SmsGateway::find($this->configData['sms_gateway']);
                if ($api) {
                    $append = "?";
                    $append .= $api->to_name . "=" . $number;
                    $append .= "&" . $api->message_name . "=" . urlencode($text);
                    $postdata = [];
                    try {
                        $postdata = @json_decode($api->postdata);
                    } catch (Exception $e) {
                        
                    }

                    foreach ($postdata as $d) {
                        $append .= "&" . $d->name . "=" . $d->value;
                    }

                    $url = $api->url . $append;

                    //send sms here
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_ENCODING, "");
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $curl_scraped_page = curl_exec($ch);
                    curl_close($ch);
                    return ['success'=>true, 'response'=>$curl_scraped_page];
                }
                return ['success'=>false, 'msg'=>'error getting script'];
                break;
            default:
                // just log it
                break;
        }

    }


}
