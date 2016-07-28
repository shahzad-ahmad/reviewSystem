<?php
namespace app\api\oAuth2\controller ;
use app\models\repository\ApiRepository;
class OrderController {
	
	private $server;
	private $apiModel;

	public function __construct($server_obj){
		$this->server = $server_obj;
		$this->apiModel = new ApiRepository();
	}
	public function handleOrderRequest(){
		$request_method = $_SERVER['REQUEST_METHOD'];
		$token = getallheaders()['Authorization'];
		$token = explode(' ',$token)[1];
		switch($request_method){
			case 'PUT':
				$order_id = 1;
				//explode('&',str_replace('+', ' ', file_get_contents(tmp_file) ) ));
				die;
				$title = "this is order title updated1 hello";
				$token = "14a518f3ef5eb7d337250a972e85cd0133892f30";
				$status = $this->apiModel->updateOrder($order_id, $title, $token);
				echo $status;
				break;
			case 'POST':
			
				$order_id = $_POST['order_id'];
				$title = $_POST['title'];
				$status = $this->apiModel->createOrder($order_id, $title, $token);
				echo $status;
				break;

			case 'DELETE':
			
				$order_id = $_GET['order_id'];
				$status = $this->apiModel->deleteOrder($order_id, $token);
				echo $status;
				break;

			case 'GET':
				if(isset($_GET['order_id']))
					$order_id = $_GET['order_id'];
				else $order_id = null;
				$status = $this->apiModel->getOrder($token ,$order_id);
				print_r($status);
				break;

 			default:
				die;
		}
	}
}

