@extends('ic.layouts.app', ['nav' => false])
@section('title', 'Welcome - Installation')

@section('content')
<div class="container">
    <div class="row">
      <h3 class="text-center">{{ config('app.name', 'POS') }} Installation <small>Step 3 of 3</small></h3>
    </div>

    <div class="row">
      <div class="card">
        @include('ic.partials.nav', ['active' => 'success'])
        <div class="card-body">
          <!-- /.box-header -->
          <h1 class="card-title">{{ config('app.name') }}</h1>

          <h3 class="text-success card-title">Great!, Your application is succesfully installed.</h3>
          <p><br><b>Username:</b> admin@admin.com<br/> <b>Password:</b> password</p>
          <p><br>Login link <a href="{{route('home')}}" target="_blank">here</a></p>
        </div>
      </div>
    </div>
</div>
@endsection