@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 col-sm-1"></div>
    <div class="col-12 col-sm-2 user_registered" id="user">

    </div>
    <div class="col-12 col-sm-8">
        <div class="d-flex justify-content-end align-items-end">
            <button type="button" class="btn btn-secondary btn-lg" data-toggle="modal" data-target="#createTaskModal">
                Create Task <i class="fa fa-plus" style="margin-left: 5px;"></i>
            </button>
        </div>            
        <div id="task"></div>
    </div>
</div>

{{-- Modal --}}

{{--
title
description
urgency
parent_id 
--}}

<div class="modal fade bd-example-modal-lg" id="createTaskModal" tabindex="-1" role="dialog" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Create Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="created-successfully" style="color : green; text-align : center;"></div>
                <form >
                    @csrf
                    <div class="form-group row">
                      <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" placeholder="Task Title" required>
                        <div style="color: red" id="title_error"> </div>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="description" placeholder="Task Description" rows="2" required></textarea>
                          <div style="color: red" id="description_error"> </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputDueTime" class="col-sm-2 col-form-label">Due Time</label>
                        <div class="col-sm-10">
                          <input type="datetime-local" class="form-control" id="due_time" placeholder="Task Due Time" required></input>
                          <div style="color: red" id="due_time_error"> </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputUrgency" class="col-sm-2 col-form-label">Urgency</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="customRadioInline0" name="urgency" class="custom-control-input urgency-radio" value="0">
                                <label class="custom-control-label" for="customRadioInline0">0</label>
                            </div>
                            
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="customRadioInline1" name="urgency" class="custom-control-input urgency-radio" value="1">
                                <label class="custom-control-label" for="customRadioInline1">1</label>
                            </div>
                            <div style="color: red" id="urgency_error"> </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="inputParentID" class="col-sm-2 col-form-label">Parent ID</label>
                        <div class="col-sm-10">
                          <input type="number" class="form-control" id="parent_id" placeholder="Tasks' Parent ID">
                          <div style="color: red" id="parent_id_error"> </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="createTask()">Create</button>
            </div>
        </div>
    </div>
</div>

