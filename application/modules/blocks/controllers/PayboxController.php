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
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Blocks_PayboxController extends Blocks_AbstractController {
	protected $_paybox;

	protected $_session;

	public function init() {
		$this -> _paybox = Manager::getService('Paybox');
		$this -> _session = Manager::getService('Session');
		$this->getHelper('Layout')->disableLayout();
		$this->getResponse()->setHeader('Content-Type', 'text/html; charset=\'utf-8\'');
	}

	public function indexAction() {

		$translator = new Zend_Translate( array('adapter' => 'array', 'content' => APPLICATION_PATH.'/../vendor/bombayworks/zendframework1/resources/languages', 'locale' => 'fr', 'scan' => Zend_Translate::LOCALE_DIRECTORY));

		$paybox = new Application_Form_ClientPayboxForm;
		$paybox->setTranslator($translator);

		$paybox -> setAttrib('action', $this -> _helper -> url -> url());

		$this -> view -> paybox = $paybox;

		$request = $this -> getRequest();

		if ($request -> isPost()) {
			if ($paybox -> isValid($request -> getPost())) {
				$params = $this -> getRequest() -> getParams();

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
				$diploma = $params['diploma'];
				$university = $params['student'];
				$studentGraduationYear = $params['studentGraduationYear'];
				$billingAddress = $params['billingAddress'];
				$paymentType = $params['payment'];
				
				if($university == ""){
					$studentGraduationYear = "";
				}

				$user = array('gender' => $gender, 'name' => $name, 'firstname' => $firstname, 'address' => $address, 'postalCode' => $postalCode, 'city' => $city, 'country' => $country, 'officeTelNumber' => $officeTelNumber, 'mobilePhoneNumber' => $mobilePhoneNumber, 'email' => $email, 'activity' => $activity, 'diploma' => $diploma, 'university' => $university, 'studentGraduationYear' => $studentGraduationYear, 'billingAddress' => $billingAddress, 'paymentType' => $paymentType, 'status' => 'nouveau', );

				$result = $this -> _paybox -> create($user);

				//Control and backup, then if it's ok
				$this -> _session -> set('payboxUser', $result['data']);
				$this -> _helper -> redirector -> gotoRoute(array('action' => 'payment'));
			}
		}

	}

	public function paymentAction() {
		$controller = $this -> getRequest() -> getControllerName();
		$module = $this -> getRequest() -> getModuleName();

		$serverUrl = $this -> getRequest() -> getScheme() . '://' . $this -> getRequest() -> getHttpHost();

		$params = array(
		//mode d'appel
		'PBX_MODE' => '1',
		//identification
		'PBX_SITE' => '0983514', 'PBX_RANG' => '01', 'PBX_IDENTIFIANT' => '354677877',
		//gestion de la page de connection : paramétrage "invisible"
		'PBX_WAIT' => '0', 'PBX_BOUTPI' => "nul", 'PBX_BKGD' => "white",
		//informations paiement (appel)
		'PBX_TOTAL' => '00001', 'PBX_DEVISE' => '978', 'PBX_CMD' => (string)rand(1, 10000), 'PBX_PORTEUR' => "mickael.goncalves@webtales.fr",
		//informations nécessaires aux traitements (réponse)
		'PBX_RETOUR' => "montant:M;maref:R;auto:A;trans:T;abonnement:B ;paiement:P;carte:C;idtrans:S;pays:Y;erreur:E;validite:D;PPPS:U;IP:I;BIN6:N;digest:H;sign:K", 'PBX_EFFECTUE' => $serverUrl . $this -> _helper -> url -> url(array('action' => 'done', 'controller' => $controller, 'module' => $module), null, true), 'PBX_REFUSE' => $serverUrl . $this -> _helper -> url -> url(array('action' => 'refused', 'controller' => $controller, 'module' => $module), null, true), 'PBX_ANNULE' => $serverUrl . $this -> _helper -> url -> url(array('action' => 'canceled', 'controller' => $controller, 'module' => $module), null, true), 'PBX_REPONDRE_A' => $serverUrl . $this -> _helper -> url -> url(array('action' => 'back-payment', 'controller' => $controller, 'module' => $module), null, true),
		//page en cas d'erreur
		'PBX_ERREUR' => $serverUrl . $this -> _helper -> url -> url(array('action' => 'error', 'controller' => $controller, 'module' => $module), null, true),
		//en plus
		'PBX_TYPECARTE' => "CB", 'PBX_LANGUE' => "FRA", );

		foreach ($params as $key => $value) {
			$queryStringArray[] = "$key=$value";
		}

		$queryString = implode('&', $queryStringArray);

		/* Mettre le montant en session */

		//lancement paiement par URL
		$url = '/cgi-bin/modulev3.cgi?' . $queryString;

		$this -> _redirect($url);
	}

	public function doneAction() {
		$controller = $this -> getRequest() -> getControllerName();
		$module = $this -> getRequest() -> getModuleName();

		$serverUrl = $this -> getRequest() -> getScheme() . '://' . $this -> getRequest() -> getHttpHost();
		$serverUrl .= $this -> _helper -> url -> url(array('action' => 'index', 'controller' => $controller, 'module' => $module), null, true);

		$this -> view -> url = $serverUrl;
	}

	public function refusedAction() {
		$this -> _session -> set('payboxUser', null);

		$controller = $this -> getRequest() -> getControllerName();
		$module = $this -> getRequest() -> getModuleName();

		$serverUrl = $this -> getRequest() -> getScheme() . '://' . $this -> getRequest() -> getHttpHost();
		$serverUrl .= $this -> _helper -> url -> url(array('action' => 'index', 'controller' => $controller, 'module' => $module), null, true);

		$this -> view -> url = $serverUrl;
	}

	public function canceledAction() {
		$this -> _session -> set('payboxUser', null);

		$controller = $this -> getRequest() -> getControllerName();
		$module = $this -> getRequest() -> getModuleName();

		$serverUrl = $this -> getRequest() -> getScheme() . '://' . $this -> getRequest() -> getHttpHost();
		$serverUrl .= $this -> _helper -> url -> url(array('action' => 'index', 'controller' => $controller, 'module' => $module), null, true);

		$this -> view -> url = $serverUrl;
	}

	public function errorAction() {
		$this -> _session -> set('payboxUser', null);

		$controller = $this -> getRequest() -> getControllerName();
		$module = $this -> getRequest() -> getModuleName();

		$serverUrl = $this -> getRequest() -> getScheme() . '://' . $this -> getRequest() -> getHttpHost();
		$serverUrl .= $this -> _helper -> url -> url(array('action' => 'index', 'controller' => $controller, 'module' => $module), null, true);

		$this -> view -> url = $serverUrl;
	}

	public function backPaymentAction() {
		$url = $this -> getRequest() -> getRequestUri();
		$stringArray = explode("?", $url);
		$url = $stringArray[1];

		$paramsKeyValue = array();

		foreach (explode("&", $url) as $value) {
			$keyValueArray = explode("=", $value);
			$params[$keyValueArray[0]] = $keyValueArray[1];
		}

		$pos = strrpos($url, '&');
		$url = substr($url, 0, $pos);

		$amount = "00001";
		
		$sessionUser = $this -> _session -> get('payboxUser', '');

		$filter = array('email' => $sessionUser['email']);
		$user = $this -> _paybox -> customFind($filter);
		if (count($user) == 1) {
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
							$user['paymentRef'] = $params['maref'];
							$user['authorizationNumber'] = $params['auto'];
							$user['transactionId'] = $params['trans'];
							$user['status'] = 'paye';
	
							$this -> _paybox -> update($user);
	
							$this -> _session -> set('payboxUSer', null);
						} else {
							$user['status'] = 'url et signature differents';
							$this -> _paybox -> update($user);
							$this -> _session -> set('payboxUSer', null);
							
							$this -> getResponse() -> setHttpResponseCode(400);
						}
					} else {
						$user['status'] = 'le montant ne correspond pas';
						$this -> _paybox -> update($user);
						$this -> _session -> set('payboxUSer', null);
							
						$this -> getResponse() -> setHttpResponseCode(500);
					}
				} else {
					$user['status'] = 'erreur CGI';
					$this -> _paybox -> update($user);
					$this -> _session -> set('payboxUSer', null);
					
					$this -> getResponse() -> setHttpResponseCode(500);
				}
			} else {
				$user['status'] = 'parametres manquants dans la requete';
				$this -> _paybox -> update($user);
				$this -> _session -> set('payboxUSer', null);
					
				$this -> getResponse() -> setHttpResponseCode(405);
			}
		} else {
			$user['status'] = 'utilisateur innexistant';
			$this -> _paybox -> update($user);
			$this -> _session -> set('payboxUSer', null);
			
			$this -> getResponse() -> setHttpResponseCode(500);
		}
	}

}
