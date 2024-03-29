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

<div class="container" style="padding-top: 40px;">
    <div class="row justify-content-end">
        <div class="col-auto">
            <button type="button" class="btn btn-lg btn-secondary" data-bs-toggle="modal" data-bs-target="#sortModel">
                Filter Chart
            </button>
            <button type="button" class="btn btn-lg btn-secondary" onclick="resetTimeForAnalytics()">
                Reset
            </button>
        </div>
    </div>
</div>


<div id="analytical">
    <div style="padding-top:40px;">
        <h3 style="text-align: center;font-family: cursive;" >Assignes Per Task</h3>
        <canvas id="taskAssignesChart" style="padding-top:35px; margin: auto; display: block; width: 100%; max-width: 1000px;"></canvas>
    </div>

    <div class="row" style="padding-top:80px;">
        <div class="col-12 col-sm-6">
            <h2 style="text-align: center;font-family: cursive;">Task Completed Day Wise</h2>
            <canvas id="taskCompletedDayChart" style="padding-top:35px; margin: auto; display: block; width: 100%; max-width: 600px;"></canvas>
        </div>
        <div class="col-12 col-sm-6">
            <h2 style="text-align: center;font-family: cursive;">Task Completed Hour Wise</h2>
            <canvas id="taskCompletedHourChart" style="padding-top:35px; margin: auto; display: block; width: 100%; max-width: 600px;"></canvas>
        </div>
    <div>
    
    <div style="padding-top:80px;">
        <h2 style="text-align: center;font-family: cursive;">User Task Analytics</h2>
        <div class="row">
            <div class="col-12 col-sm-4">
                <canvas id="taskUserCompletedTaskChart" style=" padding-top:35px; margin: auto; display: block; width: 100%; max-width: 400px;"></canvas>
            </div>
            <div class="col-12 col-sm-4">
                <canvas id="taskUserNotCompletedTaskChart" style=" padding-top:35px; margin: auto; display: block; width: 100%; max-width: 400px;"></canvas>
            </div>
            <div class="col-12 col-sm-4">
                <canvas id="taskUserDueTaskChart" style=" padding-top:35px; margin: auto; display: block; width: 100%; max-width: 400px;"></canvas>
            </div>
        </div>
    </div>
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

{{-- Modal : Sort Time --}}

<div class="modal fade" id="sortModel" tabindex="-1" aria-labelledby="sortModelLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sortModelLabel">Filter the chart by time</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="text-center" id="success_for_filter" style="color: green;padding-bottom:10px;"></div>
            <div id="error" style="color: red;padding-bottom:10px;"></div>
            <div class="mb-3 row">
                <label for="inputfrom" class="col-sm-2 col-form-label">From</label>
                <div class="col-sm-10">
                    <input type="datetime-local" class="form-control" id="from" required>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="inputfrom" class="col-sm-2 col-form-label">To</label>
                <div class="col-sm-10">
                    <input type="datetime-local" class="form-control" id="to" required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onClick="assignTimeForAnalytics()">Filter</button>
        </div>
      </div>
    </div>
</div>

