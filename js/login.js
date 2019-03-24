class loginSystem {
    constructor () {

    }

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
    }

    checkProject() {
      var project_id = location.search.split('project_id=')[1];
      if(project_id != undefined && localStorage.token != undefined) {
         this.checkUserProject(project_id);
      }
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
            if(data['status'] == 1) {
              localStorage.token = data['user_details']['token'];
              localStorage.scrum_master = data['user_details']['scrum_master'];
              localStorage.project_id = 0;
              console.log('Successfully retrieved token from the server! Token: ' + data['token']);
              this.checkLogin();
              this.checkProject();
            } else if(data['status'] == 2) {
              console.log("Error: Login Failed");
            } else if(data['status'] == 3) {
              onLogout();
            }
          }),
          error: function() {
            console.log("Error: Login Failed");
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
              this.checkLogin();
            } else if(data['status'] == 2) {
              localStorage.project_id =  0;
              this.checkLogin();
            } else if(data['status'] == 3) {
              onLogout();
            }
          }),
          error: function() {
            console.log("Error: check Failed");
          }
        });
    }

    onLogout() {
      this.onLogoutServer();
      localStorage.clear();
      this.hideDeveloperFrame();
      this.hideScrumPokerFrame();
      this.hideSmStartPage();
      this.showLoginFrame();
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
      console.log(localStorage.project_id);
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
            } else if(data['status'] == 2) {
              console.log("Error: Project creation Failed");
            }
          }),
          error: function() {
            console.log("Error: Project creation failed");
          }
        });
    }

    onTicketSubmit() {
        $.ajax({
          type: "POST",
          url: "./src/restService.php",
          dataType: "json",
          data: {
            ticket_id: $( "#ticket_id" ).val(),
            ticket_desc: $( "#ticket_desc" ).val(),
            ticket_link: $( "#ticket_link" ).val(),
            token: localStorage.token,
            project_id: localStorage.project_id,
            action: 'createTicket'
          },
          success: ((data)=>{
            if(data['status'] == 1) {
              localStorage.token = data['user_details']['token'];
              localStorage.scrum_master = data['user_details']['scrum_master'];
              localStorage.project_id = 0;
              console.log('Successfully retrieved token from the server! Token: ' + data['token']);
              this.checkLogin();
            } else if(data['status'] == 2) {
              console.log("Error: Login Failed");
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
}

$(document).ready(function() {
  var ls = new loginSystem();
  ls.bindButtonActions();
  ls.checkLogin();
  ls.checkProject();
});
