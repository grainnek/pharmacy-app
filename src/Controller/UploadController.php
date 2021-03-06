<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Prescriptions;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UploadController extends AbstractController
{
   /**
    * @Route("/uploader")
    */
    public function upload(SessionInterface $session, MailerInterface $mailer)
    {
		$target_dir = "images/scripts/"; //directory where images will be saved in public folder
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1; //we assume at the start file is an image and ok to upload
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		
		$error = '<ul>'; //we are going to append errors to this variable
		
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) 
		{
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) 
			{				
				$uploadOk = 1;
			} 
			else 
			{
				$error .= '<li>The file is not an image.</li>'; //adds error as a list item
				$uploadOk = 0;
			}
		}
		// Check if file already exists
		if (file_exists($target_file)) 
		{
			$error .= '<li>The file already exists.</li>';			
			$uploadOk = 0;
		}
		
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 5000000) 
		{
			$error .= '<li>The file is too large.</li>';			
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") 
		{
			$error .= '<li>The file must be in JPG, JPEG or PNG format.</li>';
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) 
		{
			$error .= '</ul>'; //closes the list
			
			$message = '<h2>Upload Failed.</h2><p>Sorry, your file was not uploaded for the following reasons:</p>'.$error; //adds the error list to the end of this message variable

			$response = '<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
						<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
						<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
						<div data-role="page" id="one">
						<div role="main" class="ui-content">'
						.$message.'
						<br><a href="/up">Please try again.</a></div></div>';				
			
			return new Response($response);
		} 
		// if everything is ok, try to upload file
		else 
		{
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
			{
				$image = basename( $_FILES["fileToUpload"]["name"]);
				$path = $target_dir.$image;		
				
				$entityManager = $this->getDoctrine()->getManager();
				
				$pid = $session->get('id'); //get the patient id
				$timestamp = new \Datetime;
				
				$prescription = new Prescriptions();
				
				//format the datetime variable into printable strings
				$time = $timestamp->format('H:i');
				$date = $timestamp->format('d/m/Y');
			
				$prescription->setPatientid($pid);
				$prescription->setImage($path);
				$prescription->setStatus('pending');
				$prescription->setTimestamp($timestamp);
				$prescription->setTrail('<p>Submitted by '.$session->get('fname').' '.$session->get('lname').' on '.$date.' @ '.$time.'</p>');
				
				$entityManager->persist($prescription);
		
				$entityManager->flush();
				
				
				//after data uploads, create an email and send it to the customer to acknmowledge receipt
				$email = (new Email())
					->from('info@grainnespharmacy.com')
					->to($session->get('email'))            
					->subject('Prescription Received')
					->text('Dear '.$session->get('fname').'. We have received your prescription. Received on '.$date.' at '.$time.'. Kind regards, Grainne\'s Pharmacy')
					->html('<p>Dear '.$session->get('fname').'<br><p>We have received your prescription.</p><p>Received on '.$date.' at '.$time.'.</p><p>Kind regards</p><p>Grainne\'s Pharmacy</p>');

				$mailer->send($email);
				
				//after data uploads, create an email and send it to the pharmacy to notify of new upload
				$email2 = (new Email())
					->from('info@grainnespharmacy.com')
					->to('info@grainnespharmacy.com')           
					->subject('New Prescription Received')
					->text('A prescription has been submitted by'.$session->get('fname').'. Submitted on '.$date.' at '.$time.'.')
					->html('<p>A new prescription has been uploaded by '.$session->get('fname').' '.$session->get('lname').'<br><p>Submitted on '.$date.' at '.$time.'.</p><p><a href="localhost:8000/pending">View here</a></p>');

				$mailer->send($email2);
				
				//creating a response variable which contains javascript and html to create an alert and redirect to the upload page
				//$(document).ready(function) is a function that executes when the page loads instead of when a button is clicked
				$response = '<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
							<script>
								$(document).ready( function() {
									alert("Thank you '.$session->get('fname').'. Your prescription has been received by our system.");
									window.location="/up";
								});
							</script>';				
			
				return new Response($response);
			} 
			else 
			{
				$response = '<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
							<script>
								$(document).ready( function() {
									alert("Sorry '.$session->get('fname').' There was an issue uploading your file. Please try again.");
									window.location="/up";
								});
							</script>';				
			
				return new Response($response);
			}
		}    
    } 
}
