@extends('layouts.app')

<form>
  @csrf

    <div id="error" class="alert-messgae"> </div>

    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
      <label for="password" class="col-sm-1 col-form-label">Email</label>
      <div class="col-sm-5">
        <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
      </div>
    </div>

    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
      <label for="password" class="col-sm-1 col-form-label">Password</label>
      <div class="col-sm-5">
        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
      </div>
    </div>

    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
      <label for="re_enter_password" class="col-sm-1 col-form-label">Re-enter Password</label>
      <div class="col-sm-5">
        <input type="password" class="form-control" id="re_enter_password" placeholder="Retype Password" name="re_enter_password" required>
      </div>
    </div>

    <div class="form-group row formLabel" style="padding-top:20px;">
        <div class="col-sm-4"></div>
        <div class="col-sm-6">
            <div style="padding-top:10px;">
                <button type="button" class="btn btn-primary" onclick="resetPassword()">Reset</button>
            </div>
        </div>
    </div>

  </form>

  <script>
    function resetPassword(){
        var email = $('#email').val();
        var password = $('#password').val();
        var reEnterPassword = $('#re_enter_password').val();
        if(password!=reEnterPassword){
          document.getElementById("error").innerHTML = `
                <div class="alert alert-danger alert-content" role="alert">
                    Passwords do not match
                </div>`;
        }
        else{
          let resp = $.ajax({
              type: 'POST',
              url: '/api/reset-password',
              data: {
                  email: email,
                  password: password
              }
          });
          resp.done(function(resp){
            console.log(resp);
          });
          resp.fail(function(data){
                document.getElementById("error").innerHTML = `
                <div class="alert alert-danger alert-content" role="alert">
                  Unauthroized request, try the forgot password to get personalized link
                </div>`;
          });

        }
    }
  </script>