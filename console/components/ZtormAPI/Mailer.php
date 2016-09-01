<?php
Yii::import('ext.swiftMailer.SwiftMailer');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mailer
 *
 * @author helenk
 */
class Mailer  {
    //put your code here
    /**
     
	 * Mail file to recipient list
	 *
	 * @param mixed $filename Array of filenames or a string giving single File to attach to email
	 * @param array $recipients Array of CActiveRecord objects
	 */
    
        const ADDRESS1 = 'claimmyprize@exertismicro-p.co.uk';
        //const ADDRESS1 = 'helen.kappes@exertismicro-p.co.uk';
        public function mailNotification($content){
            
            $filename = array();
            $recipients = array();
            $recipients[] = self::ADDRESS1;
            $content = preg_replace('/[^A-Za-z0-9\. -]/', '', $content);
            $this->phpmailfile($filename,$recipients,$content,$filetype='');
            
        }
        
      
        /*
         * Mail file content to specified recipients
         */
        private function phpmailfile($filename,array $recipients,$content, $filetype=''){
            try{
                
                $addresses = array();
                $subject = "April incentive winner";
                $from = "From: WebTeam\n";
                //mail('helen.kappes@exertismicro-p.co.uk',$subject,$content,"From: WebTeam\n");
                //mail('helen.kappes@exertismicro-p.co.uk','this is test3',$content,$from);
                //mail('helen.kappes@exertismicro-p.co.uk',$subject,$content,"From: WebTeam\n");
                //mail(self::ADDRESS1,'April incentive winner2',$content,"From: WebTeam\n");
                if (!empty($recipients)) {
                    // get email addresses
                    foreach($recipients as $address) {
                            $addresses[] =  $address;//$address->emailAddress->email;
                    }
        
                   foreach($addresses as $email_address) {
                        mail($email_address,$subject,$content,$from);
                        
                   }
                }
                }//try
                    catch(Exception $e){
                        Yii::log(__METHOD__.': mail could not be sent','info','system.controllers.Winningnumber');
                
                    }
        
        
            }//end phpmailfile
        
        
        /*
         * Uses swift mail to mail recipient - Not currently in use
         */
	private function swiftmailfile($filename,array $recipients,$content, $filetype='')
	{
		$addresses = array();
                

                if (!empty($recipients)) {

                    foreach($recipients as $address) {
                            $addresses[] =  $address;//$address->emailAddress->email;
                    }

                    if(count($addresses) > 0) {

                            Yii::import('ext.swiftMailer.SwiftMailer');

                            // Mail config
                            //$mail_host = 'baspop';
                            $mail_host = 'localhost';
                            $mail_port = 25; // Optional

                            // Swift mailer object and transport
                            $swift_mailer = Yii::app()->swiftMailer;
                            $mail_transport = $swift_mailer->smtpTransport($mail_host, $mail_port);

                            // Set content for email
                            $content = $content;
                            $subject = "April Incentive Winner prize claimed today";

                            $sentto = array(); // record successful messages
                            foreach($addresses as $email_address) {

                                    // Create mailer
                                    $mailer = $swift_mailer->mailer($mail_transport);

                                    // Create message
                                    $message = $swift_mailer->newMessage($subject)
                                    ->setFrom(array('no-reply@micro-p.com' => 'Micro-P Webteam'))
                                    ->setTo(array($email_address => $email_address))
                                    ->setBody($content);
                                    if (is_array($filename)) {
                                            foreach($filename as $f) {
                                                    $message->attach(Swift_Attachment::fromPath(Yii::app()->getBasePath()."/../uploads/outgoing".'/'.str_replace(' ', '_', $f)));
                                            }
                                    } else {
                                            $message->attach(Swift_Attachment::fromPath(Yii::app()->getBasePath()."/../uploads/outgoing".'/'.str_replace(' ', '_', $filename)));
                                    }

                                    // Send mail
                                    try{
                                    $result =0;
                                    $result = $mailer->send($message);
                                    }
                                    catch(Exception $e){
                                        Yii::log(__METHOD__.': mail could not be sent','info','Mailer');
                                    }
                                    if ($result>0)
                                            $sentto[] = $email_address;
                            } // foreach


                    }
                    return (count($sentto)>0 ? $sentto : FALSE);
                } else {
                    Yii::log(__METHOD__.': No recipients for filetype '.$filetype,  CLogger::LEVEL_ERROR, 'system.components.outputfilewriter');
                    return FALSE;
                }
	} // mailFile
}

?>
