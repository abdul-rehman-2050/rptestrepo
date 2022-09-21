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
    <style>

    @media print 
    {
        #print_button, #save_button, #email_button {display: none;}
        @page
        {
            size: 80mm;
            size: portrait;
        }
        
    }
    h2{
        text-transform:uppercase;
        font-weight: bolder;
        font-size: 25px;
        -webkit-print-color-adjust: exact;
    }
    .col,.col b {
        font-size: 15px;          
        -webkit-print-color-adjust: exact;
    }
    #details {
        margin-bottom: 10px;
    }
    </style>
   </head>
   <body>
      <div id="invoice-POS">
      <center id="top">
            <div class="logo">
               <img style="width:100%;height: auto" src="{{ asset(config('config.main_logo')) }}">
            </div>
            <div class="info">
               <p> 
                    {{config('config.address_line_1')}}</br>
                    {{config('config.phone')}}</br>
               </p>
            </div>
         </center>
         <main>
            <div class="clearfix"></div>
            <div id="details" class="clearfix">
               <div id="client" contentEditable="true">
                    <h2 class="to">{{ __('customer.customer')}}:</h2>
                    <div class="col"><b>{{ __('repair.code')}}:</b> {{$repair->code}}</div>
                    <div class="col"><b>{{ __('customer.name')}}:</b> {{$repair->customerData ? $repair->customerData->name : $repair->name}}</div>
                    <div class="col"><b>{{ __('customer.phone')}}:</b> {{$repair->customerData ? $repair->customerData->phone : '' }}</div>
               </div>
            </div>

            <div id="details" class="clearfix">
               <div id="client" contentEditable="true">
                    <h2 class="to">{{ __('repair.repair')}}:</h2>
                
                    <div class="col"><b>{{ __('repair.created_at')}}:</b> {{ date_format(date_create($repair->created_at),"d/m/Y") }}</div>
                    <div class="col"><b>{{ __('repair.assigned_to')}}:</b> {{ $repair->assigned_user ? $repair->assigned_user->first_name . ' ' . $repair->assigned_user->last_name : '' }}</div>
                    <br>
                    <div class="col"><b>{{ __('repair.model')}}:</b> {{$repair->model_name}}</div>
                    <div class="col"><b>{{ __('repair.status')}}:</b> {{$repair->status->label}}</div>
                    <div class="col"><b>{{ __('repair.defect')}}:</b> {{$repair->defect}}</div>
                    <div class="col"><b>{{ __('repair.pin')}}:</b> {{$repair->pin}}</div>
               </div>
            </div>

            <div id="details" class="clearfix">
               <div id="client" contentEditable="true">
                    <h2 class="to">{{ __('repair.total')}}:</h2>
                
                    <div class="col"><b>{{ __('repair.grand_total')}}:</b> {{formatMoney($repair->grand_total)}}</div>
                    <div class="col"><b>{{ __('repair.paid')}}:</b> {{formatMoney($repair->paid)}}</div>
                    <div class="col"><b>{{ __('repair.balance')}}:</b> {{formatMoney($repair->grand_total - $repair->paid)}}</div>
               </div>
            </div>

            <div id="details" class="clearfix">
               <div id="client" contentEditable="true">
                    <h2 class="to">{{ __('repair.comments')}}:</h2>
                    <div class="col">{{ ($disclaimer) }}</div>
               </div>
            </div>


           
            <div class="clearfix"></div>
         </main>
         <div class="clearfix"></div>
      
      </div>
      
   </body>
   <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <!-- <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script> -->
</html>