<?php
   $tax = $repair->tax;
   $price_without_tax = $repair->total; // PRICE WITHOUT TAX
   $total = $repair->grand_total; // PRICE WITH TAX
   ?>


<!DOCTYPE html>
<html>
   <head>
      <title>Repair Reciept</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('css/templates/invoice3.css') }}" rel="stylesheet">
      
   </head>
   <body>
      <div id="invoice-POS">
      <center id="top">
            <div class="logo">
               <img src="{{ asset(config('config.main_logo')) }}">
            </div>
            <div class="info">
               <h2>{{config('config.company_name')}}</h2>
               <p> 
                  {{__('configuration.address')}} : {{config('config.address_line_1')}}</br>
                  {{__('configuration.website')}}   : {{config('config.website')}}</br>
                  {{__('configuration.phone')}}   : {{config('config.phone')}}</br>
               </p>
            </div>
         </center>
         <div class="text-center">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($repair->code, "C128") }}" alt="barcode"   />
         </div>
         <div class="text-center">{{$repair->telephone }}</div>
         <main>
            <div class="clearfix"></div>
            <div id="details" class="clearfix">
               <div id="client" contentEditable="true">
                  <div class="to">{{ __('customer.customer')}}:</div>
                  <h2 class="name">{{$repair->name }}</h2>
                  <div class="company">{{($repair->customerData ? $repair->customerData->company : '') }}</div>
                  <div class="address">{{($repair->customerData ? $repair->customerData->address : '') }}</div>
                  <div class="postal_code">{{($repair->customerData ? $repair->customerData->city : '') }} {{($repair->customerData ? $repair->customerData->postal_code : '') }}</div>
                  <div class="email"><a {{ $repair->customerData ? 'href="mailto:'.$repair->customerData->email.'"' : '' }} >{{$repair->customerData ? $repair->customerData->email : '' }}</a></div>
                  <div class="telephone">{{$repair->customerData ? $repair->customerData->telephone : '' }}</div>
               </div>
            </div>
            <div id="dati">
               <div class="col"><b>{{ __('repair.created_at')}}:</b> {{ date_format(date_create($repair->created_at),"d/m/Y") }}</div>
               <div class="col"><b>{{ __('repair.model')}}:</b> {{$repair->model_name}}</div>
               <div class="col"><b>{{ __('repair.category')}}:</b> {{$repair->category}}</div>
               <div class="col"><b>{{ __('repair.defect')}}:</b> {{$repair->defect}}</div>
               <div class="col"><b>{{ __('grand_total')}}:</b> {{formatMoney($repair->grand_total)}}</div>
               <div class="col"><b>{{ __('repair.paid')}}:</b> {{formatMoney($repair->paid)}}</div>
               <div class="col"><b>{{ __('repair.balance')}}:</b> {{formatMoney($repair->grand_total - $repair->paid)}}</div>
               <div class="col"><b>{{ __('repair.code')}}:</b> {{$repair->code}}</div>
               <div class="col"><b>{{ __('repair.serial_number')}}:</b> {{$repair->serial_number}}</div>
               <div class="col txt">
                  <textarea id="comment" class="form-control" onkeyup="auto_grow(this)" contentEditable="true">{{$repair->comment }}</textarea>
               </div>
               <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <div>
                  {{ ($disclaimer) }}
            </div>
         </main>
         <div id="print_button">{{ __('print')}}</div>
         <div class="clearfix"></div>
      
      </div>
      
   </body>
   <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
</html>