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