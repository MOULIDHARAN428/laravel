@extends('layouts.app')

@section('content')

@php
    $bearerToken = session('bearer_token');
@endphp

<form>
    @csrf
    <div id="error" class="alert-messgae"> </div>
    <div class="form-group row formLabel" style="padding-top:50px;">
        <div class="col-sm-2"></div>
        <label for="email" class="col-sm-1 col-form-label">Email</label>
        <div class="col-sm-5">
        <input type="email" class="form-control" id="email" placeholder="Email">
      </div>
    </div>

    <div class="form-group row formLabel" style="padding-top:30px;">
        <div class="col-sm-2"></div>
      <label for="password" class="col-sm-1 col-form-label">Password</label>
      <div class="col-sm-5">
        <input type="password" class="form-control" id="password" placeholder="Password">
      </div>
    </div>

    <div class="form-group row formLabel" style="padding-top:40px;">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <button type="submit" class="btn btn-primary" onclick="registerUser()">Sign in</button>
            <button type="submit" class="btn btn-link">Forgot Password</button>
        </div>
    </div>
</form>

<script>
  function registerUser(){
    event.preventDefault();
    var email = $('#email').val();
    var password = $('#password').val();
    let resp = $.ajax({
        type: 'POST',
        url: '/api/login',
        data: {
            email: email,
            password: password
        }
    });
    console.log(resp);
    resp.done(function(resp){
      console.log("logged in!")
    });

    resp.fail(function(data){
          document.getElementById("error").innerHTML = `
          <div class="alert alert-danger alert-content" role="alert">`
            +resp['responseJSON']['error']+
          `</div>`;
    });
    
  }
</script>


@endsection

