@extends('layouts.app')

<form>
    
    <div class="form-group">
      <label for="exampleInputName">Full Name</label>
      <input name = "name" type="name" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Enter name" required>
    </div>

    <div class="form-group">
        <label for="exampleInputName">Email</label>
        <input name = "email" type="email" class="form-control" id="email" aria-describedby="nameEmail" placeholder="Enter email" required>
    </div>

    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input name = "password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
    </div>

    <div class="form-group">
        <label for="exampleInputPassword1">Re-enter Password</label>
        <input name = "repassword" type="password" class="form-control" id="exampleInputPassword1" placeholder="Re-Enter Password" required>
    </div>

    <div class="form-group">
        <label for="exampleInputPassword1">Profile Picture</label>
        <input name = "profile" type="file" class="form-control" id="exampleInputFile" placeholder="Profile Picture">
    </div>

    <button type="submit" class="btn btn-primary">Register</button>

</form>

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Register</div>

                    <div class="card-body">
                        <form id="registrationForm" method="POST" action="">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    <span class="invalid-feedback" role="alert" id="email-error">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password">
                                    <span class="invalid-feedback" role="alert" id="password-error">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        $(document).ready(function () {
            $('#registrationForm').submit(function (e) {
                e.preventDefault();
                
                var form = $(this);
                var url = form.attr('action');
                
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    success: function (data) {
                        // Handle successful registration here
                        console.log(data);
                    },
                    error: function (xhr, status, error) {
                        var errors = xhr.responseJSON.errors;
                        
                        // Display errors in the form
                        displayErrors(errors);
                    }
                });
            });

            function displayErrors(errors) {
                // Clear previous error messages
                $('.invalid-feedback strong').text('');
                
                // Display new error messages
                $.each(errors, function (key, value) {
                    $('#' + key + '-error strong').text(value[0]);
                });
            }
        });
    </script>
@endsection