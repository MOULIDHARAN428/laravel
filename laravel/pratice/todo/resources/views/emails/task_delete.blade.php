@extends('layouts.app')

<h4>Task Assigned</h4>
<p>Dear {{ $user['name'] }},</p>
<p>You task has been deleted!</p>
<p><strong>Title:</strong> {{ $task['title'] }}</p> 
<p><strong>Task Id:</strong> {{ $map['id'] }}</p> 
<p><strong>Role:</strong> {{ $map['role'] }}</p>
