@extends('ic.layouts.app', ['nav' => false])
@section('title', 'Welcome - Installation')

@section('content')
<div class="container">
    <div class="row">
      <h3 class="text-center">{{ config('app.name', 'RMSv4') }} Installation <small>Step 1 of 3</small></h3>
    </div>

    <div class="row">
      <div class="card">
        @include('ic.partials.nav', ['active' => 'install'])
        <div class="card-body">
          <h3 class="text-success">
                Welcome to RMSv4 Installation!
              </h3>
              <p><strong class="text-danger">[IMPORTANT]</strong> Before you start installing make sure you have following information ready with you:</p>

              <ol>
                <li>
                  <b>Application Name</b> - Something short & Meaningful.
                </li>
                <li>
                  <b>Database informations:</b>
                  <ul>
                    <li>Username</li>
                    <li>Password</li>
                    <li>Database Name</li>
                    <li>Database Host</li>
                  </ul>
                </li>
                <li>
                  <b>Envato or Codecanyon Details:</b>
                  <ul>
                    <li><b>Envato purchase code.</b> (<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Where Is My Purchase Code?</a>)</li>
                    <li>
                      <b>Envato Username.</b> (Your envato username)
                    </li>
                  </ul>
                </li>
              </ol>
              
              <a href="{{route('install.details')}}" class="btn btn-danger float-right">I Agree, Let's Go!</a>
        </div>
      </div>
    </div>
</div>
@endsection
