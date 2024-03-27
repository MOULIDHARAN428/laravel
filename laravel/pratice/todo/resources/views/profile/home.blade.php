@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb" style="padding-bottom:15px;">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/task">Tasks</a></li>
      <li class="breadcrumb-item active" aria-current="page">Profile</li>
    </ol>
</nav>

<div id="message" style="padding-bottom: 20px;">

</div>

<div id="user-analytical">
  
</div>

<div class="container">
  <div style="padding-top:40px;"></div>
  <div class="row">
      <div class="col-12 col-sm-1"></div>
      <div class="col-12 col-sm-12" id="user-details">
        
      </div>
  </div>
</div>

<!-- Modal : Task Completed-->
<div class="modal fade" id="taskCompleteModal" tabindex="-1" role="dialog" aria-labelledby="taskCompleteModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            Need to change the status of the task to completed?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirmCompleteBtn">Change</button>
        </div>
      </div>
    </div>
</div>

<!-- Modal : Task Redo-->
<div class="modal fade" id="taskRedoModal" tabindex="-1" role="dialog" aria-labelledby="taskRedoModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            Need to change the status of the task to not-completed?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirmCompleteBtn">Change</button>
        </div>
      </div>
    </div>
</div>

<script>
  let user_id_main = {{auth()->user()->id}};
  let task_map_id, map_status;
  function assignTaskMapID(map_id,status){
    task_map_id = map_id;
    map_status = status;
    console.log(task_map_id+" "+map_status);
  }
  function getUserAnalytical(){
    let resp = $.ajax({
                  type: 'GET',
                  url: '/profile-analytics'
               });
    resp.done(function(resp){
    //   console.log("here");
      html=`
      <div class="container">
          <div class="row">
              <div class="col-12 col-sm-4">
                  <div class="card card-user-analytics">
                      <div class="card-body text-center">
                        <h4>Completed Task</h4>
                        <h4>${resp.analytics[0]['completed_task']}</h4>
                      </div>
                    </div>
              </div>
              <div class="col-12 col-sm-4">
                  <div class="card card-user-analytics">
                      <div class="card-body text-center">
                        <h4>Not Completed Task</h4>
                        <h4>${resp.analytics[0]['yet_to_do_task']}</h4>
                      </div>
                    </div>
              </div>
              <div class="col-12 col-sm-4">
                  <div class="card card-user-analytics">
                      <div class="card-body text-center">
                          <h4>Due Task</h4>
                          <h4>${resp.analytics[0]['due_task']}</h4>
                      </div>
                    </div>
              </div>
          </div>
      </div>
      `;
      document.getElementById("user-analytical").innerHTML = html;
    })
    resp.fail(function(resp){

    })
  }

  function getUserDetails(user_id){
      let resp = $.ajax({
          type: 'GET',
          url: '/tasks/mapping/'+user_id
      });
      resp.done(function(resp){
          $user = resp.user[0];

          html = "";

          html += `
          <div class="row">
              <div class="text-center" style="padding-top:10px;">
                  <h2>Assigned Tasks</h2>
              </div>
          </div>
          <div style="padding-bottom:30px;"></div>
          `;
          
          html += '<div class="user-tasks-container" style="">';

          for(let task in resp.task){
              if(task==="user") break;
              $task = resp.task[task];
              
            //   console.log($task);
              html += `
              <div class='card'>
                <div class="card-header d-flex justify-content-between align-items-center">
                        <p class="task-title">${$task['task_title']}</p>
                        
                        ${$task['status']==0 ?
                            `<button type="button" class="btn btn-success" data-toggle="modal" data-target="#taskCompleteModal" onclick="assignTaskMapID('${$task['id']}','${$task['status']}')">
                                Mark as Completed
                            </button>`
                        :
                           `<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#taskRedoModal" onclick="assignTaskMapID('${$task['id']}','${$task['status']}')">
                                Redo task
                            </button>`
                        }
                    </div>
                  <div class="card-body">
                      <div class="container task">
                          <p><strong>Description:</strong> ${$task['role']}</p>
                          <p><strong>Assigned At:</strong> ${$task['assigned_at']}</p>
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
              <div style="padding-top:50px;"></div>`;

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
      
      getUserAnalytical();
      getUserDetails(user_id_main);

      $('#confirmCompleteBtn').on('click', function() {
            var data = {
                status: map_status == 1 ? 0 : 1
            };
            let resp = $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'tasks/mapping/status/'+task_map_id,
                data: data
            });

            resp.done(function(resp){
                html = `<div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card" style="background-color: rgb(100, 195, 100);">
                                    <div class="card-body" style="align-self:center;">
                                        Status Updated Successfully!
                                    </div>
                                </div>
                            </div>
                        </div>`;
                document.getElementById("message").innerHTML = html;
                setTimeout(function() {
                    document.getElementById("message").innerHTML = "";
                }, 5000);
                getUserDetails(user_id_main);
                getUserAnalytical();
            });
            resp.fail(function(resp){
                console.log(resp);
                html = `<div class="row justify-content-center mt-3">
                            <div class="col-md-4">
                                <div class="card" style="background-color: rgb(185, 107, 107);">
                                    <div class="card-body" style="align-self: center;">
                                        Couldn't able to update the task status, contact admin!
                                    </div>
                                </div>
                            </div>
                        </div>`;
                document.getElementById("message").innerHTML = html;

                setTimeout(function() {
                    document.getElementById("message").innerHTML = "";
                }, 5000);
            });

        });
    });
</script>

@endsection