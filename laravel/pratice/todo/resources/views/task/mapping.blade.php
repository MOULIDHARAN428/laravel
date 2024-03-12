@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 col-sm-8" style="padding-left:100px;">
        <div class="">
            <button type="button" class="btn btn-secondary btn-lg">
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

<script>
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
                        <p class="task-title">${$task.title}</p>
                    </div>
                    <div class="card-body">
                        <div class="container task">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong> ${$task.description}</p>
                                    <p><strong>Urgency:</strong> ${$task.urgency}</p>
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
                            <div class="col-md-2">
                                <p><strong>S.NO</strong></p>
                            </div>
                            <div class="col-md-2">
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
                        <div class="row" style="cursor: pointer;" onclick="getUserDetails(${$assigne.user_id})">
                            <div class="col-md-2">
                                <p><strong>${j}</strong></p>
                            </div>
                            <div class="col-md-2">
                                <img src="{{ asset('storage/profile/') }}/${$assigne.profile_picture}" alt="Profile Picture" class="rounded-circle" width="30" style="margin-left: 5px;">
                            </div>
                            <div class="col-md-2">
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
        })
        resp.fail(function(resp){
            html += "No Assignes for the task";
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
        getUserDetails(0);
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