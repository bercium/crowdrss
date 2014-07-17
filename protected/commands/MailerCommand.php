<?php

class MailerCommand extends CConsoleCommand{

	public function actionDigest(){
    
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = "New crowdfunding projects for you";  // 11.6. title change
    $message->from = Yii::app()->params['noreplyEmail'];
    
    // send newsletter to all in waiting list
    $invites = Invite::model()->findAll("NOT ISNULL(`key`)");
    foreach ($invites as $user){
      
      $create_at = strtotime($user->time_invited);
      if ($create_at < strtotime('-8 week') || $create_at >= strtotime('-1 day')) continue;     
      if (!
          (($create_at >= strtotime('-1 week')) || 
          (($create_at >= strtotime('-4 week')) && ($create_at < strtotime('-3 week'))) || 
          (($create_at >= strtotime('-8 week')) && ($create_at < strtotime('-7 week'))) )
         ) continue;      
      
      //set mail tracking
      $mailTracking = mailTrackingCode($user->id);
      $ml = new MailLog();
      $ml->tracking_code = mailTrackingCodeDecode($mailTracking);
      $ml->type = 'invitation-reminder';
      $ml->user_to_id = $user->id;
      $ml->save();
    
      //$activation_url = '<a href="'.absoluteURL()."/user/registration?id=".$user->key.'">Register here</a>';
      $activation_url = mailButton("Register here", absoluteURL()."/user/registration?id=".$user->key,'success',$mailTracking,'register-button');
      $content = "This is just a friendly reminder to activate your account on Cofinder.
                  <br /><br />
                  Cofinder is a web platform through which you can share your ideas with the like minded entrepreneurs, search for people to join your project or join an interesting project yourself.
                  <br /><br />If we got your attention you can ".$activation_url."!";
      
      $message->setBody(array("content"=>$content,"email"=>$user->email,"tc"=>$mailTracking), 'text/html');
      $message->setTo($user->email);
      Yii::app()->mail->send($message);
    }
  }
    
}