@extends('layouts.app')

<form>

    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input name = "password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
    </div>

    <div class="form-group">
        <label for="exampleInputPassword1">Re-enter Password</label>
        <input name = "repassword" type="password" class="form-control" id="exampleInputPassword1" placeholder="Re-Enter Password" required>
    </div>

    <button type="submit" class="btn btn-primary">Reset</button>

  </form>