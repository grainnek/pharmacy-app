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
     * @Route("/update_inprogress", name="update_inprogress")
     */
    public function update_inprogress(SessionInterface $session) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$script = $request->request->get('selected', 'none');
		
		$entitymanager = $this->getDoctrine()->getManager();	
		
		$repository = $entitymanager->getRepository(Prescriptions::class);		
		
		$selected = $repository->findOneBy(['id' => $script]);
		
		$assigned = $session->get('sname');
		$updated = new \Datetime;
		
		$selected->setStatus("in progress");
		$selected->setUpdated($updated);
		$selected->setAssigned($assigned);
		
		$entitymanager->flush();
		
		return new Response('The prescription #'.$script.' has been marked as "In Progress"');
    }
	
	/**
     * @Route("/update_ready", name="update_ready")
     */
    public function update_ready(SessionInterface $session, MailerInterface $mailer) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$script = $request->request->get('selected', 'none');
		
		$entitymanager = $this->getDoctrine()->getManager();	
		
		$repository = $entitymanager->getRepository(Prescriptions::class);		
		
		$selected = $repository->findOneBy(['id' => $script]);
		
		$assigned = $session->get('sname');
		$updated = new \Datetime;
		
		//get the patient ID of the selected script
		$pid = $selected->getPatientId();
		
		$selected->setStatus("ready");
		$selected->setAssigned($assigned);
		$selected->setCompleted($updated);		
		
		$entitymanager->flush();
		
		//query patient details table now
		$repository = $entitymanager->getRepository(PatientDetails::class);	
		$patient = $repository->findOneBy(['id' => $pid]);
		
		$name = $patient->getFname();
		$address = $patient->getEmail();
		
		$email = (new Email())
            ->from('info@grainnespharmacy.com')
            ->to($address)            
            ->subject('Prescription #'.$script.' is ready to collect')
            ->text('Dear '.$name.'. '.$assigned.' has finished dispensing prescription #'.$script.' and it is now ready for collection.')
            ->html('<p>Dear '.$name.'</p><p>'.$assigned.' has finished dispensing prescription #'.$script.' and it is now ready for collection.');

		$mailer->send($email);
		
		
		return new Response('The prescription #'.$script.' has been marked as "Ready for collection"');
    }
	
		/**
     * @Route("/update_collected", name="update_collected")
     */
    public function update_collected(SessionInterface $session) //function to update scripts from pending to inprogress
    {
		$request = Request::createFromGlobals();
		//get variables
		$script = $request->request->get('selected', 'none');
		
		$entitymanager = $this->getDoctrine()->getManager();	
		
		$repository = $entitymanager->getRepository(Prescriptions::class);		
		
		$selected = $repository->findOneBy(['id' => $script]);
		
		$assigned = $session->get('sname');
		$updated = new \Datetime;
		
		$selected->setStatus("collected");
		$selected->setAssigned($assigned);
		$selected->setUpdated($updated);		
		
		$entitymanager->flush();
		
		return new Response('The prescription #'.$script.' has been marked as "Collected by customer"');
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
