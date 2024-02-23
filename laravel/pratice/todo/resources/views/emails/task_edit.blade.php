@extends('layouts.app')
 
@section('title', 'Page Title')
 
@section('sidebar')
    @@parent
 
    <p>This is appended to the master sidebar.</p>
@endsection
 
@section('content')
    <p>This is my body content.</p>
@endsection

<h2>Task Assigned</h2>
<p>Dear {{ $task->user->name }},</p>
<p>You have been assigned a new task:</p>
<p><strong>Title:</strong> {{ $task->title }}</p>
<p><strong>Description:</strong> {{ $task->description }}</p>