<?php

class Application_Form_ClientPayboxForm extends Zend_Form
{
    public function init()
    {
       	$this->setMethod('post');
		//$this->setAttrib('action', '/blocks/paybox/');
		
		//Modification du décorateur
		$this->setDecorators(
		    array(
		        'FormElements',
		        array('HtmlTag', array('tag' => 'table')),
		        'Form'
		    )
		);

		$decorateur = array(
		    'ViewHelper',
		    'Errors',
		    array('Description', array('tag' => 'p', 'class' => 'description')),
		    array('HtmlTag', array('tag' => 'td')),
		    array('Label', array('tag' => 'th')),
		    array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
		);

		$this->addElement(	'select', 'gender', array(
            				'label'      	=> 'Titre :',
            				'decorators' 	=> $decorateur,
            				'multiOptions'	=> array(
            					'mister' 	=> 'Monsieur', 
            					'madam' 	=> 'Madame',
							)
		));
	
		$this->addElement(	'text', 'name', array(
            				'label'      	=> 'Nom :',
            				'decorators' 	=> $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'firstname', array(
            				'label'      => 'Prénom :',
            				'decorators' => $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'address', array(
            				'label'      => 'Adresse :',
            				'decorators' => $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'alnum',
				            )
		));
		
		$this->addElement(	'text', 'postalCode', array(
            				'label'      => 'Code postale :',
            				'decorators' => $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'digits',
				            )
		));
	
		$this->addElement(	'text', 'city', array(
            				'label'      => 'Ville :',
            				'decorators' => $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'country', array(
            				'label'      => 'Pays :',
            				'decorators' => $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'alpha',
				            )
		));
		
		$this->addElement(	'text', 'officeTelNumber', array(
            				'label'      => 'Téléphone cabinet :',
            				'decorators' => $decorateur,
		));
		
		$this->addElement(	'text', 'mobilePhoneNumber', array(
            				'label'      => 'Téléphone portable :',
            				'decorators' => $decorateur,
		));
		
		$this->addElement(	'text', 'email', array(
            				'label'      => 'E-mail :',
            				'decorators' => $decorateur,
            				'required'  	=> true,
            				'validators' => array(
				            	'EmailAddress',
				            )
		));
		
		$this->addElement(	'select', 'activity', array(
            				'label'      => 'Activité :',
            				'decorators' => $decorateur,
            				'multiOptions' => array(
            					'assitance dentaire' => 'Assistante Dentaire', 
            					'dentiste' => 'Dentiste',
							)
		));
		
		$this->addElement(	'text', 'diploma', array(
            				'label'      => 'Diplôme :',
            				'decorators' => $decorateur,
            				'validators' => array(
				            	'alnum',
				            )
            				
		));
		
		$this->addElement(	'text', 'student', array(
            				'label'      => 'Si vous êtes étudiant, nom de la faculté :',
            				'decorators' => $decorateur,
            				'validators' => array(
				            	'alnum',
				            )
		));
		
		$this->addElement(	'text', 'studentGraduationYear', array(
            				'label'      => 'Année suivie :',
            				'decorators' => $decorateur,
            				'validators' => array(
				            	'digits',
				            )
		));
		
		$this->addElement(	'checkbox', 'billingAddress', array(
            				'label'      => 'Merci de cocher cette case si l\'adresse de facturation est différente de celle renseignée ci-dessus',
            				'decorators' => $decorateur,
		));
		
		$this->addElement(	'radio', 'payment', array(
            				'decorators' => $decorateur,
            				'multiOptions' => array('card' => 'Paiement en ligne', 'check' => 'Chèque')
		));

		$this->addElement('submit', 'submit', array(
            'label'    => 'Valider',
            'name'	   => 'submit',
            'decorators' => array(
	            'ViewHelper',
	            array(array('td' => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2)),
	            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
	        ),
        ));
    }
}