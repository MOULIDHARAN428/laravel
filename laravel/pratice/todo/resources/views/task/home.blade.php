@extends('layouts.app')

@section('content')
{{-- <div class="container"> --}}
    <div class="row">
        <div class="col-12 col-sm-1"></div>
        <div class="col-12 col-sm-2 user_registered" id="user">
    
        </div>
        <div class="col-12 col-sm-8">
            <div class="d-flex justify-content-end align-items-end">
                <button type="button" class="btn btn-secondary btn-lg">
                    Create Task <i class="fa fa-plus" style="margin-left: 5px;"></i>
                </button>
            </div>            
            <div id="task"></div>
        </div>
    </div>
{{-- </div> --}}

<script>
    let resp;
    function editTaskID(resp){
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
                <div class='card'>
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
            

                // // //subtask
                if($task.sub_tasks.length>0){
                    html += `<div class="card-body"><div class="card-separator" style="padding-top:15px;"></div>
                                <div class='card'>
                                    <div class="card-header">`;
                    for(let sub_task_id = 0;sub_task_id<$task.sub_tasks.length;sub_task_id++){
                        $sub_task = $task.sub_tasks[sub_task_id];
                        html += `
                                        <div class = "row">
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
    }
    function getUserTask(user_id){
        // console.log(resp);
        if(resp===undefined){ 
            console.log("here");
            let resp = $.ajax({
                type: 'GET',
                url: '/task_subtask_user'
            });
            resp.done(function(response){
                resp = response;
            });
            resp.fail(function(resp){
                html += "No tasks";
                document.getElementById("task").innerHTML = html;
            })
        }
        editTaskID(resp);
    }

    function getUsersWithProfile(){
        let resp = $.ajax({
            type: 'GET',
            url: '/users_with_profile'
        });
        resp.done(function(resp){
            let html = "<div class='user_registered_box'><h3>Users</h3>";
            for(let i = 0; i < resp.users.length; i++){
                html += '<div class="border p-3 d-flex align-items-center justify-content-between" style="margin-top: 10px; cursor: pointer;" onclick="getUserTask(' + resp.users[i].id + ')">';
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