<!Doctype html>
<html lang="en">
   <head>
      <!-- meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
      <link rel="preconnect" href="https://fonts.gstatic.com">
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">
      <title>Faktúra</title>
      <style>

#print_button {
    /* height 50px; */
    width: 50%;
    line-height: 50px;
    position: fixed;
    left: 25%;
    bottom: 0px;
    color: white;
    font-weight: bold;
    text-align: center;
    font-size: 17px;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    cursor: pointer;
    background-color: crimson;

}

#print_button:hover {
    background-color: #3A3A3A;

}

@media print {
    #print_button {display: none;}
}
         body {
         -webkit-print-color-adjust: exact;
         font-family: 'Poppins', sans-serif;
         }
         img {
         width: 100%;
         }
         .row {
         justify-content: space-between;
         align-items: center;
         margin: 0;
         padding: 0 15px;
         }
         header {
         padding: 0 0;
         }
         h2,
         h1 {
         margin: 0;
         }
         .inner-title {
         font-weight: bolder;
         }
         .add-block.border {
         border: 2px solid #e5e5e4 !important;
         border-radius: 20px;
         padding: 30px 40px;
         }
         .right .add-number,
         .right .add-loca {
         padding: 20px 40px 0;
         }
         .add-number,
         .add-loca {
         padding: 20px 0 0;
         }
         .col span {
         display: block;
         line-height: 1.3;
         }
         .col h5.invoice-name {
         margin: 16px 0 20px;
         }
         .col h5 {
         font-weight: 700;
         font-size: 19px;
         }
         .col.left .add-block {
         padding-top: 40px;
         }
         .add-number span {
         display: block;
         font-weight: 400;
         font-size: 17px;
         line-height: 1.3;
         }
         .add-number h5 {
         margin: 0;
         }
         .right .add-loca {
         display: block;
         }
         .add-detail {
         max-width: 44%;
         margin-left: 4px;
         }
         .add-loca {
         display: flex;
         }
         li {
         list-style: none;
         }
         ul {
         margin: 0;
         }
         .row.date-bg .col {
         padding: 30px 15px;
         }
         .row.date-bg {
         background-color: #e5e5e4;
         border-radius: 20px;
         /* margin: 20px 0; */
         /* padding-right: 30px; */
         }
         .row.date-bg .grand-total {
            background-color: #ccc;
            flex: 0 0 35%;
            padding: 30px 50px 30px 70px;
            position: relative;
            border-radius: 20px;
         }
         .col.right {
         flex: 0 0 55%;
         }
         .row.date-bg .grand-total:before {
           
         }
         .row.border {
         margin: 20px 0;
         border: 2px solid #e5e5e4 !important;
         border-radius: 15px;
         padding: 13px 10px;
         }
        
         b,
         strong {
         color: #231f20;
         }
        
         .footer {
         /* border: 2px solid #e5e5e4; */
         text-align: left;
         margin: 60px 0 16px;
         border-radius: 16px;
         font-size:10px;
         padding: 6px 0;
         }
         .footer p {
         margin: 0;
         text-align: center;
         }
         .col.total-ammount {
            background-color: #f0efef;
            border-radius: 15px;
            padding: 25px 25px;
            max-width: 47%;
         }
         .col span b,
         
         .left-block,
         .right-block {
         display: flex;
         justify-content: space-between;
         }
         .left-block p {
         margin: 0px;
         }
         .right-block span,
         .right-block span b {
         font-size: 19px;
         }
         .right-block {
         margin-top: 15px;
         }
         .right-block span:last-child,
         .left-block span:last-child {
         max-width: 40%;
         flex: 0 0 40%;
         }
         .right-block span:first-child,
         .left-block span:first-child {
         max-width: 60%;
         flex: 0 0 60%;
         }
         .row.authorize {
         justify-content: flex-end;
         padding: 30px 0;
         }
         .row.authorize span {
         margin-bottom: 25px;
         }
         .row.authorize .col {
         max-width: 30%;
         flex: 0 0 30%;
         text-align: center;
         font-size: 14px;
         font-weight: 700;
         }
         .row.authorize .col p {
         margin: 0;
         line-height: 1.3;
         }
         .action-btn {
         text-align: end;
         }
         .pb-custom{
         page-break-before: auto; /* 'always,' 'avoid,' 'left,' 'inherit,' or 'right' */
         page-break-after: auto; /* 'always,' 'avoid,' 'left,' 'inherit,' or 'right' */
         page-break-inside: avoid; /* or 'auto' */
         }
         @media  print {
            .action-btn {
                display: none;
            }
            footer.container {
                padding-top:1mm; 
            }
           
            main {
                overflow:hidden;
            }
         }
         @page {
         margin: 0mm;
         }

         @media  print {
            .container {
                width: 100% !important;
            }
            body { 
                margin: 15mm !important; 
            }
        }

   <?php 
   $user_agent = @$_SERVER['HTTP_USER_AGENT']; 
   if (stripos( $user_agent, 'Chrome') !== false) {
      ?>
         @media  print {

   .footer {
                position: absolute;
                bottom:0;
                margin-top:1mm; 
               }
            }

      <?php
   }elseif (stripos( $user_agent, 'Safari') !== false) { ?>
      
      * {
         margin:0 !important;
         
      }
      [class^=pt] {padding: 0 !important;}
      [class^=pb] {padding: 0 !important;}
      
      .row.date-bg .col {
      padding: 15px 15px;
      }

      .col.total-ammount {
            padding: 10px 10px;
            max-width: 47%;
         }
         .row.border {
            padding: 5px;
         }

         .add-block.border {
            padding: 5px 14px;
         }
   <?php }?>

         .report-header {
            padding: 4px 20px;
            border: 1px solid black;
            width: 400px;
            font-size: 30px;
            text-align: center;
         }
      </style>
   </head>
   <body>
      <div class="" id="boxes" >
         <main>

            <div class="row justify-content-between mt-4 mb-1">
               <div class="col-7">
                  <div class="report-header">
                     <p class="mb-0 pb-0">Töötellimus <strong>{{$repair->code}}</strong></p>
                  </div>

                  <div class="mt-4" style="font-size:15px;">
                     @if($repair->customerData)
                     <strong>{{__('customer.customer')}}</strong>: {{$repair->customerData->name}}<br>
                     <strong>{{__('customer.phone')}}</strong>: {{$repair->customerData->phone}}<br>
                     @else
                     <strong>{{__('customer.customer')}}</strong>: {{$repair->name}}<br>
                     <strong>{{__('customer.phone')}}</strong>: {{$repair->phone}}<br>
                     @endif
                     <strong>{{__('repair.today_date')}}</strong>: {{date('d.m.Y')}}<br>
                     <strong>{{__('repair.expected_close_date')}}</strong>: {{$repair->expected_close_date ? date('d.m.Y', strtotime($repair->expected_close_date)) : ''}}<br>
                     <strong>{{__('repair.device')}}</strong>: {{$repair->category}} {{$repair->manufacturer}} {{$repair->model}}<br>
                     <strong>{{__('repair.serial_number')}}</strong>: {{$repair->serial_number}}<br>

                     <div class="row justify-content-between my-0 mx-0 px-0">
                        @foreach($fields as $field)
                           <div class="col-6 px-0">
                              <strong>{{$field['name']}}:</strong> {{$field['value']}}<br>
                           </div>
                        @endforeach
                     </div>

                     
                     
                     <strong>{{__('repair.defect')}}</strong>: {{$repair->defect}}<br>
                  </div>

                  <div class="mt-4" style="font-size:15px;font-weight:300;">
                  {{ ($disclaimer) }}

                  <br>
                  <br>
                  <br>
                  <br>

                  Võttis vastu: <div style="display:inline-block;width: 250px;border-bottom: 1px solid black;"></div>
                  </div>

               </div>
               <div class="col-4">
                  <a href="#">
                     <img style="float:left;width: 250px" src="{{ asset(config('config.main_logo')) }}" class="img">
                  </a>
                  <br>
                  <h3 class="mt-4">TEHNIKAEKSPERT OÜ</h3>
                  <h4>E-R 10-18 ja L 10-15</h4>
                  <br>
                  <p>
                     Registrikood: 10627154<br>
                     KMKR nr: EE100622168 <br>
                     Aadress: Kesk tn 12 Elva, 61504 <br>
                     Telefon: +372 568 600 40 <br>
                     E-post: info@tehnikaekspert.ee<br>
                  </p>
                  <div class="d-flex flex-row">
                     <div class="pt-2">
                        <img  src="data:image/png;base64,{{DNS1D::getBarcodePNG($repair->code, 'C39')}}" alt="barcode"   />
                     </div>

                  </div>
                  
                  <div>

                     <div class="d-flex flex-row">
                        <div class="pt-2">{!! QrCode::size(100)->margin(0)->generate($repair->code !== '' ? URL::to('/check-status?code='.$repair->code) : "-"); !!}</div>
                        <div class="pt-2">Kontrolli oma remondi staatust siit: <strong>remont.tehnikaekspert.ee</strong></div>
                     </div>
                  </div>
               </div>
            
            
            </div>
            <div class="row justify-content-between mt-4 mb-1">
            <div class="col-12">
               <div style="border-bottom: 1px dashed black;"></div>
            </div>
            </div>

            <div class="row justify-content-between mt-4 mb-1">
               <div class="col-7">
                  <div class="report-header">
                     <p class="mb-0 pb-0">Töötellimus <strong>{{$repair->code}}</strong></p>
                  </div>

                  <div class="mt-4" style="font-size:15px;">
                     @if($repair->customerData)
                     <strong>{{__('customer.customer')}}</strong>: {{$repair->customerData->name}}<br>
                     <strong>{{__('customer.phone')}}</strong>: {{$repair->customerData->phone}}<br>
                     @else
                     <strong>{{__('customer.customer')}}</strong>: {{$repair->name}}<br>
                     <strong>{{__('customer.phone')}}</strong>: {{$repair->phone}}<br>
                     @endif

                     <strong>{{__('repair.today_date')}}</strong>: {{date('d.m.Y')}}<br>
                     <strong>{{__('repair.expected_close_date')}}</strong>: {{$repair->expected_close_date ? date('d.m.Y', strtotime($repair->expected_close_date)) : ''}}<br>
                     <strong>{{__('repair.device')}}</strong>: {{$repair->category}} {{$repair->manufacturer}} {{$repair->model}}<br>
                     <strong>{{__('repair.serial_number')}}</strong>: {{$repair->serial_number}}<br>

                     <div class="row justify-content-between my-0 mx-0 px-0">
                        @foreach($fields as $field)
                           <div class="col-6 px-0">
                              <strong>{{$field['name']}}:</strong> {{$field['value']}}<br>
                           </div>
                        @endforeach
                     </div>

                     <strong>{{__('repair.pin')}}</strong>: {{$repair->pin}}<br>
                     
                     <strong>{{__('repair.defect')}}</strong>: {{$repair->defect}}<br>
                  </div>

                  <div class="mt-4" style="font-size:15px;font-weight:bolder;">
                  <strong style="font-size:16px;font-weight:bolder;">Remondi- ja maksetingimustega nõus</strong><br><br><br>

                  Kliendi allkiri: <div style="display:inline-block;width: 250px;border-bottom: 1px solid black;"></div>
                  </div>

               </div>
               <div class="col-4">
                  <a href="#">
                     <img style="float:left;width: 250px" src="{{ asset(config('config.main_logo')) }}" class="img">
                  </a>
                  <br>
                  <h3 class="mt-4">TEHNIKAEKSPERT OÜ</h3>
                  <h4>E-R 10-18 ja L 10-15</h4>
                  <br>
                  <p>
                     Registrikood: 10627154<br>
                     KMKR nr: EE100622168 <br>
                     Aadress: Kesk tn 12 Elva, 61504 <br>
                     Telefon: +372 568 600 40 <br>
                     E-post: info@tehnikaekspert.ee<br>
                  </p>
                  <div>
                  
                     <img  src="data:image/png;base64,{{DNS1D::getBarcodePNG($repair->code, 'C39')}}" alt="barcode"   />

                  </div>
                  
                  <div>
                  <div class="d-flex flex-row">
                        <div class="pt-2">{!! QrCode::size(100)->margin(0)->generate($repair->code !== '' ? URL::to('/check-status?code='.$repair->code) : "-"); !!}</div>
                        <div class="pt-2">Kontrolli oma remondi staatust siit: <strong>remont.tehnikaekspert.ee</strong></div>
                     </div>
                  </div>
               </div>
            </div>

            

         </main>
         <footer class="row">
            <div class="footer">
            </div>
         </footer>

      </div>
      <!-- <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script> -->

   </body>
</html>