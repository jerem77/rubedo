<?php

class Application_Form_ClientPayboxForm extends Zend_Form
{
    public function init()
    {
       	$this->setMethod('post');

		$this->addElement(	'select', 'gender', array(
            				'multiOptions'	=> array(
            					'docteur'		=> 'Docteur',
            					'madame' 		=> 'Madame',
            					'monsieur' 		=> 'Monsieur', 
            					'professeur' 	=> 'Professeur',
							)
		));
	
		$this->addElement(	'text', 'name', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'firstname', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'address', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'alnum',
				            )
		));
		
		$this->addElement(	'text', 'postalCode', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'digits',
				            )
		));
	
		$this->addElement(	'text', 'city', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'country', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'officeTelNumber', array(
		));
		
		$this->addElement(	'text', 'mobilePhoneNumber', array(
		));
		
		$this->addElement(	'text', 'email', array(
            				'required'  	=> true,
            				'validators' => array(
				            	'EmailAddress',
				            )
		));
		
		$this->addElement(	'select', 'activity', array(
            				'multiOptions' => array(
            					'assitante dentaire' 		=> 'Assistante Dentaire', 
            					'chirurgien maxillo-facial' => 'Chirurgien Maxillo-facial',
            					'etudiant' 					=> 'Etudiant',
            					'implantologiste' 			=> 'Implantologiste',
            					'omnipraticien'				=> 'Omnipraticien',
            					'parodontologiste'			=> 'Parodontologiste',
							)
		));
		
		$this->addElement(	'text', 'student', array(
            				'validators' => array(
				            	'alnum',
				            )
		));
		
		$this->addElement(	'select', 'studentGraduationYear', array(
            				'multiOptions' => array(
            					'5ème année'	=> '5ème année', 
            					'6ème année'	=> '6ème année',
							)
		));
		
		$this->addElement(	'checkbox', 'billingAddress', array());
		
		$this->addElement(	'radio', 'payment', array(
							'required'		=> true,
            				'multiOptions' 	=> array(
            					'card' => 'Paiement en ligne',
            					'check' => 'Chèque'
							),
							'value'			=> 'card',
		));

		$this->addElement('submit', 'submit', array(
            'label'    => 'Valider',
            'name'	   => 'submit',
        ));
		
		foreach($this->getElements() as $element) 
        {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('Label');
			$element->removeDecorator('Errors');
        }
    }
}