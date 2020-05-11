<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PatientDetails;
use App\Entity\Prescriptions;
use App\Entity\Admin;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PharmacyController extends AbstractController
{
    
	 /**
     * @Route("/pending", name="pending")
     */
    public function pending() 
    {		
		$prescriptions = $this->getDoctrine()->getRepository(Prescriptions::class);		
		
		$pending = $prescriptions->findBy(['status' => 'pending']);		
		
		return $this->render('pharmacy/pending.html.twig', array('pending' => $pending));
    }
	
	/**
     * @Route("/inprogress", name="inprogress")
     */
    public function inprogress() 
    {		
		$prescriptions = $this->getDoctrine()->getRepository(Prescriptions::class);		
		
		$inp = $prescriptions->findBy(['status' => 'in progress']);		
		
		return $this->render('pharmacy/inprogress.html.twig', array('inp' => $inp));
    }
	
	/**
     * @Route("/ready", name="ready")
     */
    public function ready() 
    {		
		$prescriptions = $this->getDoctrine()->getRepository(Prescriptions::class);		
		
		$ready = $prescriptions->findBy(['status' => 'ready']);		
		
		return $this->render('pharmacy/complete.html.twig', array('ready' => $ready));
    }
	
	/**
     * @Route("/all", name="all")
     */
    public function all() 
    {		
		$prescriptions = $this->getDoctrine()->getRepository(Prescriptions::class);		
		
		$all = $prescriptions->findAll();		
		
		return $this->render('pharmacy/all.html.twig', array('all' => $all));
    }
	
	/**
     * @Route("/update2inprogress", name="update2inprogress")
     */
    public function update2inprogress(SessionInterface $session) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$selected = $request->request->get('selected', 'none');
		
		$entitymanager = $this->getDoctrine()->getManager();	
		
		$repository = $entitymanager->getRepository(Prescriptions::class);		
		
		//searches the table based on the script ID
		$script = $repository->findOneBy(['id' => $selected]);
		
		//sets the assigned variable to record what staff member updated the script
		$assigned = $session->get('sname');
		
		//new timestamp variable to show when the script was updated
		$updated = new \Datetime;
		
		//formats datetime object into string for easy reading
		$time = $updated->format('H:i');
		$date = $updated->format('d/m/Y');
		
		//retrieves the current trail html from the database and appends an update to it.
		$trail = $script->getTrail();
		$trail .= '<p>Marked "In Progress" by '.$assigned.' on '.$date.' @ '.$time.'.</p>';
		
		$script->setStatus("in progress");
		$script->setUpdated($updated);
		$script->setAssigned($assigned);
		$script->setTrail($trail);
		
		$entitymanager->flush();
		
		return new Response('The prescription #'.$selected.' has been marked as "In Progress"');
    }
	
	/**
     * @Route("/update2ready", name="update2ready")
     */
    public function update2ready(SessionInterface $session, MailerInterface $mailer) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$selected = $request->request->get('selected', 'none');
		
		$entitymanager = $this->getDoctrine()->getManager();	
		
		$repository = $entitymanager->getRepository(Prescriptions::class);		
		
		$script = $repository->findOneBy(['id' => $selected]);
		
		
		//sets the assigned variable to record what staff member updated the script
		$assigned = $session->get('sname');
		
		//new timestamp variable to show when the script was updated
		$updated = new \Datetime;
		
		//formats datetime object into string for easy reading
		$time = $updated->format('H:i');
		$date = $updated->format('d/m/Y');
		
		//retrieves the current trail html from the database and appends an update to it.
		$trail = $script->getTrail();
		$trail .= '<p>Marked "Ready" by '.$assigned.' on '.$date.' @ '.$time.'.</p>';		
		
		$script->setStatus("ready");
		$script->setAssigned($assigned);
		$script->setCompleted($updated);	
		$script->setTrail($trail);		
		
		$entitymanager->flush();
		
		//get the patient ID of the selected script 
		$pid = $script->getPatientId();
		
		//query patient details table now
		$repository = $entitymanager->getRepository(PatientDetails::class);	
		$patient = $repository->findOneBy(['id' => $pid]);
		
		$name = $patient->getFname();
		$address = $patient->getEmail();
		
		$email = (new Email())
            ->from('info@grainnespharmacy.com')
            ->to($address)            
            ->subject('Prescription #'.$selected.' is ready to collect')
            ->text('Dear '.$name.'. '.$assigned.' has finished dispensing prescription #'.$selected.' and it is now ready for collection.')
            ->html('<p>Dear '.$name.'</p><p>'.$assigned.' has finished dispensing prescription #'.$selected.' and it is now ready for collection.');

		$mailer->send($email);
		
		
		return new Response('The prescription #'.$selected.' has been marked as "Ready for collection"');
    }
	
		/**
     * @Route("/update2collected", name="update2collected")
     */
    public function update2collected(SessionInterface $session) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$selected = $request->request->get('selected', 'none');
		
		$entitymanager = $this->getDoctrine()->getManager();	
		
		$repository = $entitymanager->getRepository(Prescriptions::class);		
		
		$script = $repository->findOneBy(['id' => $selected]);
		
		//get the patient ID of the selected script 
		$pid = $script->getPatientId();
		
		//query patient details table now
		$repository = $entitymanager->getRepository(PatientDetails::class);	
		$patient = $repository->findOneBy(['id' => $pid]);
		
		//get the patients full name
		$name = $patient->getFname();		
		
		//sets the assigned variable to record what staff member updated the script
		$assigned = $session->get('sname');
		
		//new timestamp variable to show when the script was updated
		$updated = new \Datetime;
		
		//formats datetime object into string for easy reading
		$time = $updated->format('H:i');
		$date = $updated->format('d/m/Y');
		
		//retrieves the current trail html from the database and appends an update to it.
		$trail = $script->getTrail();
		$trail .= '<p>Given to '.$name.' on '.$date.' @ '.$time.' by '.$assigned.'.</p>';
		
		$script->setStatus("collected");
		$script->setAssigned($assigned);
		$script->setUpdated($updated);
		$script->setTrail($trail);
		
		
		$entitymanager->flush();
		
		return new Response('The prescription #'.$selected.' has been marked as "Collected by customer"');
    }
	
	/**
     * @Route("/patients", name="patients")
     */
    public function patients() 
    {		
		$patients = $this->getDoctrine()->getRepository(PatientDetails::class);		
		
		$all = $patients->findAll();		
		
		return $this->render('pharmacy/patients.html.twig', array('all' => $all));
    }
	
	/**
     * @Route("/singlepatient", name="singlepatient")
     */
    public function singlepatient(SessionInterface $session) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$id = $request->request->get('selected', 'none');
		
		//prepare the patient details table
		$repository = $this->getDoctrine()->getRepository(PatientDetails::class);	
		
		//searches the table based on the patient ID
		$patient = $repository->findOneBy(['id' => $id]);
		
		//create variables for patient details
		$name = $patient->getFname().' '.$patient->getLname();
		$dob = $patient->getDob();
		$address1 = $patient->getAddress1();
		$address2 = $patient->getAddress2();
		$town = $patient->getTown();
		$county = $patient->getCounty();
		$sex = $patient->getSex();
		$nationality = $patient->getNationality();
		$doctor = $patient->getDoctor();
		$scheme = $patient->getScheme();
		$schemeid = $patient->getSchemeid();
		$allergies = $patient->getAllergies();
		
		$html = '<a href="#one" class="ui-btn ui-shadow ui-corner-all">Back to All Patients</a>';
		$html .= '<h1>Profile of '.$name.'</h1>';
		$html .= '<div class="container">';	
		$html .= '<div class="left">';
		$html .= '<h4>Date of birth:</h4><p>'.$dob.'</p>';
		$html .= '<h4>Address:</h4><p>'.$address1.'</p><p>'.$address2.'</p><p>'.$town.'</p><p>'.$county.'</p>';
		$html .= '<h4>Sex:</h4><p>'.$sex.'</p>';
		$html .= '<h4>Nationality:</h4><p>'.$nationality.'</p>';
		$html .= '</div>';
		$html .= '<div class="right">';
		$html .= '<h4>Doctor:</h4><p>'.$doctor.'</p>';
		$html .= '<h4>Scheme:</h4><p>'.$scheme.'</p>';
		$html .= '<h4>Scheme ID:</h4><p>'.$schemeid.'</p>';
		$html .= '<h4>Allergies:</h4><p>'.$allergies.'</p>';
		$html .= '</div></div>';		
		
		//prepare the prescriptions table
		$repository = $this->getDoctrine()->getRepository(Prescriptions::class);	
		
		//searches the table where patient_id = $id
		$script = $repository->findBy(['patient_id' => $id]);
		
		$html .= '<h3>Prescription History</h3>';
		$html .= '<table class="center" data-mode="reflow" class="ui-responsive">';
		$html .= '<thead><tr><th>Reference Number</th><th>Patient ID</th><th>Prescription</th><th>Status</th><th>Audit Trail</th></tr></thead><tbody>';
		
		foreach($script as $row)
		{
			$html .= '<tr>';
			$html .= '<td>'.$row->getId().'</td>';
			$html .= '<td>'.$row->getPatientId().'</td>';
			$html .= '<td><a href="'.$row->getImage().'" target="_blank"><img src="'.$row->getImage().'" width="50" height="50"></td>';
			$html .= '<td>'.$row->getStatus().'</td>';
			$html .= '<td>'.$row->getTrail().'</td>';
			$html .= '</tr>';
		}
		
		$html .= '</tbody></table>';		
		
		return new Response($html);
    }
	
    	/**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer) //tutorial found on https://symfonycasts.com/screencast/mailer/config-mailcatcher
    {
        $email = (new Email())
            ->from('info@grainnespharmacy.com')
            ->to('test@gmail.com')            
            ->subject('Prescription Received')
            ->text('We have received your prescription')
            ->html('<p>We have received your prescription</p>');

        $mailer->send($email);
		
		return new Response('<html><body><p>Sent</p></body></html>');
    }
}
