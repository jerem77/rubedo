<?php

class Application_Form_ClientPayboxForm extends Zend_Form
{
    public function init()
    {
       	$this->setMethod('post' );

		$gender = new Zend_Form_Element_Select('gender');
		$gender	->addMultiOptions(array('docteur'=> 'Docteur', 'madame'=> 'Madame', 'monsieur'=> 'Monsieur', 'professeur'=> 'Professeur',));
		$this->addElement($gender);

		$name = new Zend_Form_Element_Text('name');
		$name	->setRequired(true)
		       	->setAutoInsertNotEmptyValidator(false)
		       	->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($name);
		
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname	->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false)
		          	->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($firstname);
		
		$address = new Zend_Form_Element_Text('address');
		$address	->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false)
		          	->addValidator('Alnum', false, array('allowWhiteSpace' => true));
		$this->addElement($address);
		
		$postalCode = new Zend_Form_Element_Text('postalCode');
		$postalCode	->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false)
		          	->addValidator('Digits', false, array('allowWhiteSpace' => true));
		$this->addElement($postalCode);
	
		$city = new Zend_Form_Element_Text('city');
		$city	->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false)
		          	->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($city);
		
		$country = new Zend_Form_Element_Text('country');
		$country	->setRequired(true)
		          	->setAutoInsertNotEmptyValidator(false)
		          	->addValidator('Alpha', false, array('allowWhiteSpace' => true));
		$this->addElement($country);
		
		$officeTelNumber = new Zend_Form_Element_Text('officeTelNumber');
		$officeTelNumber	->setAutoInsertNotEmptyValidator(false)
		          			->addValidator('Digits');
		$this->addElement($officeTelNumber);
		
		$mobilePhoneNumber = new Zend_Form_Element_Text('mobilePhoneNumber');
		$mobilePhoneNumber	->setAutoInsertNotEmptyValidator(false)
		          			->addValidator('Digits');
		$this->addElement($mobilePhoneNumber);
		
		$email = new Zend_Form_Element_Text('email');
		$email	->setRequired(true)
		        ->setAutoInsertNotEmptyValidator(false)
		        ->addValidator('EmailAddress', false, array('allowWhiteSpace' => true));
		$this->addElement($email);
		
		$activity = new Zend_Form_Element_Select('activity' );
		$activity	->addMultiOptions(array('assitante dentaire'=>'Assistante Dentaire','chirurgien maxillo-facial'=>'Chirurgien Maxillo-facial','etudiant'=>'Etudiant','implantologiste'=>'Implantologiste','omnipraticien'=>'Omnipraticien','parodontologiste'=>'Parodontologiste',));
		$this->addElement($activity);
		
		$student = new Zend_Form_Element_Text('student');
		$student	->setAutoInsertNotEmptyValidator(false)
		        	->addValidator('Alnum', false, array('allowWhiteSpace' => true));
		$this->addElement($student);
		
		$studentGraduationYear = new Zend_Form_Element_Select('studentGraduationYear' );
		$studentGraduationYear	->addMultiOptions(array('' => '', '5ème année'=>'5ème année','6ème année'=> '6ème année',));
		$this->addElement($studentGraduationYear);
		
		$billingAddress = new Zend_Form_Element_Checkbox('billingAddress');
		$this->addElement($billingAddress);
		
		$payment = new Zend_Form_Element_Radio('payment');
		$payment	-> addMultiOptions(array('card' => 'Paiement en ligne',	'check' => 'Chèque'))
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