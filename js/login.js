class loginSystem {
    constructor () {

    }

    bindButtonActions() {
      $('#login-button').click(()=>{
        this.onLogin();
      });

      $('#logout-button, #dev-logout-button, #sm-logout-button').click(()=>{
        this.onLogout();
      });
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
            console.log(this);
            if(data['status'] == 1) {
              localStorage.token = data['token'];
              localStorage.scrum_master = data['scrum_master'];
              localStorage.project_id = 0;
              console.log('Successfully retrieved token from the server! Token: ' + data['token']);
              this.checkLogin();
            } else {
              console.log("Error: Login Failed");
            }
          }),
          error: function() {
            console.log("Error: Login Failed");
          }
        });
    }

    onLogout() {
      localStorage.clear();
      this.hideDeveloperFrame();
      this.hideScrumPokerFrame();
      this.hideSmStartPage();
      this.showLoginFrame();
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
});
