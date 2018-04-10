var conn = new WebSocket('ws://localhost:9000');
var chatBox, username = false, item = {}, user = {}, dirty = false;

conn.onopen = function(e) {
    console.log("Connection established!");
};

conn.onmessage = function(e) {
	console.log(e);
		var data =  JSON.parse(e.data),
	    userChat = $(".user-chat");
	    if(data.type == "send")
	    	chatBox.append(appendUserData(data.message, data.username));
	    else if(data.type=="onliners"){
	    	chatBox.html("");
	    	let JsonObj = data.data;
	    	$.each(JsonObj, function(key, value) {
	    			chatBox.append(onlineUserList(value["name"]));    			
	    	});
	    	dirty = true;
	    }

};

$(document).ready(function(){
	chatBox = $("ul.chat");

	/**
	* Hide send button and options button if username is not set
	**/
	if(!localStorage.getItem("username")) {
		$("#options-btn").css({"display":"none"});
		$(".send-message").hide();
	}
	else
		$(".set-username").hide();


	/**
	* Function to send message
	**/
    $("#chat_form").submit(function(e){
    	e.preventDefault();
    	var form = $("#chat_form"),
        chatDiv = $(document).find(".my-chat");
        item["username"] = localStorage.getItem("username");
        item["message"] = form.find(".chat-message").val();
      //  item["to"] = 90;
        item["type"] = "send";
        conn.send(JSON.stringify(item));
        chatBox.append(appendMyData(item["message"], localStorage.getItem("username")));
        form.find("#btn-input").val("");
        dirty = false;
    });  

    /**
    * Function to save username
    **/ 
    $(document).find("#btn-username").on("click", function() {
    	var username = $("#txt-username").val();
    	if(username) {
	    	localStorage.setItem("username", username);
        	user["user_id"] = Math.floor(Math.random()*(50-1+1)+1);
	    	user["username"] = username;
	    	user["type"] = "register";
	    	$.ajax( {
	    		url: window.location.origin+'/chat/src/database.php',
	    		data: {username:username},
	    		type: "POST",
	    		success: function (e) {
	    			console.log(e);
	    		},
	    		error: function(e) {
	    			console.log(e);
	    		}

	    	});
	    	conn.send(JSON.stringify(user));
	    	if(localStorage.getItem("username")) {
	    		//location.reload();
	    	}
	    }
    });

    /**
    * Show chat screen and close any other screen
    **/
    $(document).find("#chat-screen").on("click", function() {
    	if(dirty)
    		chatBox.html("");
    });

    /**
    * Logout current use
    **/
    $(document).find("#signout-btn").on("click", function() {
    	if(localStorage.getItem("username")) {
    		localStorage.removeItem("username");
    		location.reload();
    	}
    });


    /**
    * Function to show online users
    **/
    $(document).find("#online-users").on("click", function() {
    	var request = {};
    	request["type"] = "online";
    	conn.send(JSON.stringify(request));
    });
    /**
    * Function to list online Users
    **/
    
});

function appendUserData(data, username) {
	data = `<li class="left clearfix"><span class="chat-img pull-left">
                            <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                        </span>
                            <div class="chat-body user-chat clearfix">
                                <div class="header">
                                    <strong class="primary-font">`+username+`</strong> <small class="pull-right text-muted">
                                        <span class="glyphicon glyphicon-time"></span>14 mins ago</small>
                                </div>
                                <p>`+
                                   data +
                                `</p>
                            </div>
                        </li>`;
    return data;
}

function appendMyData(data, username) {
	data = ` <li class="right clearfix"><span class="chat-img pull-right">
                            <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
                        </span>
                            <div class="chat-body my-chat clearfix">
                                <div class="header">
                                    <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>15 mins ago</small>
                                    <strong class="pull-right primary-font">`+username+`</strong>
                                </div>
                                <p>`
                                    +data+
                                `</p>
                            </div>
                        </li>`;
    return data;
}

function onlineUserList(name) {
	data = `  <div class="row sideBar">
          <div class="row sideBar-body">
            <div class="col-sm-3 col-xs-3 sideBar-avatar">
              <div class="avatar-icon">
                <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
              </div>
            </div>
            <div class="col-sm-9 col-xs-9 sideBar-main">
              <div class="row">
                <div class="col-sm-8 col-xs-8 sideBar-name">
                  <span class="name-meta">`+name+`
                </span>
                </div>
              </div>
            </div>
          </div>`;
    return data;
}
function removeAllBlankOrNull(JsonObj) {
    $.each(JsonObj, function(key, value) {
        if (value === "" || value === null) {
            delete JsonObj[key];
        } else if (typeof(value) === "object") {
            JsonObj[key] = removeAllBlankOrNull(value);
        }
    });
    return JsonObj;
}

/**
* Subscribe for one to one chat
**/
function subscribe(channel) {
    conn.send(JSON.stringify({type: "subscribe", channel: channel}));
}
