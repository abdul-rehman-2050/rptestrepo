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
         <div class="clearfix"></div>
         <div id="mid">
            <div class="info">
               <h2></h2>
               <center>
                     <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($repair->code, "C128") }}" alt="barcode"   />
               </center>
               <h2>{{__('customer.name')}}: {{$repair->customer}}<br>{{__('repair.code')}}: {{$repair->code}}<br>{{__('repair.model')}}: {{$repair->model}}</h2>
               <div class="clearfix"></div>
            </div>
         </div>
         <div id="bot">
            <div id="table">
               <table>
                  <tr class="tabletitle">
                     <td class="item">
                        <h2>{{__('repair_item.product_service')}}</h2>
                     </td>
                     <td class="Hours">
                        <h2>{{__('repair_item.qty')}}</h2>
                     </td>
                     <td class="Rate price text-right">
                        <h2>{{__('repair_item.subtotal')}}</h2>
                     </td>
                  </tr>
                  
                  @if($repair->items)
                     @foreach ($repair->items as $item)
                        <tr class="service">
                           <td class="tableitem">
                              <p class="itemtext"> 
                                 <strong>{{ $item->product_name }}</strong>
                              </p>  
                           </td>
                           <td class="tableitem"><p class="itemtext">{{$item->quantity}}</p></td>
                           <td class="tableitem"><p class="itemtext text-right">{{formatMoney($item->subtotal, 2)}}</p></td>
                        </tr>
                     @endforeach
                  @else
                  <tr class="service">
                        <td class="tableitem">
                           <p class="itemtext"> 
                              <strong>{{ $repair->model}}</strong>
                              <small>
                                 {{ $repair->serial_number ? '('.$repair->serial_number.')' : ''}}
                                 <br>
                                 {{$repair->defect}}
                              </small>
                           </p>  
                        </td>
                        <td class="tableitem"><p class="itemtext">1</p></td>
                        <td class="tableitem"><p class="itemtext text-right">{{formatMoney($repair->total - $repair->service_charges, 2)}}</p></td>
                     </tr>
                  @endif

                  <tr class="service">
                     <td class="tableitem">
                        <p class="itemtext"> 
                           {{__('repair.service_charges')}}
                        </p>  
                     </td>
                     <td class="tableitem"><p class="itemtext">1</p></td>
                     <td class="tableitem"><p class="itemtext text-right">{{formatMoney($repair->service_charges, 2)}}</p></td>
                  </tr>

                  <tr class="tabletitle">
                     <td class="Rate" style="direction: rtl;" colspan="2">
                        {{__('tax.tax')}}
                     </td>
                     <td class="text-right payment">
                        {{ formatMoney($repair->tax, 2) }}
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td class="Rate" style="direction: rtl;" colspan="2">
                        {{__('repair.grand_total')}}
                     </td>
                     <td class="text-right payment">
                        {{ formatMoney($repair->grand_total, 2) }}
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td class="Rate" style="direction: rtl;" colspan="2">
                        {{__('repair.paid')}}
                     </td>
                     <td class="text-right payment">
                        {{ formatMoney($repair->paid, 2) }}
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td class="Rate" style="direction: rtl;" colspan="2">
                        {{__('repair.balance')}}
                     </td>
                     <td class="text-right payment">
                        {{ formatMoney($repair->grand_total - $repair->paid, 2) }}
                     </td>
                  </tr>
               </table>
               <small class="text-right">
               @if($repair->payments)
               @foreach ($repair->payments as $payment)
               {{ __('payment.paid_line', 
	               [
	               'type' => __($payment->paid_by),
	               'amount' => $payment->amount,
	               'date' => $payment->date
	               ]
               ) }}<br>
               @endforeach
               @endif
               </small>
            </div>
            <div id="legalcopy">
               <p class="legal">{{ ($disclaimer) }}</p>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
      
   </body>
   <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
</html>