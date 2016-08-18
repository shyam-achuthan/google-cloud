<?php 
namespace \GoogleCloud;

/**
* 
*/
class Pubsub 
{
	private $auth_json = null;
	private $project_id = null;
	private $client = null;
	private $service = null;
	private $topic = null;
	private $subscription = null;

	function __construct($project_id='',$auth_json='')
	{
		$this->$auth_json = $auth_json;
		$this->project_id = $project_id;

        $this->client = new \Google_Client();
        $this->client->setApplicationName($project_id);
        $this->client->setAuthConfig($this->$auth_json);
        $this->client->useApplicationDefaultCredentials();
        $this->client->addScope('https://www.googleapis.com/auth/cloud-platform');
        $this->service = new \Google_Service_Pubsub($this->client);
	}

	public function sendMessage($message_title='',$data=[])
	{
		  $postBody = new \Google_Service_Pubsub_PublishRequest($this->client);
          $message = new \Google_Service_Pubsub_PubsubMessage();
          $message->setData(base64_encode($message_title));
		  
		  $message->setAttributes($data);

          $postBody->setMessages([$message]);
          $response = $this->service->projects_topics->publish($this->getQueueTopicUrl(), $postBody);

          return $response;
	}

	

	public function receiveMessages(){

		  $postBody = new \Google_Service_Pubsub_PullRequest($this->client);
	      $postBody->setMaxMessages(20);
	      $postBody->setReturnImmediately(true);

	      $response = $this->service->projects_subscriptions->pull($this->getQueueSubscriptionUrl(), $postBody);

	      $messages =[];
	      foreach($response->getReceivedMessages() as $message){
	        $msg = $message->getMessage();

	        $new_msg = new \stdClass();
	        $new_msg->message_title = base64_decode($msg->getData());
	        $new_msg->data = $msg->getAttributes();
	        $new_msg->handle = $message->getAckId();

	        $messages[] = $new_msg;
	      }
	      return $messages;
	}

	public function deleteMessage($ackId=''){
		return $this->deleteMessages([$ackId]);
	}

	public function deleteMessages($ackIds=[]){
		  $postBody = new \Google_Service_Pubsub_AcknowledgeRequest($this->client);
          $postBody->setAckIds($ackIds);
          $response = $this->service->projects_subscriptions->acknowledge($this->getQueueSubscriptionUrl(), $postBody);
          return $response;
	}

	private function getQueueTopicUrl(){
		return "projects/{$this->project_id}/topics/".$this->topic;
	}

	private function getQueueSubscriptionUrl(){
		return "projects/{$this->project_id}/subscriptions/".$this->subscription;
	}

	public function setTopic($topic_name=''){
		$this->topic = $topic_name;
	}

	public function setSubscription($subscription=''){
		$this->subscription = $subscription;
	}


}