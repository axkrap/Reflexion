<?php
/*

*/
class Email{
	protected $_email_sender;
	protected $_email_receiver;
	protected $_subject;
	protected $_body;
	protected $_name;
	protected $_header_string;
	
	function __construct($email_sender, $email_receiver, $subject, $body, $name) {
		$this->_email_sender = isset($email_sender) ? $email_sender : (array_key_exists('email_sender', $_POST) ? $_POST['email_sender'] : '(no sender)');
		$this->_email_receiver = isset($email_receiver) ? $email_receiver : (array_key_exists('email_receiver', $_POST) ? $_POST['email_receiver'] : 'stop');
		if($this->_email_receiver==='stop'){
			echo 'failure';
		}
		$this->_subject = isset($subject) ? $subject : (array_key_exists('subject', $_POST) ? $_POST['subject'] : '(no subject)');
		$this->_body = isset($body) ? $body : (array_key_exists('body', $_POST) ? $_POST['body'] : 'no body');
		$this->_name = isset($name) ? $name : (array_key_exists('name', $_POST) ? $_POST['name'] : '');
		$this->_header_string = "From: ". $this->_name . " <" . $this->_email_sender . ">\r\n";
		mail($this->_email_receiver, $this->_subject, $this->_body, $this->_header_string);
	}
}