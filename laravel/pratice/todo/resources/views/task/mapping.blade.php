@extends('layouts.app')

@section('content')
<div id="message">
    {{-- <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" style="background-color: rgb(100, 195, 100);">
                <div class="card-body" style="align-self:center;">
                    This is some text within a card body.
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="row justify-content-center mt-3">
        <div class="col-md-6">
            <div class="card" style="background-color: rgb(185, 107, 107);">
                <div class="card-body" style="align-self: center;">
                    This is some text within a card body.
                </div>
            </div>
        </div>
    </div> --}}

</div>


<div class="row">
    <div class="col-12 col-sm-8" style="padding-left:100px;">
        <div class="">
            <button type="button" class="btn btn-secondary btn-lg" data-toggle="modal" data-target="#createAssignModal">
                Assign new user <i class="fa fa-plus" style="margin-left: 5px;"></i>
            </button>
        </div>            
        <div id="task"> </div>
    </div>
    <div class="col-12 col-sm-4" style="padding-right:50px;">
        <div class="card-separator" style="padding-top:40px;"></div>
        <div id="user-details"> </div>
    </div>
</div>


{{-- 
    task_id -> int : should not enter
    user_id -> int
    role -> textbox
    assigned_at ->date-time

--}}

<div class="modal fade bd-example-modal-lg" id="createAssignModal" tabindex="-1" role="dialog" aria-labelledby="createAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssignModalLabel">Assign Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="created-successfully" style="color : green; text-align : center;"></div>
                <form >
                    @csrf
                    
                    <div class="form-group row">
                        <label for="inputUserID" class="col-sm-2 col-form-label">User ID</label>
                        <div class="col-sm-10">
                          <input type="number" class="form-control" id="user_id" placeholder="Tasks' User ID">
                          <div style="color: red" id="user_id_error"> </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="inputRole" class="col-sm-2 col-form-label">Role</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="role" placeholder="Task Role" rows="2" required></textarea>
                          <div style="color: red" id="role_error"> </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputAssignTime" class="col-sm-2 col-form-label">Assign Time</label>
                        <div class="col-sm-10">
                          <input type="datetime-local" class="form-control" id="assigned_at" placeholder="Task Due Time" required></input>
                          <div style="color: red" id="assigned_at_error"> </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="assignTask()">Create</button>
            </div>
        </div>$("input[name='urgency']:checked").val()
    </div>
</div>

