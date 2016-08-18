# GoogleCloud

An easy to use platform for google pubsub on php

composer require shyam-achuthan/google-cloud

### Initializing the library

        $projectId = "<your-google-project-id>";
        $key_file_location = "<path-to-service-account-json-credentials";
        
        $pubsub = new \GoogleCloud\Pubsub($projectId,$key_file_location);
        $pubsub->setTopic("<your-message-topic>");
        $pubsub->setSubscription("<your-pull-subscription-name>");
        
        
### Sending a message to the queue

          $data = ["name"=>"Shyam",'email'=>"shyam@shyamachuthan.com" ]; // string key value pair data to pass to the queue receiver
          $message = "SEND_USER_WELCOME_EMAIL"; // any text message here am using this string to differentiate the actions on the receiver side
          $response = $pubsub->sendMessage($message,$data);
          $resp = $response;
          
### Receiving queue messages

        $resp = $pubsub->receiveMessages(); 

will return Array of message objects which has three attributes
    -   message_title - [String] the message passed to send message while sending
    -   data    - [Array] array of data ( key value pair as passed in sending message )
    -   handle  - [String] unique identifier of the message, the acknoledgement-id which is used to delete the message
    
### Delete a message

        $response = $pubsub->deleteMessage($message->handle);
        
### Receive message do execution and delete message

        $resp = $pubsub->receiveMessages(); 
        foreach($resp as $message){
            // Do what ever executin needed with $message->message_title, $message->data
            $pubsub->deleteMessage($message->handle);
        }

### Delete multiple messages together

        $pubsub->deleteMessage($handles); // where handles is array of handle/acknowledgement-ids