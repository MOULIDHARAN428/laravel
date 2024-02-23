@extends('layouts.app')

<h4>Task Assigned</h4>
<p>Dear {{ $user['name'] }},</p>
<p>You task has been edited:</p>
<p><strong>Title:</strong> {{ $task['title'] }}</p> 
<p><strong>Task Id:</strong> {{ $map['id'] }}</p> 
<p><strong>Role:</strong> {{ $map['role'] }}</p> 
<p><strong>Assigned At:</strong> {{ $map['assigned_at'] }}</p> 
<p><strong>Due Time:</strong> {{ $task['due_time'] }}</p>


{{-- name, email, title, due_time, $map['id'],role, assigned_at --}}
{{-- @if(isset($value))
    ---
@else
    ---
@endif --}}