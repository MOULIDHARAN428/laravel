@extends('layouts.app')

@section('content')

<form>
    @csrf

    <div id="error" class="alert-messgae"> </div>
    
    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
        <label for="email" class="col-sm-1 col-form-label">Full Name</label>
        <div class="col-sm-5">
          <input type="name" class="form-control" id="name" placeholder="Full Name" name="name">
        </div>
    </div>

    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
        <label for="email" class="col-sm-1 col-form-label">Email</label>
        <div class="col-sm-5">
          <input type="email" class="form-control" id="email" placeholder="Email" name="email">
        </div>
    </div>

    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
        <label for="password" class="col-sm-1 col-form-label">Password</label>
        <div class="col-sm-5">
          <input type="password" class="form-control" id="password" placeholder="Password" name="password">
        </div>
    </div>

    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
        <label for="profile_picture" class="col-sm-1 col-form-label">Profile Picture</label>
        <div class="col-sm-5">
          <input type="file" class="form-control" id="profile_picture" placeholder="Profile Picture" name="profile_picture" accept="image/*">
        </div>
    </div>


    <div class="form-group row formLabel" style="padding-top:40px;">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
              <button type="submit" class="btn btn-primary" onclick="registerUser()">Sign in</button><br>
              <div style="padding-top:10px;">
                <a href="/forgot-password" class="btn btn-primary">Forgot Password</a>
                Already have account? 
              </div>
            </div>
        </div>
    </div>

</form>

<script>
  function registerUser(){
    var name = $('#name').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var file = $('#profile_picture');
    let resp = $.ajax({
        type: 'POST',
        url: '/api/register',
        data: {
            name, name,
            email: email,
            password: password,
            profile_picture: file
        }
    });

    resp.done(function(resp){
      console.log(resp);
    });

    resp.fail(function(data){
          document.getElementById("error").innerHTML = `
          <div class="alert alert-danger alert-content" role="alert">`
            +data['responseJSON']['error']+
          `</div>`;
    });
    
    event.preventDefault();
  }
</script>

@endsection
