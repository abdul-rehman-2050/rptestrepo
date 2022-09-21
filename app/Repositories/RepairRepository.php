<?php
namespace App\Repositories;

use App\Repair;
use Illuminate\Validation\ValidationException;
use App\Repositories\ConfigurationRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\NotificationLogRepository;
use App\Repositories\Site;
use App\Helpers\CustomHtmlable;
use App\Repositories\TaxRepository;
use App\Repositories\PaymentRepository;

use Mail;
class RepairRepository
{
    protected $repair;
    protected $configData;
    protected $notification;
    protected $notification_log;
    protected $site;
    protected $payment;
    protected $tax;

    public function __construct(Repair $repair, PaymentRepository $payment, ConfigurationRepository $config, NotificationRepository $notification, NotificationLogRepository $notification_log, Site $site, TaxRepository $tax)
    {
        $this->repair = $repair;
        $this->site = $site;
        $this->configData = $config->getAll();
        $this->notification = $notification;
        $this->notification_log = $notification_log;
        $this->tax = $tax;
        $this->payment = $payment;
    }



    public function preRequisite($id = null) {

        if ( $id ) {
             $fields = \App\CustomField::selectRaw('custom_fields.*, IF(custom_field_responses.value_int IS NOT NULL, custom_field_responses.value_int, IF(custom_field_responses.value_str IS NOT NULL, custom_field_responses.value_str, custom_field_responses.value_text)) as value')->where('custom_fields.model_type', get_class($this->repair))
                ->leftJoin('custom_field_responses', 'custom_fields.id', '=', 'custom_field_responses.field_id')
                ->where('custom_field_responses.model_id', $id)
                ->orWhere('custom_field_responses.id', null)
                ->get();
        }else{
            $fields = \App\CustomField::where('model_type', get_class($this->repair))->get();
        }
        
        $customers = \App\Company::onlyCustomers()->get();
        if(config('config.enable_multistore') && request()->header('StoreID') > 0) {
            $customers = \App\Company::where('store_id', request()->header('StoreID'))->onlyCustomers()->get();
        }
        $taxes = $this->tax->listTaxRates();
        $statuses = \App\Status::orderByRaw('position ASC')->get();
        $users = \App\User::with('profile')->get();
        $manufacturers = \App\ItemType::where('parent_id', null)->get();

        return (compact('customers', 'taxes', 'statuses', 'users', 'fields', 'manufacturers'));


    }