<script>
    function editTask(taskID){

    }

    function editAssignesDetail(){

    }

    function deleteTask(taskID){
        console.log(taskID);
        if (window.confirm("Are you sure you want to delete this task?")) {
            let resp = $.ajax({
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/tasks/'+taskID
            });
            resp.done(function(resp){
                html = `<div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card" style="background-color: rgb(100, 195, 100);">
                                    <div class="card-body" style="align-self:center;">
                                        Deleted Successfully!
                                    </div>
                                </div>
                            </div>
                        </div>`;
                document.getElementById("message").innerHTML = html;
                var taskId = {{ request()->route('task_id') }};
                getTask(taskId);
                setTimeout(function() {
                    document.getElementById("message").innerHTML = "";
                }, 5000);
            });
            resp.fail(function(resp){
                console.log(resp);
                html = `<div class="row justify-content-center mt-3">
                            <div class="col-md-4">
                                <div class="card" style="background-color: rgb(185, 107, 107);">
                                    <div class="card-body" style="align-self: center;">
                                        Couldn't able to delete the task, contact admin!
                                    </div>
                                </div>
                            </div>
                        </div>`;
                document.getElementById("message").innerHTML = html;

                setTimeout(function() {
                    document.getElementById("message").innerHTML = "";
                }, 5000);
            });
        }
        else{
            html = `<div class="row justify-content-center mt-3">
                        <div class="col-md-4">
                            <div class="card" style="background-color: rgb(100, 195, 100);">
                                <div class="card-body" style="align-self: center;">
                                    Canceled successfully!
                                </div>
                            </div>
                        </div>
                    </div>`;
            document.getElementById("message").innerHTML = html;

            setTimeout(function() {
                document.getElementById("message").innerHTML = "";
            }, 5000);
        }
    }

    function deleteAssigne(assigneID){
        if (window.confirm("Are you sure you want to delete this assigne?")) {
            let resp = $.ajax({
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/tasks/mapping/'+assigneID
            });
            resp.done(function(resp){
                html = `<div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card" style="background-color: rgb(100, 195, 100);">
                                    <div class="card-body" style="align-self:center;">
                                        Deleted Successfully!
                                    </div>
                                </div>
                            </div>
                        </div>`;
                document.getElementById("message").innerHTML = html;

                var taskId = {{ request()->route('task_id') }};
                getTask(taskId);
                
                setTimeout(function() {
                    document.getElementById("message").innerHTML = "";
                }, 5000);
            });
            resp.fail(function(resp){
                console.log(resp);
                html = `<div class="row justify-content-center mt-3">
                            <div class="col-md-4">
                                <div class="card" style="background-color: rgb(185, 107, 107);">
                                    <div class="card-body" style="align-self: center;">
                                        Couldn't able to delete the task, contact admin!
                                    </div>
                                </div>
                            </div>
                        </div>`;
                document.getElementById("message").innerHTML = html;

                setTimeout(function() {
                    document.getElementById("message").innerHTML = "";
                }, 5000);
            });
        }
        else{
            html = `<div class="row justify-content-center mt-3">
                        <div class="col-md-4">
                            <div class="card" style="background-color: rgb(100, 195, 100);">
                                <div class="card-body" style="align-self: center;">
                                    Canceled successfully!
                                </div>
                            </div>
                        </div>
                    </div>`;
            document.getElementById("message").innerHTML = html;

            setTimeout(function() {
                document.getElementById("message").innerHTML = "";
            }, 5000);
        }
    }

    function assignTask(){
        document.getElementById("user_id_error").innerHTML = "";
        document.getElementById("role_error").innerHTML = "";
        document.getElementById("assigned_at_error").innerHTML = "";
        
        var assigned_at = $('#assigned_at').val();

        var data = {
            task_id: {{ request()->route('task_id') }},
            user_id: $('#user_id').val(),
            role: $('#role').val(),
        };
        if(assigned_at){
            var selectedDate = new Date(assigned_at);
            var formattedDateTime =
                selectedDate.getFullYear() + '-' +
                ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
                ('0' + selectedDate.getDate()).slice(-2) + ' ' +
                ('0' + selectedDate.getHours()).slice(-2) + ':' +
                ('0' + selectedDate.getMinutes()).slice(-2) + ':' +
                ('0' + selectedDate.getSeconds()).slice(-2);
            data.assigned_at = formattedDateTime;
        }
        let resp = $.ajax({
            type: 'POST',
            url: '/tasks/mapping',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:data,
        });
        resp.done(function(resp){
            document.getElementById("created-successfully").innerHTML = "<h5>Created Successfully!</h5>";
            let task_id = {{ request()->route('task_id') }};
            getTask(task_id);
        });
        resp.fail(function(resp){
            // console.log(resp);
            if(resp.responseJSON.message==="Unauthenticated."){
                var baseUrl = window.location.origin;
                window.location.href = baseUrl + "/login";
            }
            // console.log(resp);
            var response = JSON.parse(resp.responseText);
            var errors = response.validation_errors;

            for (var field in errors) {
                var errorMessage = errors[field][0];
                document.getElementById(field+"_error").innerHTML = "<div>"+errorMessage+"</div>";
            }

        });
    }
    function getTask(taskID){
        let resp = $.ajax({
            type: 'GET',
            url: '/task_with_user/'+taskID
        });
        resp.done(function(resp){
            $task = resp.task;
            html = `
                <div class="card-separator" style="padding-top:40px;"></div>
                <div class='card'>
                    <div class="card-header">
                        <div class = "row">
                            <div class="col-12 col-sm-9">
                                <p class="task-title">${$task.title}</p>
                            </div>
                            <div class="col-12 col-sm-3">
                                <button type="button" class="btn btn-secondary btn-sm">Edit <i class="fa fa-pencil-square-o" style="margin-left: 5px;"></i> </button>
                                <button type="button" class="btn btn-danger btn-sm" onClick="deleteTask(${$task.id})">Delete <i class="fa fa-trash" style="margin-left: 5px;"></i> </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container task">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong> ${$task.description}</p>
                                    <p><strong>Urgency:</strong>
                                        ${
                                            $task.urgency == 1
                                            ? '<span class="badge badge-danger">Urgent</span>'
                                            : '<span class="badge badge-success">Not Urgent</span>'
                                        }  
                                    </p>
                                    <p><strong>Status:</strong>
                                        ${
                                            $task.status == 0
                                            ? '<span class="badge badge-danger">Not Completed</span>'
                                            : '<span class="badge badge-success">Completed</span>'
                                        }    
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Time Completed:</strong> ${
                                        $task.time_completed ?  $task.time_completed : "Not Completed"
                                    }</p>
                                    <p><strong>Due Time:</strong> ${$task.due_time}</p>
                                </div>
                            </div>
                        </div>`;
            
            //user : user dp, user name, status, assigned-at
            $assigne = $task.assignes;
            if($assigne.length!=0){
                html+=`
                    <div class="container user">
                        <h5>Users</h5> 
                        <div class="row">
                            <div class="col-md-1">
                                <p><strong>S.NO</strong></p>
                            </div>
                            <div class="col-md-1">
                                <p><strong>Profile</strong></p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Name</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Role</strong></p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Status</strong></p>
                            </div>
                        </div>`;
                let j = 1; 
                for(let assignesID in $task.assignes){
                    $assigne = $task.assignes[assignesID];
                    html+=`
                        <div class="row">
                            <div class="col-md-1">
                                <p><strong>${j}</strong></p>
                            </div>
                            <div class="col-md-1" style="cursor: pointer;" onclick="getUserDetails(${$assigne.user_id})">
                                <img src="{{ asset('storage/profile/') }}/${$assigne.profile_picture}" alt="Profile Picture" class="rounded-circle" width="30" style="margin-left: 5px;">
                            </div>
                            <div class="col-md-2" style="cursor: pointer;" onclick="getUserDetails(${$assigne.user_id})">
                                <p><strong>${$assigne.name}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>${$assigne.role}</strong></p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>
                                    ${
                                        $assigne.status == 0
                                            ? '<span class="badge badge-danger">Not Completed</span>'
                                            : '<span class="badge badge-success">Completed</span>'
                                    }
                                </strong></p>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-secondary btn-sm"> <i class="fa fa-pencil-square-o" style="margin-left: 5px; opacity: 0.8;"></i> </button>
                                <button type="button" class="btn btn-danger btn-sm" onClick="deleteAssigne(${$assigne.id})"> <i class="fa fa-trash" style="margin-left: 5px; opacity: 0.8;"></i> </button>
                            </div>
                        </div>`;

                    j++;
                }
                html+=`
                        </div>
                    `
            }
            else{
                html += `<div class="text-center"><h4>No assigned users!</h4></div>`;
            }

            // two unfinishing div
            html +="</div></div>";
            document.getElementById("task").innerHTML = html;
            getUserDetails(0);
        })
        resp.fail(function(resp){
            html = "<div style='padding-top:30px;'><h3>Task doesn't exist</h3></div>";
            document.getElementById("task").innerHTML = html;
        })
    }

    function getUserDetails(user_id){
        if(user_id==0){
            let error_html = `<div style="padding-top: 44px;"><h4></h4></div>`;
            document.getElementById("user-details").innerHTML = error_html;
            return;
        }
        let resp = $.ajax({
            type: 'GET',
            url: '/tasks/mapping/'+user_id
        });
        resp.done(function(resp){
            $user = resp.user[0];

            html = "";

            html += `
            <div class="row">
                <div class="col-md-2">
                    <img src="{{ asset('storage/profile/') }}/${$user.profile_picture}" alt="Profile Picture" class="rounded-circle" width="50" style="margin-left: 5px;">
                </div>
                <div class="col-md-2" style="padding-top:10px;">
                    <h3>${$user.name}</h3>
                </div>
            </div>
            <div style="padding-bottom:30px;"></div>
            `;
            
            html += '<div class="user-tasks-container" style="max-height: 650px; overflow-y: auto;">';

            for(let task in resp.task){
                if(task==="user") break;
                $task = resp.task[task];
                
                // console.log($task);
                html += `
                <div class='card'>
                    <div class="card-header">
                        <p class="task-title">${$task['task_title']}</p>
                    </div>
                    <div class="card-body">
                        <div class="container task">
                            <p><strong>Description:</strong> ${$task['role']}</p>
                            <p><strong>Due Time:</strong> ${$task['assigned_at']}</p>
                            <div class="row">
                                <div class="col-md-5">
                                    <p><strong>Status:</strong>
                                        ${
                                            $task['status'] == 0
                                            ? '<span class="badge badge-danger">Not Completed</span>'
                                            : '<span class="badge badge-success">Completed</span>'
                                        }    
                                    </p>
                                </div>
                                <div class="col-md-7">
                                    <p><strong>Time Completed:</strong> ${
                                        $task['time_completed'] ?  $task['time_completed'] : "Not Completed"
                                    }</p>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="padding-top:10px;"></div>`;

            }

            html += '</div>';

            if(html === "") {
                html += "No task assigned for the user!";
            }
            document.getElementById("user-details").innerHTML = html;
        })
        resp.fail(function(resp) {
            let error_html = `<div style="padding-top: 44px;"><h4>No task assigned for the user!</h4></div>`;
            document.getElementById("user-details").innerHTML = error_html;
        });

    }

    $(document).ready(function() {
        var taskId = {{ request()->route('task_id') }};
        getTask(taskId);
    });

</script>

{{-- 
assigned_at: "18 days ago"
id: 10
role: "asd"
status: 0
task_id: 15
time_completed: "N/A"
user_id: 3 

Task Title

role
status
time_completed



--}}
@endsection