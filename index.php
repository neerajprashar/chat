<!DOCTYPE html>
<html>
<head>
	<title>Chat App</title>
</head>
<link rel="stylesheet" type="text/css" href="Assets/main.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="Assets/main.js"></script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<body>
<h1>Welcome to Chat</h1>
<div class="container">
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-comment"></span> Chat
                    <div class="btn-group pull-right" id="options-btn">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </button>
                        <ul class="dropdown-menu slidedown">
                            <li id="online-users"><a href="#"><span class="glyphicon glyphicon-off"></span>
                                 Online Users</a></li>
                             <li id="chat-screen"><a href="#"><span class="glyphicon glyphicon-off"></span>
                             Chat screen</a></li>
                            <li id="signout-btn"><a href="#"><span class="glyphicon glyphicon-off"></span>
                                Sign Out</a></li>
                                
                        </ul>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="chat">
                   		   <div class="input-group set-username">
	                        	<input id="txt-username" type="text" class="form-control chat-message input-sm" placeholder="Type your username here..." />
	                       		<span class="input-group-btn">
	                            <button class="btn btn-warning btn-sm" id="btn-username">
	                                Save</button>
	                        	</span>                     
                    		</div>
                    </ul>
                </div>
                <div class="panel-footer">
                    <form id="chat_form">

                    <div class="input-group send-message">
                        <input id="btn-input" type="text" class="form-control chat-message input-sm" placeholder="Type your message here..." />
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                Send</button>
                        </span>
                      
                    </div>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>