    public function calculateDiscount($discount = NULL, $amount) {
        if ($discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode("%", $discount);
                return formatNumber((((formatNumber($amount)) * (Float) ($pds[0])) / 100), 4);
            } else {
                return formatNumber($discount, 4);
            }
        }
        return 0;
    }


    public function calculateTax($product_details = NULL, $tax_details, $custom_value = NULL, $c_on = NULL) {
        $value = $custom_value ? $custom_value : (($c_on == 'cost') ? $product_details['purchase_price_net'] : $product_details['net_unit_price']);
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            $tax_amount = formatNumber((($value) * $tax_details->rate) / 100, 4);
            $tax = $tax_details->name;
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = formatNumber($tax_details->rate);
            $tax = $tax_details->name;
        }
        return array('tax' => $tax, 'amount' => $tax_amount);
    }

    public function calculateOrderTax($tax_id, $value) {
        $tax_details = \App\Tax::find($tax_id);
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            $tax_amount = formatNumber((($value) * $tax_details->rate) / 100, 4);
            $tax = $tax_details->name;
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = formatNumber($tax_details->rate);
            $tax = $tax_details->name;
        }
        return $tax_amount;
    }

    public function send_sms($number, $text, $repair, $customer, $status) {
        if (substr($number, 0, 1) !== '+') {
            $number = '+'.$number;
        }
        $search  = array('%businessname%', '%customer%', '%model%', '%site_url%', '%statuscode%', '%id%', '%statusurl%', '%total%', '%servicecharges%');
        $replace = array(config('config.company_name'), $customer->name, $repair->model, \URL::to('/'), $repair->code, $repair->id,  \URL::to('/check-status?code='.$repair->code), $repair->grand_total, $repair->service_charges);
        $text = str_replace($search, $replace, $text);
        $result = $this->notification->sendSms($number, $text);
        $this->notification_log->record([
            'type' => 'sms',
            'body' => $text,
            'phone_number' => $number,
            'module' => 'dashboard',
            'module_id' => null,
        ]);
        return $result;
    }


    public function email_message($email, $subject,$body, $repair, $customer, $status) {
        $search  = array('%businessname%', '%customer%', '%model%', '%site_url%', '%statuscode%', '%businesscontact%', '%id%', '%statusurl%', '%total%', '%servicecharges%');
        $replace = array(config('config.company_name'), $customer->name, $repair->model, \URL::to('/'), $repair->code, config('config.phone'),  $repair->id, \URL::to('/check-status?code='.$repair->code), $repair->grand_total, $repair->service_charges);
        $body = new CustomHtmlable(str_replace($search, $replace, $body));
        $from_name = config('config.from_name');
        $from_address = config('config.from_address');
        try {
            $mail = Mail::send('emails.email', compact('body'), function ($message) use ($subject, $email, $from_name, $from_address) {
                $message->from($from_address, $from_name)->to($email)->subject($subject);
            });
        } catch (\Exception $e) {
            return ['success'=>false, 'msg'=> $e->getMessage()];
        }
    }

  
    public function change_status($repair,  $status_text = '') {
        $sms_result = FALSE;
        $email_result = FALSE;


        $status = \App\Status::find($repair->status_id);
        $customer = \App\Company::find($repair->customer_id);


        if ($repair->send_email && $status->send_email) {
            $email_body = $status->email_text;
            
            $subject =  __('repair.status_change_email_subject', ['status' => $status->label]);
            if ($status->email_subject !== '') {
                $subject = sprintf($status->email_subject, $status->label);
            }

            $email_result = $this->email_message($customer->email, $subject, $email_body, $repair, $customer, $status);
        }

        if ($repair->send_sms && $status->send_sms && $customer->phone !== '') {
            $sms_result = $this->send_sms($customer->phone, $status->sms_text, $repair, $customer, $status);
        }
     
        $repair->status_id = $status->id;
        if ($status->completed) {
            $repair->closed_at = date('Y-m-d H:i:s');
        }else{
            $repair->closed_at = null;
        }
        $repair->save();

        $returnData = array();
        $returnData['sms_sent'] = $sms_result;
        $returnData['email_sent'] = $email_result;
        $returnData['label'] = $status->label;
        return $returnData;
    }


    public function getActiveStatuses($completed) {
        $q2 = \App\Status::where('completed', $completed)->get();        
        $status = array();
        foreach ($q2 as $row) {
            $status[] = $row->id;
        }
        return $status;
    }


    public function checkQty($items, $id = null) {
        foreach ($items as $item) {
            if($item['product_type'] == 'service'){
                // do nothing
            }else{
                $qty_to_add = 0;
                if ($id) {
                    $sale_item = \App\SaleItem::where('repair_id', $id)->where('product_id', $item['product_id'])->first();
                    if ($sale_item) {
                        $qty_to_add = $sale_item->quantity;
                    }
                }
                $product = \App\Product::where('id', $item['product_id'])
                    ->first();

                if ($product) {
                    if (($product->quantity + $qty_to_add) >= (int)$item['qty']) {
                        continue;
                    }
                    return [
                        'success'=>false, 
                        'product_name'=>__('repair.product_x_not_in_stock', ['product'=>$item['product_name'], 'quantity'=>$product->quantity + $qty_to_add])
                    ];

                }
            }
        }
        return ['success'=>true];
    }

    public function create($request, $register = 0)
    {

        if (!empty($request->input('items'))) {
            $items = $request->input('items');

            if (!config('config.enable_overselling')) {
                $data = $this->checkQty($items);
                if (!$data['success']) {
                    return $data;
                }
            }
        }

        
        $params = $request->only(['serial_number','customer_id','category','assigned_to','manufacturer','model', 'manufacturer_id','model_id', 'defect','service_charges','expected_close_date','has_warranty','warranty_period','comments','diagnostics', 'intake_signature', 'repair_toggles','pattern','pin', 'code','status_id','send_sms','send_email', 'taxrate_id', 'imei']);

        if ($params['taxrate_id'] == '') {
            $params['taxrate_id'] = null;
        }else{
            $params['taxrate_id'] = \App\Tax::find($params['taxrate_id']) ? $params['taxrate_id'] : null;
        }

        if ($params['manufacturer_id'] > 0) {
            $m = \App\ItemType::find($params['manufacturer_id']);
            $params['manufacturer'] = $m ? $m->name : '';
        }else{
            $params['manufacturer_id'] = null;
            $params['manufacturer'] = $params['manufacturer'] !== '' ? $params['manufacturer'] : '';
        }
        if ($params['model_id'] > 0) {
            $m = \App\ItemType::find($params['model_id']);
            $params['model'] = $m ? $m->name : '';
        }else{
            $params['model_id'] = null;
            $params['model'] = $params['model'] !== '' ? $params['model'] : '';
        }

        
        if(config('config.enable_multistore') && $request->header('StoreID') > 0) {
            $store = $request->header('StoreID');
            $params['store_id'] = $store;
        }elseif (\Auth::user()->hasRole('admin') && $request->get('store_id') > 0) {
            $params['store_id'] = $request->get('store_id');
        }


        if ($params['expected_close_date'] == '') {
            $params['expected_close_date'] = null;
        }
        
        if (!$params['assigned_to']) {
            unset($params['assigned_to']);
        }
        
        $params['created_by'] = \Auth::user()->id;
        $customer = \App\Company::find($params['customer_id']);
        if ($customer) {
            $params['customer'] = $customer->name;
        }

        $image =  is_array($params['intake_signature']) ? $params['intake_signature']['data'] : null;  // your base64 encoded
        if ($image) {
             $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            \File::put(public_path('uploads/signs'). '/' . $imageName, base64_decode($image));
            $params['intake_signature'] = $imageName;
        }
       


        $rt = $params['repair_toggles'];
        $params['repair_toggles'] = [];
        foreach ($rt as $key => $bool) {
            if ($bool) {
                $params['repair_toggles'][$key] = $bool;
            }
        }
        $params['repair_toggles'] = json_encode($params['repair_toggles']);

        $repair = Repair::create($params);
        $attachments = $request->input('attachments');
        foreach ($attachments as $attachment) {
            \App\Attachment::find($attachment)->update(['repair_id'=>$repair->id]);
        }

        $total = (float) $params['service_charges'];
        $product_tax = 0;

        if (!empty($request->input('items'))) {
            $items = $request->input('items');

            $data = [];
            foreach ($items as $value) {
                if (!empty($value)) {
                    // check each product stock

                    $tax = $this->calculateTax($value, \App\Tax::find($value['taxrate_id']));
                    
                    if ($value['taxrate_id'] == '') {
                        $value['taxrate_id'] = null;
                    }
                    
                    // it will be a feature later. thats why didn't remove.
                    // $pr_discount = $this->calculateDiscount($value['item_discount'], $value['price_gross']);
                    // dd($pr_discount);
                    // calculate tax string and total tax
                    $data[] = [
                        'repair_id' => $repair->repair_id,
                        'product_id' => $value['product_id'],
                        'product_code' => $value['product_code'],
                        'product_name' => $value['product_name'],
                        'product_type' => $value['product_type'],
                        'net_unit_price' => $value['net_unit_price'],
                        'unit_price' => $value['unit_price'],
                        'quantity' => $value['qty'],
                        'purchase_price_gross' => $value['purchase_price_gross'],
                        'taxrate_id' => $value['taxrate_id'],
                        'item_tax' => $tax['tax'],
                        'tax' => $tax['amount'] * $value['qty'],
                        'subtotal' => $value['unit_price'] * $value['qty'],
                        'discount' => $value['discount'],
                        'item_discount' => $value['item_discount'],
                        'comment' => $value['comment'],
                    ];

                    $product_tax += (float) ($tax['amount'] * $value['qty']);
                    $total += (float) $value['unit_price'] * $value['qty'];
                }
            }
            $repair->items()->createMany($data);

            // remove all previous costing
            \App\Costing::where('repair_id', $repair->id)->delete();
            // add new costing depending on all items
            foreach (\App\SaleItem::where('repair_id', $repair->id)->get() as $item) {
                $product = \App\Product::find($item->product_id);
                if (!$product->service) {
                    \App\Costing::create([
                        'product_id' => $item->product_id,
                        'repair_id' => $item->repair_id,
                        'sale_item_id' => $item->id,
                        'code' => $item->product_code,
                        'name' => $item->product_name,
                        'cost' => $item->purchase_price_gross,
                        'quantity' => 0-$item->quantity,
                    ]);
                }
            }
            $this->site->syncQuantity($repair->id);
        }

        $order_tax      = $this->calculateOrderTax($params['taxrate_id'], $total);

        $total_tax      = $product_tax + $order_tax;
        $grand_total    = $total + $order_tax;

        $repair->product_tax = $product_tax;
        $repair->order_tax = $order_tax;
        $repair->total_tax = $total_tax;
        $repair->total = $total;
        $repair->grand_total = $grand_total;
        $repair->save();


        try {
            $payment = $request->get('payment');
            if($payment){
                $payment['repair_id'] = $repair->id;
                $this->payment->add($payment);
            }
        } catch (\Throwable $th) {
           
        }
        

        if ($request->input('responses')) {
            $responses = $request->input('responses');
            $validation = $this->repair->validateCustomFields($responses, get_class($this->repair));
            if ($validation->fails()) {
                $repair->delete();
                throw ValidationException::withMessages((array) $validation->errors()->messages());
            }else{
                $this->repair->saveCustomFields($validation->validated(), $repair->id);
            }
        }

        $this->change_status($repair);
        

        return $repair;
    }

    public function update($id, $request)
    {
        if (!empty($request->input('items'))) {
            $items = $request->input('items');

            if (!config('config.enable_overselling')) {
                $data = $this->checkQty($items, $id);
                if (!$data['success']) {
                    return $data;
                }
            }
        }

        $repair = \App\Repair::find($id);
        $params = $request->only(['serial_number','customer_id','category','assigned_to','manufacturer','model', 'manufacturer_id','model_id', 'defect','service_charges','expected_close_date','has_warranty','warranty_period','comments','diagnostics', 'intake_signature', 'repair_toggles','pattern','pin','code','status_id','send_sms','send_email', 'taxrate_id', 'imei']);
        
        if ($params['taxrate_id'] == '') {
            $params['taxrate_id'] = null;
        }

      
        if ($params['manufacturer_id'] > 0) {
            $m = \App\ItemType::find($params['manufacturer_id']);
            $params['manufacturer'] = $m ? $m->name : '';
        }else{
            $params['manufacturer_id'] = null;
            $params['manufacturer'] = $params['manufacturer'] !== '' ? $params['manufacturer'] : '';
        }
        if ($params['model_id'] > 0) {
            $m = \App\ItemType::find($params['model_id']);
            $params['model'] = $m ? $m->name : '';
        }else{
            $params['model_id'] = null;
            $params['model'] = $params['model'] !== '' ? $params['model'] : '';
        }



        $customer = \App\Company::find($params['customer_id']);
        if ($customer) {
            $params['customer'] = $customer->name;
        }

        if (!$params['assigned_to']) {
            unset($params['assigned_to']);
        }
        if ($params['expected_close_date'] == '') {
            $params['expected_close_date'] = null;
        }
        
        $image =  is_array($params['intake_signature']) ? $params['intake_signature']['data'] : null;  // your base64 encoded
        if ($image) {
             $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            \File::put(public_path('uploads/signs'). '/' . $imageName, base64_decode($image));
            $params['intake_signature'] = $imageName;
        }
       
        $rt = $params['repair_toggles'];
        $params['repair_toggles'] = [];
        foreach ($rt as $key => $bool) {
            if ($bool) {
                $params['repair_toggles'][$key] = $bool;
            }
        }
        $params['repair_toggles'] = json_encode($params['repair_toggles']);

        $params['updated_by'] = \Auth::user()->id;
        $repair->update($params);

        $total = (float) $params['service_charges'];
        $product_tax = 0;

        //update variation
        $data = [];
        if (!empty($request->input('items'))) {
            $items = $request->input('items');
            
			
			$ids = array_column($items, 'product_id');
			$repairs = \App\SaleItem::where('repair_id', $id)->whereNotIn('product_id', $ids)->delete();
			
            foreach ($items as $value) {
                $tax = $this->calculateTax($value, \App\Tax::find($value['taxrate_id']));
                if ($value['taxrate_id'] == '') {
                    $value['taxrate_id'] = null;
                }
                if (!empty($value['id'])) {
                    $item = \App\SaleItem::find($value['id']);
                    $item->repair_id = $repair->repair_id;
                    $item->product_id = $value['product_id'];
                    $item->product_code = $value['product_code'];
                    $item->product_name = $value['product_name'];
                    $item->product_type = $value['product_type'];
                    $item->net_unit_price = $value['net_unit_price'];
                    $item->unit_price = $value['unit_price'];
                    $item->quantity = $value['qty'];
                    $item->purchase_price_gross = $value['purchase_price_gross'];
                    $item->taxrate_id = $value['taxrate_id'];
                    $item->item_tax = $tax['tax'];
                    $item->tax = $tax['amount'] * $value['qty'];
                    $item->subtotal = $value['unit_price'] * $value['qty'];
                    $item->discount = $value['discount'];
                    $item->item_discount = (float)$value['item_discount'];
                    $item->comment = @$value['comment'];
                    $data[] = $item;
                }else {
                    $data[] = new \App\SaleItem([
                        'repair_id' => $repair->repair_id,
                        'product_id' => $value['product_id'],
                        'product_code' => $value['product_code'],
                        'product_name' => $value['product_name'],
                        'product_type' => $value['product_type'],
                        'net_unit_price' => $value['net_unit_price'],
                        'unit_price' => $value['unit_price'],
                        'quantity' => $value['qty'],
                        'purchase_price_gross' => $value['purchase_price_gross'],
                        'taxrate_id' => $value['taxrate_id'],
                        'item_tax' => $tax['tax'],
                        'tax' => $tax['amount'] * $value['qty'],
                        'subtotal' => $value['unit_price'] * $value['qty'],
                        'discount' => $value['discount'],
                        'item_discount' => (float)$value['item_discount'],
                        'comment' => @$value['comment'],
                    ]);
                }

                $product_tax += (float) ($tax['amount'] * $value['qty']);
                $total += (float) $value['unit_price'] * $value['qty'];
            }
            $repair->items()->saveMany($data);

           
            // remove all previous costing
            \App\Costing::where('repair_id', $repair->id)->delete();
            // add new costing depending on all items
            foreach (\App\SaleItem::where('repair_id', $repair->id)->get() as $item) {
                $product = \App\Product::find($item->product_id);
                if (!$product->service) {
                    \App\Costing::create([
                        'product_id' => $item->product_id,
                        'repair_id' => $item->repair_id,
                        'sale_item_id' => $item->id,
                        'code' => $item->product_code,
                        'name' => $item->product_name,
                        'cost' => $item->purchase_price_gross,
                        'quantity' => 0-$item->quantity,
                    ]);
                }
            }
            $this->site->syncQuantity($repair->id);

        }
        
        $order_tax = $this->calculateOrderTax($params['taxrate_id'], $total);
        $total_tax = $product_tax + $order_tax;
        $grand_total    = $total + $order_tax;


        $repair->product_tax = $product_tax;
        $repair->order_tax = $order_tax;
        $repair->total_tax = $total_tax;
        $repair->total = $total;
        $repair->grand_total = $grand_total;
        $repair->save();



        if ($request->input('responses')) {
            $responses = $request->input('responses');
            \App\CustomFieldResponse::where('model_id', $repair->id)->where('model_type', get_class($this->repair))->delete();

            $validation = $this->repair->validateCustomFields($responses, get_class($this->repair));
            if ($validation->fails()) {
                throw ValidationException::withMessages((array) $validation->errors()->messages());
            }else{
                $this->repair->saveCustomFields($validation->validated(), $repair->id);
            }
        }


        // change status function
        $this->change_status($repair);
        return $repair;

    }



       
    public function findOrFail($id)
    {
        $repair = $this->repair->find($id);
        if (! $repair) {
            throw ValidationException::withMessages(['message' => trans('repair.could_not_find')]);
        }

        $items = \App\SaleItem::where('repair_id', $id)->get();
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_code' => $item->product_code,
                'product_name' => $item->product_name,
                'product_type' => $item->product_type,
                'net_unit_price' => $item->net_unit_price,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'qty' => $item->quantity,
                'item_tax' => $item->item_tax,
                'taxrate_id' => $item->taxrate_id,
                'tax' => $item->tax,
                'discount' => $item->discount,
                'item_discount' => $item->item_discount,
                'subtotal' =>  $item->subtotal,
                'purchase_price_gross' => $item->purchase_price_gross,
            ];
        }
        


        $repair->models = \App\ItemType::where('parent_id', $repair->manufacturer_id)->get();


        $repair->items = $data;
        return $repair;
    }

    public function delete(Repair $repair)
    {
        
        \App\Costing::where('repair_id', $repair->id)->delete();
        $sale_items = \App\SaleItem::where('repair_id', $repair->id)->get();
        foreach ($sale_items as $item) {
            $this->site->syncProductQty($item->product_id);
        }
        \App\SaleItem::where('repair_id', $repair->id)->delete();
        \App\Attachment::where('repair_id', $repair->id)->delete();
        return $repair->delete();

    }

    public function deleteMultiple($ids)
    {
        return $this->repair->whereIn('id', $ids)->delete();
    }
}
