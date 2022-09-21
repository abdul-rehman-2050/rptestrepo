@extends('ic.layouts.app', ['nav' => false])
@section('title', 'Welcome - Installation')

@section('content')
<div class="container">
    <div class="row">
      <h3 class="text-center">{{ config('app.name', 'POS') }} Installation <small>Step 2 of 3</small></h3>
    </div>

    <div class="row">
      <div class="card">
        @include('ic.partials.nav', ['active' => 'app_details'])
        <div class="card-body">
          <form class="form" method="post" 
            action="{{route('install.installAlternate')}}"
            id="env_details_form">
                  {{ csrf_field() }}

                  <h4 class="install_instuction">Hey, I need your help. </h4>
                  <p class="install_instuction">
                    Please create a file with name <code>.env</code> in application folder with <code>read & write permission</code> and paste the below content. <br/> Press install after it.
                  </p>
                  <hr/>

                  <div class="col-md-12">
                    <div class="form-group">
                        <textarea rows="25" cols="50">{{$envContent}}</textarea>
                    </div>
                  </div>
                  
                  <div class="col-md-12">
                    <button type="submit" class="btn btn-primary float-md-right" id="install_button">Install</button>
                  </div>

                  <div class="col-md-12 text-center text-danger install_msg d-none">
                    <h3>Installation in progress, Please do not refresh, go back or close the browser.</strong>
                  </h3>
              </form>
        </div>
      </div>
    </div>
</div>
@endsection

@section('footer')
  <script type="text/javascript">
    $(document).ready(function(){

      $('form#env_details_form').submit(function(){
        $('button#install_button').attr('disabled', true).text('Installing...');
        $(".install_instuction").addClass('d-none');
        $('div.install_msg').removeClass('d-none');
        $('textarea').addClass('d-none');
        $('.back_button').d-none();
      });

    })
  </script>
@endsection