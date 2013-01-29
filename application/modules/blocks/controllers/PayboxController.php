<?php
/**
 * Rubedo
 *
 * LICENSE
 *
 * yet to be written
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license    yet to be written
 * @version    $Id:
 */
Use Rubedo\Services\Manager;

require_once ('AbstractController.php');

/**
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Blocks_PayboxController extends Blocks_AbstractController
{

    protected $_paybox;

    protected $_session;

	/**
     * Get an instance of message
     * 
     * @return Swift_Message
     */
    protected function _getMailObject ()
    {
        return Swift_Message::newInstance();
    }

    /**
     * Send the message
     * 
     * return number of destination message
     * 
     * @param Swift_Message $message
     * @throws Zend_Controller_Exception
     * @return number
     */
    protected function _sendMessage (Swift_Message $message)
    {
        if (! isset($this->_transport)) {
            $options = Zend_Registry::get('swiftMail');
            if (! isset($options['smtp'])) {
                throw new Zend_Controller_Exception('no smtp in configuration');
            }
            $this->_transport = Swift_SmtpTransport::newInstance($options['smtp']['server'], $options['smtp']['port'], $options['smtp']['ssl'] ? 'ssl' : null);
            if (isset($options['smtp']['username'])) {
                $this->_transport->setUsername($options['smtp']['username'])->setPassword($options['smtp']['password']);
            }
        }
        if (! isset($this->_mailer)) {
            $this->_mailer = Swift_Mailer::newInstance($this->_transport);
        }
        
        // Send the message
        return $this->_mailer->send($message);
    }
	
	protected function _sendConfirmationForCBPayment($gender, $name, $firstname, $email) {
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();	
		$message = $this->_getMailObject();
        
		//Set header
        $message->setSubject('VOTRE INSCRIPTION A LA ZID 2013');
		$message->setReplyTo(array('contact@zimmerdental.fr' => 'Zimmer Dental'));
		$message->setReturnPath('contact@zimmerdental.fr');
        $message->setFrom(array('contact@zimmerdental.fr' => 'Zimmer Dental'));
		$message->setSender('contact@zimmerdental.fr');
        $message->setTo(array($email => $gender." ".$firstname." ".$name));
		$message->setBcc(array('nicolas.trenti@webtales.fr' => 'Nicolas TRENTI'));
        //$message->setBcc(array('francoise.trupel@zimmer.com' => 'Françoise TRUPEL'));
	
		//Set body content
		$msgContent = $this->view->render('paybox/confirm-cb.phtml');

		//Send the logo to the view
        $this->view->logo = $message->embed(Swift_Image::fromPath(APPLICATION_PATH.'/../public/templates/journees/images/logo_mail.png'));
        
		//Set the body
        $message->setBody($msgContent, 'text/html');
        
        $this->_sendMessage($message);
	}
	
	protected function _sendFailForCBPayment($gender, $name, $firstname, $email) {
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();	
		$message = $this->_getMailObject();
        
		//Set header
        $message->setSubject('VOTRE INSCRIPTION A LA ZID 2013');
		$message->setReplyTo(array('contact@zimmerdental.fr' => 'Zimmer Dental'));
		$message->setReturnPath('contact@zimmerdental.fr');
        $message->setFrom(array('contact@zimmerdental.fr' => 'Zimmer Dental'));
		$message->setSender('contact@zimmerdental.fr');
        $message->setTo(array($email => $gender." ".$firstname." ".$name));
		$message->setBcc(array('nicolas.trenti@webtales.fr' => 'Nicolas TRENTI'));
        //$message->setBcc(array('francoise.trupel@zimmer.com' => 'Françoise TRUPEL'));
	
		//Set body content
		$msgContent = $this->view->render('paybox/fail-cb.phtml');

		//Send the logo to the view
        $this->view->logo = $message->embed(Swift_Image::fromPath(APPLICATION_PATH.'/../public/templates/journees/images/logo_mail.png'));
        
		//Set the body
        $message->setBody($msgContent, 'text/html');
        
        $this->_sendMessage($message);
	}
	
	protected function _sendConfirmationForCheckPayment($gender, $name, $firstname, $email) {
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
		$message = $this->_getMailObject();
        
		//Set header
        $message->setSubject('VOTRE INSCRIPTION A LA ZID 2013');
		$message->setReplyTo(array('contact@zimmerdental.fr' => 'Zimmer Dental'));
		$message->setReturnPath('contact@zimmerdental.fr');
        $message->setFrom(array('contact@zimmerdental.fr' => 'Zimmer Dental'));
		$message->setSender('contact@zimmerdental.fr');
        $message->setTo(array($email => $gender." ".$firstname." ".$name));
		$message->setBcc(array('nicolas.trenti@webtales.fr' => 'Nicolas TRENTI'));
        //$message->setBcc(array('francoise.trupel@zimmer.com' => 'Françoise TRUPEL'));
	
		//Set body content
		$msgContent = $this->view->render('paybox/confirm-check.phtml');

		//Send the logo to the view
        $this->view->logo = $message->embed(Swift_Image::fromPath(APPLICATION_PATH.'/../public/templates/journees/images/logo_mail.png'));
        
		//Set the body
        $message->setBody($msgContent, 'text/html');
        
        $this->_sendMessage($message);
	}

    public function init ()
    {
        $this->_paybox = Manager::getService('Paybox');
        $this->_session = Manager::getService('Session');
        $this->getHelper('Layout')->disableLayout();
        $this->getResponse()->setHeader('Content-Type', 'text/html; charset=\'utf-8\'');
    }

    public function indexAction ()
    {
        $translator = new Zend_Translate(array(
            'adapter' => 'array',
            'content' => APPLICATION_PATH . '/../vendor/bombayworks/zendframework1/resources/languages',
            'locale' => 'fr',
            'scan' => Zend_Translate::LOCALE_DIRECTORY
        ));
        
        $paybox = new Application_Form_ClientPayboxForm();
        $paybox->setTranslator($translator);
        
        $paybox->setAttrib('action', $this->_helper->url->url(array(), null, true));
        
        $this->view->paybox = $paybox;
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            if ($paybox->isValid($request->getPost())) {
                $params = $this->getRequest()->getParams();
                
                $gender = $params['gender'];
                $name = $params['name'];
                $firstname = $params['firstname'];
                $address = $params['address'];
                $postalCode = $params['postalCode'];
                $city = $params['city'];
                $country = $params['country'];
                $officeTelNumber = $params['officeTelNumber'];
                $mobilePhoneNumber = $params['mobilePhoneNumber'];
                $email = $params['email'];
                $activity = $params['activity'];
                $university = $params['student'];
                $studentGraduationYear = $params['studentGraduationYear'];
                $billingAddress = $params['billingAddress'];
                $paymentType = $params['payment'];
                $ref = new MongoId();
                $ref = (string) $ref;
                
                if ($university == "") {
                    $studentGraduationYear = "";
                }
                
                $result = $this->_paybox->getList(array(
                    array(
                        'property' => 'email',
                        'value' => $email
                    ),
                    array(
                        'property' => 'status',
                        'value' => 'payé'
                    )
                ));
                if (count($result['data']) == 0) {
                    if ($paymentType == "card") {
                        $user = array(
                            'gender' => $gender,
                            'name' => $name,
                            'firstname' => $firstname,
                            'address' => $address,
                            'postalCode' => $postalCode,
                            'city' => $city,
                            'country' => $country,
                            'officeTelNumber' => $officeTelNumber,
                            'mobilePhoneNumber' => $mobilePhoneNumber,
                            'email' => $email,
                            'activity' => $activity,
                            'university' => $university,
                            'studentGraduationYear' => $studentGraduationYear,
                            'billingAddress' => $billingAddress,
                            'paymentType' => $paymentType,
                            'status' => 'nouveau',
                            'ref' => $ref
                        );
                        
                        $result = $this->_paybox->create($user);
                        
                        $this->_session->set('payboxUser', $result['data']);
                        $this->_helper->redirector->gotoRoute(array(
                            'action' => 'payment'
                        ));
                    } else {
                        $user = array(
                            'gender' => $gender,
                            'name' => $name,
                            'firstname' => $firstname,
                            'address' => $address,
                            'postalCode' => $postalCode,
                            'city' => $city,
                            'country' => $country,
                            'officeTelNumber' => $officeTelNumber,
                            'mobilePhoneNumber' => $mobilePhoneNumber,
                            'email' => $email,
                            'activity' => $activity,
                            'university' => $university,
                            'studentGraduationYear' => $studentGraduationYear,
                            'billingAddress' => $billingAddress,
                            'paymentType' => $paymentType,
                            'status' => 'paiement par chèque'
                        );
                        
                        $this->_paybox->create($user);
						$this->_sendConfirmationForCheckPayment($gender, $name, $firstname, $email);
                        
                        $this->_helper->redirector->gotoRoute(array(
                            'action' => 'check'
                        ));
                    }
                } else {
                    $this->view->error = 'Adresse e-mail déja utilisée';
                }
            } else {
                $this->view->hasError = true;
                $this->view->errorFieldsArray = array();
                foreach ($paybox->getErrors() as $key => $value) {
                    if (count($value) > 0) {
                        $this->view->errorFieldsArray[] = '<a href="#' . $key . '">' . $paybox->getElement($key)->getLabel() . '</a>';
                    }
                }
            }
        }
    }

    public function paymentAction ()
    {
        $controller = $this->getRequest()->getControllerName();
        $module = $this->getRequest()->getModuleName();
        
        $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $sessionUser = $this->_session->get('payboxUser');
        
        if (is_array($sessionUser) && count($sessionUser) > 0) {
            $params = array(
                // mode d'appel
                'PBX_MODE' => '1',
                // identification
                'PBX_SITE' => '0983514',
                'PBX_RANG' => '01',
                'PBX_IDENTIFIANT' => '354677877',
                // gestion de la page de connection : paramétrage "invisible"
                'PBX_WAIT' => '0',
                'PBX_BOUTPI' => "nul",
                'PBX_BKGD' => "white",
                // informations paiement (appel)
                'PBX_TOTAL' => '18000',
                'PBX_DEVISE' => '978',
                'PBX_CMD' => $sessionUser['ref'],
                'PBX_PORTEUR' => $sessionUser['email'],
                // informations nécessaires aux traitements (réponse)
                'PBX_RETOUR' => "montant:M;maref:R;auto:A;trans:T;paiement:P;carte:C;idtrans:S;pays:Y;erreur:E;validite:D;IP:I;BIN6:N;digest:H;sign:K",
                'PBX_EFFECTUE' => $serverUrl . $this->_helper->url->url(array(
                    'action' => 'done',
                    'controller' => $controller,
                    'module' => $module
                ), null, true),
                'PBX_REFUSE' => $serverUrl . $this->_helper->url->url(array(
                    'action' => 'refused',
                    'controller' => $controller,
                    'module' => $module
                ), null, true),
                'PBX_ANNULE' => $serverUrl . $this->_helper->url->url(array(
                    'action' => 'canceled',
                    'controller' => $controller,
                    'module' => $module
                ), null, true),
                'PBX_REPONDRE_A' => "www.journees-zimmerdental.fr/blocks/paybox/back-payment",
                'PBX_PAYBOX' => "https://preprod-tpeweb.paybox.com/cgi/MYpagepaiement.cgi",
                // page en cas d'erreur
                'PBX_ERREUR' => $serverUrl . $this->_helper->url->url(array(
                    'action' => 'error',
                    'controller' => $controller,
                    'module' => $module
                ), null, true),
                // en plus
                'PBX_TYPECARTE' => "CB",
                'PBX_LANGUE' => "FRA"
            );
            
            foreach ($params as $key => $value) {
                $queryStringArray[] = "$key=$value";
            }
            
            $queryString = implode('&', $queryStringArray);
            
            /* Mettre le montant en session */
            
            // lancement paiement par URL
            $url = '/cgi-bin/modulev3.cgi?' . $queryString;
            
            $this->_redirect($url);
        } else {
            $this->_session->set('error', 'Erreur lors de la procédure de paiement, merci de réessayer ultérieurement');
            $this->_helper->redirector->gotoRoute(array(
                'action' => 'index'
            ));
        }
    }

    public function doneAction ()
    {
        $controller = $this->getRequest()->getControllerName();
        $module = $this->getRequest()->getModuleName();
        
        $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $serverUrl .= $this->_helper->url->url(array(
            'action' => 'index',
            'controller' => $controller,
            'module' => $module
        ), null, true);
        
        $this->view->url = $serverUrl;
    }

    public function refusedAction ()
    {
        $controller = $this->getRequest()->getControllerName();
        $module = $this->getRequest()->getModuleName();
        
        $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $serverUrl .= $this->_helper->url->url(array(
            'action' => 'index',
            'controller' => $controller,
            'module' => $module
        ), null, true);
        
        $this->view->url = $serverUrl;
    }

    public function canceledAction ()
    {
        if ($this->_session->get('payboxUser', '') != "") {
            $sessionUser = $this->_session->get('payboxUser', '');
            
            $user = $this->_paybox->findById($sessionUser['id']);
            
            if (count($user) > 0) {
                $sessionUser['status'] = 'paiement annulé';
                $this->_paybox->update($sessionUser);
                
                $this->_session->set('payboxUser', null);
            }
        }
        
        $controller = $this->getRequest()->getControllerName();
        $module = $this->getRequest()->getModuleName();
        
        $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $serverUrl .= $this->_helper->url->url(array(
            'action' => 'index',
            'controller' => $controller,
            'module' => $module
        ), null, true);
        
        $this->view->url = $serverUrl;
    }

    public function errorAction ()
    {
        $controller = $this->getRequest()->getControllerName();
        $module = $this->getRequest()->getModuleName();
        
        $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $serverUrl .= $this->_helper->url->url(array(
            'action' => 'index',
            'controller' => $controller,
            'module' => $module
        ), null, true);
        
        $this->view->url = $serverUrl;
    }
	
	 public function checkAction ()
    {			
        $controller = $this->getRequest()->getControllerName();
        $module = $this->getRequest()->getModuleName();
        
        $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
        $serverUrl .= $this->_helper->url->url(array(
            'action' => 'index',
            'controller' => $controller,
            'module' => $module
        ), null, true);
        
        $this->view->url = $serverUrl;
    }

    public function backPaymentAction ()
    {
        $url = $this->getRequest()->getRequestUri();
        $stringArray = explode("?", $url);
        $url = $stringArray[1];
        
        $paramsKeyValue = array();
        
        foreach (explode("&", $url) as $value) {
            $keyValueArray = explode("=", $value);
            $params[$keyValueArray[0]] = $keyValueArray[1];
        }
        
        $pos = strrpos($url, '&');
        $url = substr($url, 0, $pos);
        
        $amount = "18000";
        
        $user = $this->_paybox->findByRef($params['maref']);
        
        if (is_array($user) && count($user['data']) == 1) {
            $user = $user['data'][0];
            if (isset($params['auto']) && isset($params['erreur']) && isset($params['sign']) && isset($params['montant'])) {
                if ($params['erreur'] == "00000") {
                    if ($amount == $params['montant']) {
                        $file = fopen(APPLICATION_PATH . "/../data/paybox/pubkey.pem", "r");
                        $pubkey = fread($file, 1024);
                        fclose($file);
                        
                        $sign = $params['sign'];
                        $sign = urldecode($sign);
                        $sign = base64_decode($sign);
                        $result = openssl_verify($url, $sign, $pubkey);
                        
                        if ($result == 1) {
                            $user['authorizationNumber'] = $params['auto'];
                            $user['transactionId'] = $params['trans'];
                            $user['amount'] = $params['montant'];
                            $user['status'] = 'payé';
                            
                            $this->_paybox->update($user);
							$this->_sendConfirmationForCBPayment($user['gender'], $user['name'], $user['firstname'], $user['email']);
                        } else {
                            $user['status'] = 'url et signature differents';
                            $this->_paybox->update($user);
							$this->_sendFailForCBPayment($user['gender'], $user['name'], $user['firstname'], $user['email']);
                            $this->_session->set('payboxUSer', null);
							
                            $this->getResponse()->setHttpResponseCode(400);
                        }
                    } else {
                        $user['status'] = 'le montant ne correspond pas';
                        $this->_paybox->update($user);
						$this->_sendFailForCBPayment($user['gender'], $user['name'], $user['firstname'], $user['email']);
                        $this->_session->set('payboxUSer', null);
                        
                        $this->getResponse()->setHttpResponseCode(500);
                    }
                } else {
                    $user['status'] = 'erreur CGI (' . $params['erreur'] . ')';
                    $this->_paybox->update($user);
					$this->_sendFailForCBPayment($user['gender'], $user['name'], $user['firstname'], $user['email']);
                    $this->_session->set('payboxUSer', null);
                    
                    $this->getResponse()->setHttpResponseCode(500);
                }
            } else {
                $user['status'] = 'parametres manquants dans la requete';
                $this->_paybox->update($user);
				$this->_sendFailForCBPayment($user['gender'], $user['name'], $user['firstname'], $user['email']);
                $this->_session->set('payboxUSer', null);
                
                $this->getResponse()->setHttpResponseCode(405);
            }
        } else {
            $user['status'] = 'utilisateur innexistant';
            $this->_paybox->update($user);
			$this->_sendFailForCBPayment($user['gender'], $user['name'], $user['firstname'], $user['email']);
            $this->_session->set('payboxUSer', null);
            
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    public function getCsvAction ()
    {
        $fileName = 'export_csv_' . date('Ymd') . '.csv';
        $filePath = sys_get_temp_dir() . '/' . $fileName;
        $csvResource = fopen($filePath, 'w+');
        
        $list = $this->_paybox->getList(array(
            array(
                'property' => 'status',
                'value' => array(
                    'payé',
                    'paiement par chèque'
                ),
                'operator' => '$in'
            )
        ), array(
            array(
                'property' => 'lastUpdateTime',
                'direction' => 'ASC'
            )
        ));
        
        $fieldsArray = array(
            'gender',
            'name',
            'firstname',
            'email',
            'ref',
            'status',
            'address',
            'postalCode',
            'city',
            'country',
            'mobilePhoneNumber',
            'officeTelNumber',
            'activity',
            'billingAddress',
            'lastUpdateTime',
            'paymentType',
            'authorizationNumber',
            'transactionId',
            'studentGraduationYear',
            'university'
        );
        
        $headerArray = array(
            'gender' => 'titre',
            'name' => 'nom',
            'firstname' => 'prénom',
            'email' => 'courriel',
            'ref' => 'référence',
            'status' => 'statut',
            'address' => 'adresse',
            'postalCode' => 'code postal',
            'city' => 'ville',
            'country' => 'pays',
            'mobilePhoneNumber' => 'numéro portable',
            'officeTelNumber' => 'numéro cabinet',
            'activity' => 'activité',
            'billingAddress' => 'facturation',
            'lastUpdateTime' => 'date de validation',
            'paymentType' => 'type de paiement',
            'authorizationNumber' => 'numéro d\'autorisation',
            'transactionId' => 'identifiant de transaction',
            'studentGraduationYear' => 'année suivie',
            'university' => 'université'
        );
        
        $csvLine = array();
        
        foreach ($fieldsArray as $field) {
            $csvLine[] = $headerArray[$field];
        }
        fputcsv($csvResource, $csvLine, ';');
        
        foreach ($list['data'] as $client) {
            $csvLine = array();
            
            foreach ($fieldsArray as $field) {
                switch ($field) {
                    case 'lastUpdateTime':
                        $csvLine[] = date('d-m-Y H:i:s', $client[$field]);
                        break;
                    case 'paymentType':
                        $csvLine[] = $client[$field] == 'card' ? 'carte bleue' : 'chèque';
                        break;
                    case 'billingAddress':
                        $csvLine[] = $client[$field] ? 'différente' : 'identique';
                        break;
                    default:
                        $csvLine[] = isset($client[$field]) ? $client[$field] : 'null';
                        break;
                }
            }
            fputcsv($csvResource, $csvLine, ';');
        }
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $this->getResponse()->clearBody();
        $this->getResponse()->clearHeaders();
        $this->getResponse()->setHeader('Content-Type', 'application/csv');
        $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->getResponse()->sendHeaders();
        
        fclose($csvResource);
        
        $content = file_get_contents($filePath);
        echo utf8_decode($content);
        die();
    }
}
