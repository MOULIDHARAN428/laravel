@extends('layouts.app')

@section('content')

<div class="container">
    
    <div class="row head-main-task">
        <div class="col-12 col-sm-1"></div>

        <div class="col-12 col-sm-6 filter">
            <form class="example" action="action_page.php">
                @csrf
                <input type="text" placeholder="Search by User ID" name="search" id="search">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
        
        <div class="col-12 col-sm-2 assign-time">
            <div class="dropdown">
                
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter
                </button>
                
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" href="#">Completed Task </a>
                  <a class="dropdown-item" href="#">Not Completed Task</a>
                  <a class="dropdown-item" href="#">Due Task</a>
                </div>

              </div>
        </div>
    </div>

    <div class="task" id="task">

    </div>

    
    

    <script>
        function getUserTask(){
            let resp = $.ajax({
                type: 'GET',
                url: '/task_subtask_user'
            });
            resp.done(function(resp){
                // console.log(resp.task);
                // console.log(resp.task[10]);
                // console.log(resp.task[10].sub_tasks.length);
                // console.log(resp.task[16].sub_tasks[0].id);
                console.log(resp.task[16].sub_tasks[0].assignes!==undefined);
                let html = "";
                for(let taskID in resp.task){
                    $task = resp.task[taskID];
                    html += `
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
                                        <p><strong>Parent ID:</strong> ${
                                            $task.parent_id ? $task.parent_id : "No parent ID"
                                        }</p>
                                    </div>
                                </div>
                            </div>`;


                    if($task.assignes!==undefined){
                        html+=`
                            <div class="container user">
                                <h5>Users :</h5> 
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>S.NO</strong></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Name</strong></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Role</strong></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Status</strong></p>
                                    </div>
                                </div>`;
                        let j = 1; 
                        for(let assignesID in $task.assignes){
                            $assigne = $task.assignes[assignesID];
                            html+=`
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>${j}</strong></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>${$assigne.name}</strong></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>${$assigne.role}</strong></p>
                                    </div>
                                    <div class="col-md-3">
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

                    //subtask
                    if($task.sub_tasks.length>0){
                        html+=`<p class="sub-task-head">Sub Tasks</p> `;
                        for(let sub_task_id = 0;sub_task_id<$task.sub_tasks.length;sub_task_id++){
                            $sub_task = $task.sub_tasks[sub_task_id];
                            html += `
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-heading-${$sub_task.id}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-${$sub_task.id}" aria-expanded="false" aria-controls="flush-collapse-${$sub_task.id}">
                                    ${$sub_task.title}
                                    </button>
                                </h2>
                                <div id="flush-collapse-${$sub_task.id}" class="accordion-collapse collapse" aria-labelledby="flush-heading-${$sub_task.id}" data-bs-parent="#accordionFlushExample">
                                    <div class="sub-task-card">
                                        <div class="card-body">

                                            <div class="container task">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Description:</strong> ${$sub_task.description}</p>
                                                        <p><strong>Urgency:</strong> ${$sub_task.urgency}</p>
                                                        <p><strong>Status:</strong>
                                                            ${
                                                                $sub_task.status == 0
                                                                ? '<span class="badge badge-danger">Not Completed</span>'
                                                                : '<span class="badge badge-success">Completed</span>'
                                                            } 
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Time Completed:</strong>${
                                                            $sub_task.time_completed ?  $sub_task.time_completed : "Not Completed"
                                                        }</p>
                                                        <p><strong>Due Time:</strong>${
                                                            $sub_task.due_time ? $sub_task.due_time : "No parent ID"
                                                        }</p>
                                                        <p><strong>Completed:</strong>${
                                                            $sub_task.parent_id ? $sub_task.parent_id : "No parent ID"
                                                        }</p>
                                                    </div>
                                                </div>
                                            </div>
                            `;

                            if($sub_task.assignes!==0){
                            
                            let haveAssignes = false;
                            for(let assignesID in $sub_task.assignes){
                                haveAssignes = true;
                                break;
                            }
                            if(haveAssignes){
                            html+= `<div class="container user">
                                    <h5>Users :</h5> 
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p><strong>S.NO</strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Name</strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Role</strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Status</strong></p>
                                        </div>
                                    </div>`;
                            for(let assignesID in $sub_task.assignes){
                                let j = 1;
                                $assigne = $sub_task.assignes[assignesID];
                                html += `<div class="row">
                                            <div class="col-md-3">
                                                <p><strong>${j}</strong></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>${$assigne.name}</strong></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>${$assigne.role}</strong></p>
                                            </div>
                                            <div class="col-md-3">
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
                                
                                html += `</div>`;
                            }
                            }
                            
                            html += `
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            `;
                        }
                    }

                    html += `</div>
                             </div>`;


                }
                document.getElementById("task").innerHTML = html;
            });
            resp.fail(function(resp){

            })
        }

        $(document).ready(function() {
            getUserTask();
        });

    </script>

</div>



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