<script>
    let user_id_main = {{auth()->user()->id}};
    let task_map_id, map_status;
    let from_,to_;
    function assignTaskMapID(map_id,status){
        task_map_id = map_id;
        map_status = status;
        console.log(task_map_id+" "+map_status);
    }

    function truncateLabel(label, maxLength) {
        if (label.length > maxLength) {
            return label.substring(0, maxLength) + '...'; // Truncate label
        }
        return label;
    }
    function callChartsFunction(){
      taskAssignesAnalytics();
      taskCompletedDayAnalytics();
      taskCompletedHourAnalytics();
      userTaskAnalytics();
    }
    function resetTimeForAnalytics(){
        from_ = null;
        to_ = null;
        callChartsFunction();
    }

    function assignTimeForAnalytics(){
        var start_time = $('#from').val();
        var end_time = $('#to').val();
        if(start_time){
            var selectedDate = new Date(start_time);
            from_ =
                selectedDate.getFullYear() + '-' +
                ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
                ('0' + selectedDate.getDate()).slice(-2) + ' ' +
                ('0' + selectedDate.getHours()).slice(-2) + ':' +
                ('0' + selectedDate.getMinutes()).slice(-2) + ':' +
                ('0' + selectedDate.getSeconds()).slice(-2);
        }
        if(end_time){
            var selectedDate = new Date(end_time);
            to_ =
                selectedDate.getFullYear() + '-' +
                ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' +
                ('0' + selectedDate.getDate()).slice(-2) + ' ' +
                ('0' + selectedDate.getHours()).slice(-2) + ':' +
                ('0' + selectedDate.getMinutes()).slice(-2) + ':' +
                ('0' + selectedDate.getSeconds()).slice(-2);
        }
        callChartsFunction();
    }

    function taskAssignesAnalytics(){
        var data = {

        };
        if(from_){
            data.from = from_;
        }
        if(to_){
            data.to = to_;
        }
        let resp = $.ajax({
                    type: 'GET',
                    url: '/analytics_task_assignes',
                    data: data,
                });
        resp.done(function(resp){
            let data = resp['data'];
            var title = [];
            var assignes = [];
            var barColors = [];
            for(let i=0;i<data.length;i++){
                title.push(truncateLabel(data[i]['title'], 10)); 
                assignes.push(data[i]['assignes']);
                barColors.push('#' + Math.floor(Math.random() * 16777215).toString(16));
            }

            new Chart("taskAssignesChart", {
            type: "bar",
            data: {
                labels: title,
                datasets: [{
                    backgroundColor: barColors,
                    data: assignes
                }]
            },
            options: {
                legend: {display: false},
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 10,
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }]
                }
            }
            });
            if(from_)
                document.getElementById("success_for_filter").innerHTML = "<div> Filtered Successfully! </div>";
        });
        resp.fail(function(resp){
            var response = JSON.parse(resp.responseText);
            document.getElementById("error").innerHTML = "<div>"+response.error+"</div>";

            setTimeout(function() {
                document.getElementById("error").innerHTML = "";
            }, 5000);
        });
    }

    function taskCompletedDayAnalytics(){
        var data = {
        };
        if(from_){
            data.from = from_;
        }
        if(to_){
            data.to = to_;
        }
        let resp = $.ajax({
                    type: 'GET',
                    url: '/analytics_day_data',
                    data: data
                });
        
        resp.done(function(resp){
            let data = resp['data'];
            const lables = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            const values = [];
            for(let i=0;i<data.length;i++){
                values.push(data[i]);
            }
            new Chart("taskCompletedDayChart", {
            type: "line",
            data: {
                labels: lables,
                datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "yellow",
                borderColor: "pink",
                data: values
                }]
            },
            options: {
                legend: {display: false},
                scales: {
                    yAxes: [{
                        ticks: {
                            precision: 0, // Display integer values only
                            beginAtZero: true, // Start scale from zero
                            suggestedMin: Math.min(...values), // Extra padding at the bottom
                            suggestedMax: Math.max(...values) + 1 // Extra padding at the top
                        }
                    }]
                }
            }
            });
        });
        resp.fail(function(resp){});
    }

    function taskCompletedHourAnalytics(){
        var data = {
        };
        if(from_){
            data.from = from_;
        }
        if(to_){
            data.to = to_;
        }
        let resp = $.ajax({
                    type: 'GET',
                    url: '/analytics_hour_data',
                    data: data
                });
        
        resp.done(function(resp){
            let data = resp['data'];
            const lables = ['0', '1', '2', '3', '4', '5', '6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
            const values = [];
            for(let i=0;i<data.length;i++){
                values.push(data[i]);
            }
            new Chart("taskCompletedHourChart", {
            type: "line",
            data: {
                labels: lables,
                datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "red",
                borderColor: "violet",
                data: values
                }]
            },
            options: {
                legend: {display: false},
                scales: {
                    yAxes: [{
                        ticks: {
                            precision: 0, // Display integer values only
                            beginAtZero: true, // Start scale from zero
                            suggestedMax: Math.max(...values) + 1 // Extra padding at the top
                        }
                    }]
                }
            }
            });
        });
        resp.fail(function(resp){});
    }

    function userTaskAnalytics(){
        let resp = $.ajax({
                  type: 'GET',
                  url: '/analytics_user_tasks'
               });
        resp.done(function(resp){
            let data = resp['data'];
            var lables = [];
            var assignes = [];
            var completed = [];
            var due = [];
            var barColors1 = [];
            var barColors2 = [];
            var barColors3 = [];
            for(let i=0;i<data.length;i++){
                lables.push(data[i]['name']);
                assignes.push(data[i]['assigned']);
                completed.push(data[i]['completed']);
                due.push(data[i]['due']);
                barColors1.push('#' + Math.floor(Math.random() * 16777215).toString(16));
                barColors2.push('#' + Math.floor(Math.random() * 16777215).toString(16));
                barColors3.push('#' + Math.floor(Math.random() * 16777215).toString(16));
            }
            //Completed chart
            new Chart("taskUserCompletedTaskChart", {
            type: "bar",
            data: {
                labels: lables,
                datasets: [{
                    backgroundColor: barColors1,
                    data: completed
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: "Completed Tasks"
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 10,
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            precision: 0
                        }
                    }],
                }
            }
            });

            //Not-Completed chart
            new Chart("taskUserNotCompletedTaskChart", {
            type: "bar",
            data: {
                labels: lables,
                datasets: [{
                    backgroundColor: barColors2,
                    data: assignes
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: "Not Completed Tasks"
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 10,
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            precision: 0
                        }
                    }],
                }
            }
            });

            //Due Chart
            new Chart("taskUserDueTaskChart", {
            type: "bar",
            data: {
                labels: lables,
                datasets: [{
                    backgroundColor: barColors3,
                    data: due
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: "Due Tasks"
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 10,
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            precision: 0
                        }
                    }],
                }
            }
            });
        })
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
                  <h2 style="font-family: cursive;">Assigned Tasks</h2>
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
      callChartsFunction();

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