<script>
    let past_user ="default";
    function createTask(){
        document.getElementById("title_error").innerHTML = "";
        document.getElementById("description_error").innerHTML = "";
        document.getElementById("due_time_error").innerHTML = "";
        document.getElementById("urgency_error").innerHTML = "";
        document.getElementById("parent_id_error").innerHTML = "";
        
        var parent_id = $('#parent_id').val();
        var due_time = $('#due_time').val();

        var data = {
            title: $('#title').val(),
            description: $('#description').val(),
            urgency: $("input[name='urgency']:checked").val()
        };
        if(due_time){
            var selectedDate = new Date(due_time);
            var formattedDateTime =
                selectedDate.getFullYear() + '-' +
                ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
                ('0' + selectedDate.getDate()).slice(-2) + ' ' +
                ('0' + selectedDate.getHours()).slice(-2) + ':' +
                ('0' + selectedDate.getMinutes()).slice(-2) + ':' +
                ('0' + selectedDate.getSeconds()).slice(-2);
            data.due_time = formattedDateTime;
        }
        if (parent_id) {
            data.parent_id = parent_id;
        }

        let resp = $.ajax({
            type: 'POST',
            url: '/tasks',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:data,
        });
        resp.done(function(resp){
            document.getElementById("created-successfully").innerHTML = "<h5>Created Successfully!</h5>";
            getUserTask(0);
        });
        resp.fail(function(resp){
            if(resp.responseJSON.message==="Unauthenticated."){
                var baseUrl = window.location.origin;
                window.location.href = baseUrl + "/login";
            }
            var response = JSON.parse(resp.responseText);
            var errors = response.validation_errors;

            for (var field in errors) {
                var errorMessage = errors[field][0];
                document.getElementById(field+"_error").innerHTML = "<div>"+errorMessage+"</div>";
            }

        });
    }
    function getUserTask(user_id){
        // console.log(user_id);
        if(user_id==0){
            if(past_user!=="default")
                document.getElementsByClassName(past_user)[0].style.backgroundColor = "";
            past_user = "default";
        }
        if(user_id!=0){
            let class_name = "user_filter_"+user_id;
            document.getElementsByClassName(class_name)[0].style.backgroundColor = "#6fb53a";
            if(past_user!=="default")
                document.getElementsByClassName(past_user)[0].style.backgroundColor = "";
            past_user = class_name;
        }
        let resp = $.ajax({
            type: 'GET',
            url: '/task_subtask_user'
        });
        resp.done(function(resp){
            let serial_no = 1; 
            let html = "";
            for(let index=0;index<resp.task.length;index++){
                $task = resp.task[index];

                if(user_id!==0){
                    let flag = false;
                    if($task.sub_tasks.length!==0){
                        for(let sub_task_id = 0;sub_task_id<$task.sub_tasks.length;sub_task_id++){
                            $sub_task = $task.sub_tasks[sub_task_id];
                            for (let assignesID in $sub_task.assignes) {
                                if($sub_task.assignes[assignesID].user_id===user_id){
                                    flag = true;
                                    break;
                                }
                            }
                            if(flag) break;
                        }
                    }
                    
                    else{
                        for (let assignesID in $task.assignes) {
                            if($task.assignes[assignesID].user_id===user_id){
                                flag = true;
                                break;
                            }
                        }
                    }
                    if(!flag) continue;
                }

                
                html += `
                <div class="card-separator" style="padding-top:40px;"></div>
                <div class='card' ${$task.sub_tasks.length == 0 ? `onclick="window.location.href='/task/${$task.id}'"`+`style="cursor: pointer;"` : ''}>
                    <div class="card-header">
                        <div class = "row">
                            <div class = "col-12 col-sm-8">
                                <p class="task-title"> ${serial_no}. ${$task.title}
                                ${
                                    $task.status == 0
                                        ? '<span class="badge badge-danger">Not Completed</span>'
                                        : '<span class="badge badge-success">Completed</span>'
                                }
                                </p>
                            </div>
                            <div class = "col-12 col-sm-4">
                                <div class="d-flex flex-row-reverse">
                `;


                if ($task.assignes !== undefined && $task.sub_tasks.length===0) {
                    for (let assignesID in $task.assignes) {
                        html += '<img src="' + "{{ asset('storage/profile/') }}" + '/' + $task.assignes[assignesID].profile_picture + '" alt="Profile Picture" class="rounded-circle" width="30" style="margin-left: 5px;">';
                    }
                }
                
                html += `           </div>
                                </div>
                            </div>
                        </div>`;
            

                // subtask
                if($task.sub_tasks.length>0){
                    html += `<div class="card-body"><div class="card-separator" style="padding-top:15px;"></div>
                                <div class='card'>
                                    <div class="card-header">`;
                    for(let sub_task_id = 0;sub_task_id<$task.sub_tasks.length;sub_task_id++){
                        $sub_task = $task.sub_tasks[sub_task_id];
                        html += `
                                        <div class = "row" style="cursor: pointer;" onclick="window.location.href='/task/${$sub_task.id}'">
                                            <div class = "col-12 col-sm-8">
                                                <p class="task-title"> ${sub_task_id+1}. ${$sub_task.title}
                                                    ${
                                                        $sub_task.status == 0
                                                            ? '<span class="badge badge-danger">Not Completed</span>'
                                                            : '<span class="badge badge-success">Completed</span>'
                                                    }    
                                                </p>
                                            </div>
                                            <div class = "col-12 col-sm-4">
                                                <div class="d-flex flex-row-reverse">
                                `;

                            if($sub_task.assignes!==undefined){
                                let haveAssignes = false;
                                for(let assignesID in $sub_task.assignes){
                                    haveAssignes = true;
                                    break;
                                }
                                if(haveAssignes){
                                    for(let assignesID in $sub_task.assignes){
                                        $assigne = $sub_task.assignes[assignesID];
                                        html += '<img src="' + "{{ asset('storage/profile/') }}" + '/' + $assigne.profile_picture + '" alt="Profile Picture" class="rounded-circle" width="30" style="margin-left: 5px;">';   
                                    }
                                }
                            }

                            html += `           </div>
                                            </div>
                                        </div>
                                        `;
            
                    }
                    html+=` </div>
                        </div>
                    </div>`;
                        
                }
                html+='</div>';
                serial_no++;
            }
            if(html==="") 
                html = `<div class="card-separator" style="padding-top:40px;"></div>
                        <h3>No tasks available...!</h3>`;
            document.getElementById("task").innerHTML = html;
        });
        resp.fail(function(resp){
            html += "No tasks";
            document.getElementById("task").innerHTML = html;
        })
    }

    function getUsersWithProfile(){
        let resp = $.ajax({
            type: 'GET',
            url: '/users_with_profile'
        });
        resp.done(function(resp){
            let html = "<div class='user_registered_box'><h3>Users</h3>";
            for(let i = 0; i < resp.users.length; i++){
                html += '<div class="border p-3 d-flex align-items-center justify-content-between user_filter_'+resp.users[i].id+'" style="margin-top: 10px; cursor: pointer;" onclick="getUserTask(' + resp.users[i].id + ')">';
                html += '<div>' + resp.users[i].name + '</div>';
                html += '<img src="{{ asset('storage/profile/') }}/' + resp.users[i].profile_picture +
                        '" alt="Profile Picture" class="rounded-circle" width="30" style="margin-left: 5px;">';
                html += '</div>';
            }

            html += '<div class="border p-3 d-flex align-items-center justify-content-between" style="margin-top: 10px; cursor: pointer;" onclick="getUserTask(0)"> Reset <i class="fa fa-refresh fa-spin" style="margin-right:10px;"></i> </div>';

            html+="</div>";
            document.getElementById("user").innerHTML = html;
        });
        resp.fail(function(resp){
            html += "No users";
            document.getElementById("user").innerHTML = html;
        })
    }

    $(document).ready(function() {
        getUserTask(0);
        getUsersWithProfile();
    });

</script>



{{-- Layout:

Search, Filter by userID, Sort by assigned time | task not yet finished

tasks : title

{task
    details
    {user
        details
    }

    add assignes
} --}}

@endsection