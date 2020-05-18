class loginSystem {
    constructor () {
      this.websocket;
      this.sender;
      this.estiMap = new Map();
    }

    alertErrorMessage(msg) {
      $(".alert-message").addClass("error-alert").removeClass("normal-alert");
      $(".alert-message").html(msg);
      $(".alert-message").delay(100).fadeIn().delay(4000).fadeOut();
    };

    alertMessage(msg) {
      $(".alert-message").addClass("normal-alert").removeClass("error-alert");
      $(".alert-message").html(msg);
      $(".alert-message").delay(100).fadeIn().delay(4000).fadeOut();
    };

    bindButtonActions() {
      $('#login-button').click(()=>{
        this.onLogin();
      });

      $('#createProj').click(()=>{
        this.onProjectCreate();
      });

      $('#logout-button, #dev-logout-button, #sm-logout-button').click(()=>{
        this.onLogout();
      });

      $('#sm_ticket_submit').click(()=>{
        this.onTicketSubmit();
      });
      $('#sm_ticket_reset').click(()=>{
        this.ticketFormReset();
      });

      $('#dev_est_button').click(()=>{
        this.onEstimationSubmit();
      });

      $('#sm_reveal').click(()=>{
        this.sendRevealOrder();
      });

      $('#final_est_button').click(()=>{
        this.sendFinalEstimation();
      });
    }

    checkProject() {
      var project_id = location.search.split('project_id=')[1];
      if(project_id != undefined && localStorage.token != undefined) {
         this.checkUserProject(project_id);
      }
    }
    setUserProjectName() {
        $( "#project_name" ).text(localStorage.project_name);
        $( "#user_name" ).text(localStorage.user_name);
        $( "#sm_project_name" ).text(localStorage.project_name);
        $( "#sm_user_name" ).text(localStorage.user_name);
    }

    onLogin() {
        $.ajax({
          type: "POST",
          url: "./src/restService.php",
          dataType: "json",
          data: {
            username: $( "#username" ).val(),
            password: $( "#password" ).val(),
            action: 'login'
          },
          success: ((data)=>{
            console.log(data);
            if(data['status'] == 1) {
              localStorage.token = data['user_details']['token'];
              localStorage.scrum_master = data['user_details']['scrum_master'];
              localStorage.project_id = 0;
              localStorage.user_name = data['user_details']['full_name'];
              localStorage.project_url = data['project_url'];
              localStorage.socket_url = data['socket_url'];
              console.log('Successfully retrieved token from the server! Token: ' + data['token']);
              this.checkLogin();
              this.checkProject();
            } else if(data['status'] == 2) {
              this.alertErrorMessage("Error: Login Failed");
            } else if(data['status'] == 3) {
              this.onLogout();
            }
          }),
          error: function(error) {
            this.alertErrorMessage("Error: Login Failed");
          }
        });
    }

    checkUserProject(proj_id) {
        $.ajax({
          type: "POST",
          url: "./src/restService.php",
          dataType: "json",
          data: {
            token: localStorage.token,
            proj_id: proj_id,
            action: 'projUserCheck'
          },
          success: ((data)=>{
            if(data['status'] == 1) {
              localStorage.project_id =  data['project_id'];
              localStorage.project_name = data['project_name'];
              this.checkLogin();
            } else if(data['status'] == 2) {
              this.alertErrorMessage("Error: Project not selected");
              localStorage.project_id =  0;
              this.checkLogin();
            } else if(data['status'] == 3) {
              this.onLogout();
            }
          }),
          error: function() {
            this.alertErrorMessage("Error: Project not selected");
          }
        });
    }

    onLogout() {
      this.onLogoutServer();
      localStorage.clear();
      this.estiMap = new Map();
      this.hideDeveloperFrame();
      this.hideScrumPokerFrame();
      this.hideSmStartPage();
      this.showLoginFrame();
      this.alertMessage("Successfully logged out.");
    }

    onLogoutServer() {
      $.ajax({
        type: "POST",
        url: "./src/restService.php",
        dataType: "json",
        data: {
          action: 'logout'
        },
        success: ((data)=>{
          console.log('logged out');
        }),
        error: function() {
          console.log("Error: Login Failed");
        }
      });
    }

    checkLogin() {
      this.hideLoginFrame();
      this.hideScrumPokerFrame();
      this.hideDeveloperFrame();
      this.hideSmStartPage();
      if(localStorage.token) {
        if (localStorage.scrum_master == 1) {
            if(localStorage.project_id != 0) {
              this.showScrumPokerFrame();
            } else {
              this.showSmStartPage();
            }
        } else {
            this.showDeveloperFrame();
        }
      } else {
        this.showLoginFrame();
      }
      this.setUserProjectName()
    }

    onProjectCreate() {
        $.ajax({
          type: "POST",
          url: "./src/restService.php",
          dataType: "json",
          data: {
            proj_name: $( "#project-name" ).val(),
            token: localStorage.token,
            action: 'create_proj'
          },
          success: ((data)=>{
            if(data['status'] == 1) {
              console.log(data);
              localStorage.project_id = data['project_id'];
              localStorage.project_name = data['project_name'];
              this.checkLogin();
              var data = {
                action: "newProject",
                project_id: data['project_id'],
                project_name: data['project_name']
              };
              this.send_socket_message(data);
            } else if(data['status'] == 2) {
              console.log("Error: Project creation Failed");
            } else if(data['status'] == 4) {
                this.alertErrorMessage("Error: Project already exist. Pls give different name.");
            }
          }),
          error: function() {
            console.log("Error: Project creation failed");
          }
        });
    }

    ticketFormReset() {
      $( "#ticket_name" ).val('');
      $( "#ticket_desc" ).val('');
      $( "#ticket_link" ).val('');
      localStorage.removeItem('ticket_id');
      var data = {
        action: "resetTicket"
      };
      this.send_socket_message(data);
    }

    onTicketSubmit() {
        $.ajax({
          type: "POST",
          url: "./src/restService.php",
          dataType: "json",
          data: {
            ticket_name: $( "#ticket_name" ).val(),
            ticket_desc: $( "#ticket_desc" ).val(),
            ticket_link: $( "#ticket_link" ).val(),
            token: localStorage.token,
            project_id: localStorage.project_id,
            action: 'createTicket'
          },
          success: ((data)=>{
            if(data['status'] == 1) {
              this.send_socket_message(data.broadcast);
              this.alertMessage("Ticket created and broadcasted.");
            } else if(data['status'] == 2) {
              this.alertErrorMessage("Error: Ticket creation failed.");
            } else if(data['status'] == 3) {
              onLogout();
            } else if(data['status'] == 4) {
              this.alertErrorMessage("Error: Ticket creation failed. Provide all data.");
            }
          }),
          error: function() {
            console.log("Error: Login Failed");
          }
        });
    }

      sendFinalEstimation() {
        var est = $('input[name=sm_estimation]:checked').val();
        if (est == undefined) {
          console.log("select estimation");
          return;
        }
          $.ajax({
            type: "POST",
            url: "./src/restService.php",
            dataType: "json",
            data: {
              token: localStorage.token,
              ticket_id: localStorage.ticket_id,
              project_id: localStorage.project_id,
              estimation: est,
              action: 'finalEstimation'
            },
            success: ((data)=>{
              console.log(data);
              if(data['status'] == 1) {
                console.log(data.broadcast);
                this.send_socket_message(data.broadcast);
                this.alertMessage("Final estimation submitted.");
                this.resetTicket();
                $('input[name=sm_estimation]:checked').attr("checked", false);
              } else if(data['status'] == 2) {
              } else if(data['status'] == 3) {
                onLogout();
              }
            }),
            error: function() {
              console.log("Error: Login Failed");
            }
          });
      }



    onEstimationSubmit() {
      var est = $('input[name=dev_estimation]:checked').val();
      if (est == undefined) {
        console.log("select estimation");
        return;
      }
        $.ajax({
          type: "POST",
          url: "./src/restService.php",
          dataType: "json",
          data: {
            token: localStorage.token,
            ticket_id: localStorage.ticket_id,
            project_id: localStorage.project_id,
            estimation: est,
            action: 'submitEstimation'
          },
          success: ((data)=>{
            if(data['status'] == 1) {
              this.send_socket_message(data.broadcast);
            } else if(data['status'] == 2) {
            } else if(data['status'] == 3) {
              onLogout();
            }
          }),
          error: function() {
            console.log("Error: Login Failed");
          }
        });
    }

    showScrumPokerFrame() {
        $("#scrum_poker_frame").show();
        console.log("s show");
         var url = localStorage.project_url +'project_id='+ localStorage.project_id;
         var projUrlStr = '<a href="'+url+'">'+url+'</a>';
        $("#sm_share_url").html(projUrlStr);
    }

    hideScrumPokerFrame() {
      $("#scrum_poker_frame").hide();
      console.log("s hide");

    }

    showLoginFrame() {
       $("#login_frame").show();
       console.log("l show");
    }

    hideLoginFrame() {
       $("#login_frame").hide();
       console.log("l hide");
    }

    showDeveloperFrame() {
       $("#developer_frame").show();
       console.log("dev show");
    }

    hideDeveloperFrame() {
       $("#developer_frame").hide();
       console.log("dev hide");
    }

    showSmStartPage() {
      $("#sm-project-frame").show();
    }

    hideSmStartPage() {
      $("#sm-project-frame").hide();
    }

    create_socket(){
  		//var wsUri = localStorage.socket_url;
  		this.websocket = new WebSocket("ws://ec2-34-214-229-187.us-west-2.compute.amazonaws.com:9000/scrumpoker/socket/server.php");
  		this.sender = true;
  		this.websocket.onopen = function(ev) { // connection is open
  			console.log("connection created");
  		}

  		// Message received from server
  		this.websocket.onmessage = ((ev)=> {
  			var response 		= JSON.parse(ev.data); //PHP sends Json data
        console.log(response);
        if(response.message != null && response.message.action != null) {
      			var res_type 		= response.message.action; //message type

      			switch(res_type){
      				case 'new_ticket_created':
      					this.update_new_ticket(response.message);
      					break;
              case 'resetTicket':
        					this.resetTicket();
        					break;
              case 'update_estimation':
                 this.updateEstimation(response.message);
                 break;
              case 'revealEstimation':
                if(localStorage.scrum_master == 0) {
                    this.revealDevEstimationPanel();
                    this.alertMessage("Estimation revealed. You can discuss.");
                }
                break;
              case 'newProject':
                this.updateNewProject(response.message);
                break;
              case 'estimation_done':
                this.listAllprojects(response.message);
                break;
      			}
        }
        this.sender = false;
  		});
  		this.websocket.onerror	= function(ev){
  			 console.log("socket connection error");
  		};
  		this.websocket.onclose 	= function(ev){
  			console.log("socket connection closed");
  		};
  	}


    listAllprojects(message){
      //console.log(message);
      var projHtml = "";
      for (var est in message.estimations) {
        var ticket = message.estimations[est];
        console.log(est);
        projHtml += '<div class="row">Issue Id: '+ ticket.name +'</div>';
        projHtml += '<div class="row">Final Estimation: '+ ticket.final_estimation +'</div>';
        projHtml += '</hr>';
      }
      if(localStorage.scrum_master == 1) {
        $( "#sm_project_list" ).html(projHtml);
      } else {
        $( "#dev_project_list" ).html(projHtml);
      }
      this.alertMessage("Estimated ticket list updated.");
      this.resetTicket();
    }

    updateNewProject(message){
      localStorage.project_id = message.project_id;
      localStorage.project_name = message.project_name;
      this.setUserProjectName();
      this.checkUserProject(message.project_id);
    }

    updateEstimation(message){
      var userEsti = {};
      if(localStorage.ticket_id == message.ticket_id) {
         userEsti['user_id'] = message.user_id;
         userEsti['user_name'] = message.user_name;
         userEsti['estimation'] = message.estimation;
         this.estiMap.set(message.user_id, userEsti);
      }
      if(localStorage.scrum_master == 1) {
        this.updateScrumMasterEstimationPanel();
      } else {
        this.updateDevEstimationPanel();
      }
      this.alertMessage("Developer provided estimation.");
    }

    sendRevealOrder() {
      this.send_socket_message({'action':'revealEstimation'});
    }

    updateScrumMasterEstimationPanel() {
      var estHtml = "";
      for (let [key, value] of this.estiMap) {
        console.log(value);
        estHtml += '<div class="row">'+ value.user_name +' : '+ value.estimation+' Days</div>';
      }
      $( "#sm_estimation_list" ).html(estHtml);
    }

    updateDevEstimationPanel() {
      var estHtml = "";
      for (let [key, value] of this.estiMap) {
        console.log(value);
        estHtml += '<div class="row">'+ value.user_name +' : ***'+' Days</div>';
      }
      $( "#dev_estimation_list" ).html(estHtml);
    }

    revealDevEstimationPanel() {
      var estHtml = "";
      for (let [key, value] of this.estiMap) {
        console.log(value);
        estHtml += '<div class="row">'+ value.user_name +' : '+ value.estimation+' Days</div>';
      }
      $( "#dev_estimation_list" ).html(estHtml);
    }

    resetTicket() {
      console.log('reset');
      localStorage.removeItem('ticket_id');
      $( "#dev_ticket_name" ).text('');
      $( "#dev_ticket_desc" ).text('');
      $( "#dev_ticket_url" ).text('');
      $('input[name=dev_estimation]:checked').attr("checked", false);
      this.estiMap = new Map();
      if(localStorage.scrum_master == 1) {
        this.updateScrumMasterEstimationPanel();
      } else {
        this.updateDevEstimationPanel();
      }
    }

    send_socket_message(message){
        var msg = {
          message: message
        };
        this.sender = true;
        this.websocket.send(JSON.stringify(msg));
	  }

    update_new_ticket(response) {
      localStorage.ticket_id = response.ticket_id;
      if(!this.sender) {
        console.log(response);
          $( "#dev_ticket_name" ).text(response.ticket_name);
          $( "#dev_ticket_desc" ).text(response.ticket_desc);
          $( "#dev_ticket_url" ).text(response.ticket_url);
          this.alertMessage("New ticket posted. Pls estimate.");
      }
    }
}

$(document).ready(function() {
  var ls = new loginSystem();
  ls.bindButtonActions();
  ls.checkLogin();
  ls.checkProject();
  ls.create_socket();
  ls.resetTicket();
});
