@extends('layouts.app')

<form>
    <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" required>
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
    <button type="submit" class="btn btn-link">Forgot Password</button>
</form>