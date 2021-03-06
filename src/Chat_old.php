<?php
namespace MyApp;
require(__DIR__ .'/database.php');
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
            $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        if($data["type"]=="send") {
            $this->sendMessage($from, $data);
        }
        else if ($data["type"] == "register") {
                $this->clients[$conn] = $data["user_id"];
                $name = htmlspecialchars($data["username"]);
                $this->users[] = array(
                    "name"  => $name,
                    "seen"  => time(),
                    "id" => $data["user_id"],
                );      
        }
        else if ($data["type"]=="online") {
            $clientsD = $this->clients;
            foreach ($this->clients as $client) {
                $this->send($client, "onliners", $client);
            }
           // $this->checkOnliners();
        }
    }
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
       // $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function getUserIdConnections($userId){
        $connections = array();
        foreach ($this->clients as $connection){
            if($this->clients[$connection] == $userId){
                $connections[] = $connection;
            }
        }

        return $connections;
        }   


    private function sendMessage($conn, $data){
        $metaData = array();
        $receiverId = $data['to'];
        $metaData["to"] = $data["to"];
        $metaData["senderId"] = $data["user_id"];
        $metaData["message"] = $data["message"];
        if(empty($receiverId)){
            return;
        }

        $senderId = $this->getUserIdFromConnection($conn);

        $this->sendDataToUser($receiverId, $metaData);
    }

    private function getUserIdFromConnection($conn){
        foreach ($this->clients as $connection){
            if($connection == $conn){
                return $this->clients[$connection];
            }
        }
        return null;
    }

    private function sendDataToUser($userId, $responseInfo){
        $data = $responseInfo;
        $connections = $this->getUserIdConnections($userId);
        foreach ($connections as $index => $connection) {
            $connection->send($data);
        }
    }
    /**
    * Fetch online users
    **/
 public function checkOnliners($curUser = ""){
        date_default_timezone_set("UTC");
        if( $curUser != "" && isset($this->users[$curUser->resourceId]) ){
            $this->users[$curUser->resourceId]['seen'] = time();
        }
        
        // $curtime    = strtotime(date("Y-m-d H:i:s", strtotime('-5 seconds', time())));
        // foreach($this->users as $id => $user){
        //     $usertime   = $user['seen'];
        //     if($usertime < $curtime){
        //         unset($this->users[$id]);
        //     }
        // }
        
        /* Send online users to evryone */
        $data = $this->users;
        foreach ($this->clients as $client) {
            $this->send($client, "onliners", $data);
        }
    }
    
 
/**
* Send function
**/   
public function send($client, $type, $data){
        $send = array(
            "type" => $type,
            "data" => $data
        );
        $send = json_encode($send, true);
        $client->send($send);
 }

public function saveMessageToDb() {
         $query = 'INSERT INTO chatting (`username`, `to`, `from` , `message`, `date`, `seen` ) VALUES ($data["username"],$client, `$from->resourceId`,$data["message"], time() , 0)';
                  echo $query;
                if ($conn->query($query) === TRUE) {
                    echo sprintf("success %d", $query);
                }
}
}



/** Old code **/
// use Ratchet\MessageComponentInterface;
// use Ratchet\ConnectionInterface;
// class Chat implements MessageComponentInterface{
//     private $connections = [];
//     private $users = [];
//      /**
//      * When a new connection is opened it will be passed to this method
//      * @param  ConnectionInterface $conn The socket/connection that just connected to your application
//      * @throws \Exception
//      */
//     function onOpen(ConnectionInterface $conn){
//         $this->connections[$conn->resourceId] = compact('conn') + ['user_id' => null];
//         echo "New connection! ({$conn->resourceId})\n";
//     }   
    
//      /**
//      * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
//      * @param  ConnectionInterface $conn The socket/connection that is closing/closed
//      * @throws \Exception
//      */
//     function onClose(ConnectionInterface $conn){
//         $disconnectedId = $conn->resourceId;
//         unset($this->connections[$disconnectedId]);
//         foreach($this->connections as &$connection)
//             $connection['conn']->send(json_encode([
//                 'offline_user' => $disconnectedId,
//                 'from_user_id' => 'server control',
//                 'from_resource_id' => null
//             ]));
//     }
    
//      *
//      * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
//      * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
//      * @param  ConnectionInterface $conn
//      * @param  \Exception $e
//      * @throws \Exception
     
//     function onError(ConnectionInterface $conn, \Exception $e){
//         $userId = $this->connections[$conn->resourceId]['user_id'];
//         echo "An error has occurred with user $userId: {$e->getMessage()}\n";
//         unset($this->connections[$conn->resourceId]);
//         $conn->close();
//     }
    
//      /**
//      * Triggered when a client sends data through the socket
//      * @param  \Ratchet\ConnectionInterface $conn The socket/connection that sent the message to your application
//      * @param  string $msg The message received
//      * @throws \Exception
//      */
//     function onMessage(ConnectionInterface $conn, $msg){
//         $data = json_decode($msg, true);
//         $onlineUsers = [];

//         if(is_null($this->connections[$conn->resourceId]['user_id'])){
//             $this->connections[$conn->resourceId]['user_id'] = $data["user_id"];
//             foreach($this->connections as $resourceId => &$connection){
//                 $connection['conn']->send(json_encode([$conn->resourceId => $data["user_id"]]));
//                 if($conn->resourceId != $resourceId)
//                     $onlineUsers[$resourceId] = $connection['user_id'];
//             }
//             $conn->send(json_encode(['online_users' => $onlineUsers]));
//         } else{
//             $fromUserId = $this->connections[$conn->resourceId]['user_id'];
//             $msg = json_decode($msg, true);
//             $this->connections[$msg['to']]['conn']->send(json_encode([
//                 'msg' => $msg['message'],
//                 'from_user_id' => $fromUserId,
//                 'from_resource_id' => $conn->resourceId
//             ]));
//         }
//     }
// }