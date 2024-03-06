@extends('layouts.app')

<form>
    <div class="form-group row" style="padding-top:30px;">
        <div class="col-sm-2"></div>
        <label for="email" class="col-sm-1 col-form-label">Email</label>
        <div class="col-sm-5">
        <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
      </div>
    </div>

    <div class="form-group row formLabel" style="padding-top:30px;">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <button type="submit" class="btn btn-primary">Reset Password</button> <br>
        </div>
    </div>
</form>