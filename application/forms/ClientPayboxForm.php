<?php

class Application_Form_ClientPayboxForm extends Zend_Form
{
    public function init()
    {
       	$this->setMethod('post' );

		$gender = new Zend_Form_Element_Select('gender');
		$gender	->addMultiOptions(array('docteur'=> 'Docteur', 'madame'=> 'Madame', 'monsieur'=> 'Monsieur', 'professeur'=> 'Professeur',))
				->setLabel('Titre');
		$this->addElement($gender);

		$name = new Zend_Form_Element_Text('name');
		$name	->setLabel('Nom')
				->setRequired(true)
		       	->setAutoInsertNotEmptyValidator(false);
		       	//->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($name);
		
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname	->setLabel('Prénom')
					->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false);
		          	//->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($firstname);
		
		$address = new Zend_Form_Element_Text('address');
		$address	->setLabel('Adresse')
					->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false);
		          	//->addValidator('Alnum', false, array('allowWhiteSpace' => true));
		$this->addElement($address);
		
		$postalCode = new Zend_Form_Element_Text('postalCode');
		$postalCode	->setLabel('Code Postal')
					->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false)
		          	->addValidator('Digits', false, array('allowWhiteSpace' => true));
		$this->addElement($postalCode);
	
		$city = new Zend_Form_Element_Text('city');
		$city	->setLabel('Ville')
				->setRequired(true)
		       	->setAutoInsertNotEmptyValidator(false);
		       	//->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($city);
		
		$country = new Zend_Form_Element_Text('country');
		$country	->setLabel('Pays')
					->setValue('France')
					->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false);
		          	//->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($country);
		
		$officeTelNumber = new Zend_Form_Element_Text('officeTelNumber');
		$officeTelNumber	->setLabel('Téléphone Cabinet')
							->setAutoInsertNotEmptyValidator(false);
		          			//->addValidator('Digits');
		$this->addElement($officeTelNumber);
		
		$mobilePhoneNumber = new Zend_Form_Element_Text('mobilePhoneNumber');
		$mobilePhoneNumber	->setLabel('Téléphone Portable')
							->setAutoInsertNotEmptyValidator(false);
		          			//->addValidator('Digits');
		$this->addElement($mobilePhoneNumber);
		
		$email = new Zend_Form_Element_Text('email');
		$email	->setLabel('E-mail')
				->setRequired(true)
		        ->setAutoInsertNotEmptyValidator(false)
		        ->addValidator('EmailAddress', false);
		$this->addElement($email);
		
		$activity = new Zend_Form_Element_Select('activity' );
		$activity	->setLabel('Activité')
					->addMultiOptions(array('assistante dentaire'=>'Assistante Dentaire','chirurgien maxillo-facial'=>'Chirurgien Maxillo-facial','etudiant'=>'Etudiant','implantologiste'=>'Implantologiste','omnipraticien'=>'Omnipraticien','parodontologiste'=>'Parodontologiste',));
		$this->addElement($activity);
		
		$student = new Zend_Form_Element_Text('student');
		$student	->setLabel('Faculté')
					->setAutoInsertNotEmptyValidator(false);
		        	//->addValidator('Alnum', false, array('allowWhiteSpace' => true));
		$this->addElement($student);
		
		$studentGraduationYear = new Zend_Form_Element_Select('studentGraduationYear' );
		$studentGraduationYear	->setLabel('Année suivie')
								->addMultiOptions(array('' => '', '5ème année'=>'5ème année','6ème année'=> '6ème année',));
		$this->addElement($studentGraduationYear);
		
		$billingAddress = new Zend_Form_Element_Checkbox('billingAddress');
		$billingAddress ->setLabel('Adresse de facturation');
		$this->addElement($billingAddress);
		
		$payment = new Zend_Form_Element_Radio('payment');
		$payment	->setLabel('Type de paiement')
					-> addMultiOptions(array('card' => 'Paiement en ligne',	'check' => 'Chèque'))
					-> setValue('card');
		$this->addElement($payment);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit -> setLabel('Valider')
				-> setName('submit');
		$this->addElement($submit);
		
		foreach($this->getElements() as $element) 
        {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('Label');
        }
    }
}