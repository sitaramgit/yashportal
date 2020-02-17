<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");  
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$raw_post = file_get_contents('php://input');
$request = json_decode($raw_post);

// echo json_encode($request, true);
// echo $funName =  $request->module.$request->view;
// die;
 
$proxy_host = ''; //Host Name of the Proxy
$proxy_port = ''; //Port Number of the Proxy
$proxy_username = ''; //User Name of the Proxy
$proxy_password = ''; //Password of the Proxy
//The character set to be used as character encoding for all soap requests
$default_charset = 'UTF-8';//
include("AngularSoap/soap.php"); 
$travel = new YashPortal($client,$Server_Path);

 
  if($_FILES){
// echo json_encode($_FILES, true);
  	$result = $travel->uploadImage($_FILES,$_REQUEST);
echo json_encode($result, true);
exit();
}
 $funName =  $request->module.$request->view;

 $result = $travel->$funName($request);

echo json_encode($result, true);



 
class YashPortal 
{
	public $client;
	public $Server_Path;
	function __construct($cli,$path)
	{
		$this->client = $cli;
		$this->Server_Path = $path;
	}

	public function uploadImage($files,$request){
		 
		$imgType = end(explode(".", $files['document']['name']));
		$imgNme = time().rand().".".$imgType;
		 $params = Array(Array('id'=>$request['contact_id'],'filecontents'=>$files,'sessionid'=>$request['sessionid'],'title'=>$request['name'],'ticketid'=>$request['ticket_id'],'filename'=>$imgNme,'filetype'=>$files['document']['type'],'filesize'=>$files['document']['size'],'filetmp'=>$files['document']['tmp_name']));  
		 // return $files;
	 move_uploaded_file($files['document']['tmp_name'],'AngularSoap/tmp/'.$imgNme);
		 $this->soapCall('add_ticket_attachment',$params);
		return $this->getTicketAttachmentsList($request['contact_id'],$request['sessionid'],$request['ticket_id']);
	}
	public function getTicketAttachmentsList($customerid,$sessionid,$ticketid)
	{ 
		  
		$params = Array(Array('id'=>"$customerid", 'sessionid'=>"$sessionid", 'ticketid'=>"$ticketid"));
		return $this->soapCall('get_ticket_attachments',$params);
 
	}

	public function dashboarddetails($request)
	{  
		$params = Array('id'=>$request->id,  "sessionid"=>$request->sessionid);	 
		return	$result = $this->soapCall('dashboard_details',$params); 
	}

	//Contacts module Starts here
	public function usercheck($request)
	{	
		$params = Array('user_name'=>$request->email,
						'user_password'=>$request->password,
						'version'=>"7.1.1",
						'login'=>'true');
		return $this->soapCall('authenticate_user',$params);
	}

	public function usercreate($request)
	{  
		 	$result = array();
	        foreach ($request as $key => $value)
	        {
	            $result[$key] =  $value;
	        } 
		return $this->soapCall('create_user',array($result));
		 
	}
	public function object_to_array($data)
	{ 
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] =  $value;
	        }
	        return $result;
	    
	}

	public function logoutuser($request)
	{	
		$params = Array(Array('sessionid'=>$request->sessionId,'id'=>$request->id));
		return $this->soapCall('logout_portal_user',$params); 
	}
	
	public function checksession($request)
	{	
		$params = Array(Array('sessionid'=>$request->sessionId,'id'=>$request->id));
		return $this->soapCall('check_session_id',$params);
	}
	public function userdetails($request)
	{	 
		$module = "Contacts";
		$record = $request->record;
		$params = Array('id'=>$record,'module'=>$module,'customerid'=>'','sessionid'=>'');
		 
		return $this->soapCall('get_details',$params);
	}

	public function userunique($request)
	{	  
		$params = Array('email'=>$request->email); 
		return $this->soapCall('user_unique',Array($params));
	}

	public function userchange_password($request)
	{	 
		$module = "Contacts";
		$record = $request->record;
		$params = Array(Array('sessionid'=>$request->sessionid,'id'=>$record,'username'=>$request->username,'version'=>'7.1.1','password'=>$request->new_password,'oldpassword'=>$request->old_password));  
		return $this->soapCall('change_password',$params);
	}
 
	public function userupdate($request)
	{	 
		$params = array(Array('id'=>$request->record,
								'firstname'=>$request->firstname,
								'lastname'=>$request->lastname,
								'mobile'=>$request->mobile, 
								'mailingcity'=>$request->mailingcity,
								'mailingstate'=>$request->mailingstate,
								'mailingcountry'=>$request->mailingcountry, 
							));

		// return $request;
 
		return $this->soapCall('Update_Contact',$params);
	}

	public function userforgot_passwod($request)
	{	 
		 
		$params = Array( 'email'=>$request->email);  
		return $this->soapCall('send_mail_for_password',$params);
	}

	//Contacts module End here


	//Tickets module Start here

	public function moduleslist($request)
	{	
		if($request->name == "HelpDesk"){
			$onlymine = true;
		$params = Array('id'=>$request->id,'module'=>$request->name,'sessionid'=>$request->sessionid,'onlymine'=>true);	
		return $this->soapCall('get_tickets_list',array($params));
		}else{
			$onlymine = true;
		$params = Array('id'=>$request->id,'module'=>$request->name,'sessionid'=>$request->sessionid,'only_mine'=>'true');	
		return $this->soapCall('get_list_values',$params);
		}
		
	}

	public function modulesdetails($request)
	{	 
		$module = $request->name;
		$record = $request->id;
		$params = Array('id'=>$record,'module'=>$module);
		 
		return $this->soapCall('get_details',$params);
	}
	//Tickets module End here

	//Products module Start here

	public function Productslist($request)
	{	
		$block = "Products";
		$onlymine = true;
		$params = Array('id'=>$request->id,'block'=>$block,'onlymine'=>"true", "sessionid"=>$request->sessionid);	
		$result = $this->soapCall('get_product_list_values',$params);

		$html =  $this->getblock_fieldlistview_product($result,$block);
		return Array('module'=>$block,'view'=>$result,'html'=>$html);
	}

	 

	public function Productsdetails($request)
	{	
		$module = "Products"; 
		$record = $request->record;  
		$params = array('id' => $request->id, 'block'=>$module,'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_details',$params);
		$html =  $this->getblock_fieldlist($result[0][$module]);
		return Array('module'=>$module,'view'=>"Detail",'html'=>$html);
	}
	//Products module End here


	//Quotes module Start here

	 

	public function Quoteslist($request)
	{	
		$contactid = $request->id;
		$block = "Quotes";
		$onlymine = true; 
		$params = array('id' => $contactid, 'block'=>"$block",'sessionid'=>$request->sessionid,'onlymine'=>$onlymine);
		$result = $this->soapCall('get_list_values',$params);

		$html = $this->getblock_fieldlistview($result,$block);

		return Array('module'=>$block,'view'=>"List",'html'=>$html);
	}

	public function Quotesdetails($request)
	{	 
		$module = "Quotes";  
		$params = array('id' => $request->id, 'block'=>$module,'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_details',$params);
		$html =  $this->getblock_fieldlist($result[0][$module]);
		return Array('module'=>$module,'view'=>"Detail",'html'=>$html);
	}


	 
	 
	public function Invoicelist($request)
	{	
		$contactid = $request->id;
		$block = "Invoice";
		$onlymine = true; 
		$params = array('id' => $contactid, 'block'=>"$block",'sessionid'=>$request->sessionid,'onlymine'=>$onlymine);
		 $result = $this->soapCall('get_list_values',$params);
		$html = $this->getblock_fieldlistview($result,$block);

		return Array('module'=>$block,'view'=>$result,'html'=>$html);
	}

	public function Invoicedetails($request)
	{	 
		$module = "Invoice";
		$record = $request->record;
		 
		$params = array('id' => $request->id, 'block'=>$module,'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);



		// $params = Array('id'=>$record,'module'=>$module);
		 $result = $this->soapCall('get_invoice_detail',$params);

		 $invoiceinfo = $result[0]["Invoice"];
		 $html =  $this->getblock_fieldlist($invoiceinfo);

		 return Array('module'=>$module,'view'=>$result,'html'=>$html);

	}
 

	
	//Quotes module End here
	//Tickets module Start here
 
	public function HelpDesklist($request)
	{	  
		$contactid = $request->id; 
		$params = Array(Array('id'=>$contactid, 'sessionid'=>$request->sessionid, 'user_name' => "", 'onlymine' => true, 'where' => "", 'match' => ""));	
		 
		$result = $this->soapCall('get_tickets_list',$params);
		$html = $this->getTickets_fieldlistview($result);

		return Array('module'=>"Tickets",'view'=>$result,'html'=>$html);
	}


	public function HelpDeskdetails($request)
	{	 
		$module = "HelpDesk";  
		$params = array('id' => $request->id, 'block'=>$module,'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $details =  $this->soapCall('get_details',$params);

		  $params1 = Array(Array('id'=>$request->customerid, 'sessionid'=>$request->sessionid, 'ticketid' => $request->id));

		  $comments =  $this->soapCall('get_ticket_comments',$params1);

		$html =  $this->ticketDetailHtml($details[0][$module],$comments,$request->id);
		$imgs = $this->getTicketAttachmentsList($request->customerid,$request->sessionid,$request->id);
		return Array('files'=>$imgs,'module'=>$module,'view'=> $details,'html'=> $html);
	}

	public function HelpDeskComments($request)
	{
		global $client,$Server_Path;
		$ticketid = $request->id;
		$ownerid = $request->customerid;
		$comments = $request->comments;
		$customerid = $request->customerid;
		$sessionid = $request->sessionid;

		$params = Array(Array('id'=>"$customerid", 'sessionid'=>"$sessionid", 'ticketid'=>"$ticketid",'ownerid'=>"$customerid",'comments'=>"$comments"));
 
	        return $this->soapCall('update_ticket_comment',$params);


	}


	public function HelpDeskcombo_values($request)
	{	  
		$params = Array(Array('id'=>$request->customerid, 'sessionid'=>$request->sessionid));
		 
		$result = $this->soapCall('get_combo_values',$params);

		 $productslist = array();
		 $productsname = array();
		 $ticketpriorities = array();
		 $ticketseverities = array();
		 $ticketcategories = array();
		 $servicename = array();
		 $serviceid = array();
		 


		for($i=0;$i<count($result);$i++)
		{
			if($result[$i]['productid'] != '')
			{ 
				$productslist[] = $result[$i]['productid'];
			} 
			if($result[$i]['productname'] != '')
			{
				$productsname[] = $result[$i]['productname'];
			}
			if($result[$i]['ticketpriorities'] != '')
			{
				$ticketpriorities[] = $result[$i]['ticketpriorities'];
			}
			if($result[$i]['ticketseverities'] != '')
			{
				$ticketseverities[] = $result[$i]['ticketseverities'];
			}
			if($result[$i]['ticketcategories'] != '')
			{
				$ticketcategories[] = $result[$i]['ticketcategories'];
			} 
			if($result[$i]['serviceid'] != ''){
				$serviceid[]= $result[$i]['serviceid'];
			}
			if($result[$i]['servicename'] != ''){
				$servicename[] = $result[$i]['servicename'];
			}
		}

		return Array('productslist'=>$productslist,'productsname'=>$productsname,'ticketpriorities'=> $ticketpriorities,'ticketseverities'=> $ticketseverities,'ticketcategories'=> $ticketcategories,'servicename'=> $servicename,'serviceid'=> $serviceid);

	}

	public function ticketcommentscreate($request)
	{	  
		 
		$params = Array(Array('ownerid'=>$request->id,'ticketid'=>$request->tktid,'comments'=>$request->comments));
		 
		return $this->soapCall('create_ticket_comment',$params);
	}

	public function ticketchangestatus($request)
	{	  
		 
		$params = Array(Array('ticketid'=>$request->tktid));
		 
		return $this->soapCall('change_ticket_status',$params);
	}

	public function HelpDeskcreate($request)
	{	  
		 

		$params = Array(Array(
		'id'=>$request->id,
		'sessionid'=>$request->sessionid,
		'title'=>$request->title,
		'description'=>$request->description,
		'priority'=>$request->priority,
		'severity'=>$request->severity,
		'category'=>$request->category, 
		'parent_id'=>$request->parent_id,
		'product_id'=>$request->productid,
		'module'=>"HelpDesk", 
		'serviceid'=>$request->servicename, 
	));
		 
		return $this->soapCall('create_ticket',$params);
	}
 	//Tickets module End here

 	//faq module start
	public function Faqlist($request)
	{	   
		 
		$params = Array(Array('id' => $request->id, 'sessionid'=>$request->sessionid));
		 
		$result = $this->soapCall('get_KBase_details',$params);

		$category_array = $result[0];
		$faq_array = $result[2];

		if(@array_key_exists('productid',$result[1][0]) && @array_key_exists('productname',$result[1][0]))
		        $product_array = $result[1];
		elseif(@array_key_exists('id',$result[1][0]) && @array_key_exists('question',$result[1][0]) && @array_key_exists('answer',$result[1][0]))
		        $faq_array = $result[1];
		$html = $this->getLatestlyCreatedFaqList($product_array,$faq_array,$category_array);


		 return Array('module'=>"faq",'view'=> $result,'html'=> $html);

	}


	

 	//faq module end


 	//services module start

	public function Serviceslist($request)
	{	 
		$module = "Services";  
		$params = array('id' => $request->id, 'module'=>$module,'sessionid'=>$request->sessionid,'onlymine'=>true);
		  $result =  $this->soapCall('get_service_list_values',$params);
		$html =  $this->getblock_fieldlistview_product($result,$module);
		  
		return Array('module'=>$module,'view'=> $result,'html'=> $html);
	}


	public function Servicesdetails($request)
	{	 
		$module = "Services";  
		 
		 $params = array('id' =>$request->id, 'block'=>$module, 'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		   $result =  $this->soapCall('get_details',$params);
		  $serviceinfo = $result[0]["Services"];
		 $html =  $this->getblock_fieldlist($serviceinfo);
		  
		return Array('module'=>"Services",'view'=>'list','html'=> "$html");
	}
 	//services module end

 	//Documents module Starts here

	public function Documentslist($request)
	{	   
		$block = "Documents";
		$params = array('id' => $request->id, 'module'=>$block,'sessionid'=>$request->sessionid,'onlymine'=>true); 
		 
		$result = $this->soapCall('get_list_values',$params);
		$html =  $this->getblock_fieldlistview($result,$block);
		  
		return Array('module'=>$block,'view'=> $result,'html'=> $html);
	}

	public function Documentsdetails($request)
	{	  
		$module = "Documents";  
		 
		$params = array('id' =>$request->id, 'block'=>$module, 'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_details',$params);
		  $noteinfo = $result[0][$module];
		$html =  $this->getblock_fieldlist($noteinfo);
		  
		return Array('module'=>$module,'view'=> $result,'html'=> $html);
	}

 	//Documents module End here

 	//Project module Start here

 	public function Projectlist($request)
	{	   
		$block = "Project";
		$params = array('id' => $request->id, 'module'=>$block,'sessionid'=>$request->sessionid,'onlymine'=>true); 
		 
		$result = $this->soapCall('get_list_values',$params);
		$html =  $this->getblock_fieldlistview($result,$block);
		  
		return Array('module'=>$block,'view'=> $result,'html'=> $html);
	}


	public function Projectdetails($request)
	{	  
		$module = "Project";  
		 
		$params = array('id' =>$request->id, 'block'=>$module, 'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_details',$params);
		  $noteinfo = $result[0][$module];
		$html =  $this->getblock_fieldlist($noteinfo);
		  
		return Array('module'=>$module,'view'=> $result,'html'=> $html);
	}
 	//Project module End here


 	//ProjectTask module Start here

 	public function ProjectTasklist($request)
	{	   
		$block = "ProjectTask";

		$projecttaskblock = 'ProjectTask';

		$params = array('id' => $request->id, 'module'=>$block,'sessionid'=>$request->sessionid,'onlymine'=>true); 
		 
		$prjtId = $this->soapCall('get_list_Project',$params);
		$pt = array();
		$i=0;
		 
		return Array('module'=>$block,'view'=> "list",'html'=> $prjtId[0]);
	}


	public function ProjectTaskdetails($request)
	{	  
		$module = "ProjectTask";  
		 
		$params = array('id' =>$request->id, 'block'=>$module, 'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_project_components',$params);
		  
		$html =  $this->getblock_fieldlistview($result,"ProjectTask");
		  
		return Array('module'=>$module,'view'=> $result,'html'=> $html);
	}
 	//ProjectTask module End here


 		//ProjectMilestone module Start here

 	public function ProjectMilestonelist($request)
	{	   
		$block = "ProjectMilestone";

		$projecttaskblock = 'ProjectTask';

		$params = array('id' => $request->id, 'module'=>$block,'sessionid'=>$request->sessionid,'onlymine'=>true); 
		 
		$prjtId = $this->soapCall('get_list_Project',$params);
		$pt = array();
		$i=0;
		 
		return Array('module'=>$block,'view'=> $prjtId,'html'=> $prjtId);
	}


	public function ProjectMilestonedetails($request)
	{	  
		$module = "ProjectMilestone";  
		 
		$params = array('id' =>$request->id, 'block'=>$module, 'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_project_components',$params);
		  
		$html =  $this->getblock_fieldlistview($result,"ProjectMilestone");
		  
		return Array('module'=>$module,'view'=> $result,'html'=> $html);
	}
 	//ProjectTask module End here



 	//ProjectMilestone module Start here

 	public function Assetslist($request)
	{	   
		$block = "Assets";

		 

		$params = array('id' => $request->id, 'block'=>$block,'sessionid'=>$request->sessionid,'onlymine'=>true); 
		 
		$result = $this->soapCall('get_list_values',$params);
		$html =  $this->getblock_fieldlistview($result,"Assets");
		 
		 
		return Array('module'=>$block,'view'=> $result,'html'=> $html);
	}


	public function Assetsdetails($request)
	{	  
		$module = "Assets";  
		 
		$params = array('id' =>$request->id, 'block'=>$module, 'contactid'=>$request->customerid,'sessionid'=>$request->sessionid);
		  $result =  $this->soapCall('get_details',$params);
		  

		   $noteinfo = $result[0][$module];
		$html =  $this->getblock_fieldlist($noteinfo); 
		  
		return Array('module'=>$module,'view'=> $result,'html'=> $html);
	}
 	//ProjectTask module End here
 	
 
	public function soapCall($method,$params)
	{
		$status = $this->client->call($method, $params, $this->Server_Path, $this->Server_Path);
		if($this->client->getError()){
			return $this->client->getError();
		} else {
			return $status;
		}
	}



	//only for product
public function getblock_fieldlistview_product($block_array,$module)
{
	
 $header = array();
 $header[0] = "Products";
 $header[1] = "Quotes Related Products";	
 $header[2] = "Invoices Related Products";	
 
 if($block_array == '')
 {
	$list.='<tr><td colspan="6">'.$module.' Not Available</td></tr>';
	return $list;
 }

for($k=0;$k<=2;$k++)
{

$header_arr =$block_array[$k][$module]['head'][0];	
$nooffields= is_array($header_arr) ? count($header_arr) : 0;
$data_arr=$block_array[$k][$module]['data'];
	$noofdata= is_array($data_arr) ? count($data_arr) :0;
	
	$list .='<tr><td colspan="6"><b>' .$header[$k].'</b></td></tr>';
	
	if($block_array[$k][$module]['data'] == ''){
		$list.='<tr><td colspan="6">'.$header[$k].' Not Available</td></tr>';
	}
	
	if($nooffields > 0 ){
		$list .='<tr>';
		for($i=0;$i<$nooffields;$i++)
			$list .= "<th>". $header_arr[$i]['fielddata']."</th>";
		$list .='</tr>';
	}
		
	if($noofdata > 0)
	{
		for($j=0;$j<$noofdata;$j++)
		{
			if($j==0||$j%2==0)
				$list .='<tr>';
			else
				$list .='<tr>';

			for($i=0;$i<$nooffields;$i++)
			{
				$data =$data_arr[$j][$i]['fielddata'];
				if($data == '')
					$data ='&nbsp;';
				$list .= "<td>".$data."</td>";
			}
			$list .='</tr>';
		}
	}	
   $list .= '<tr><td colspan ="'.$nooffields.'">&nbsp;</td></tr>';
}

$tbl = '<section class="content"><div class="row">';
    $tbl .= '<div class="col-md-12">';
   	$tbl .= '<div class="box-body table-responsive no-padding"><table class="table table-hover">';
   	$tbl .=$list;
   	$tbl .='</table></td></tr></table></td></tr></table>';
return $tbl;
}



public function getblock_fieldlist($block_array)
{
	$list='';$z=0;
	 $field_count=count($block_array);

	if($field_count != 0)
	{
		$list .= '<div style = "clear:both;"></div>';
		for($i=0;$i<$field_count;$i++,$z++)
		{
			$blockname = $block_array[$i]['blockname'];
			$data = $block_array[$i]['fieldvalue'];
			if($block_array[$i]['fieldlabel'] == 'Note'){
    			$data = html_entity_decode($data);
    		}
    		if($data =='')
				$data ='&nbsp;';
			if(strcmp($blockname,$block_array[$i-1]['blockname'])){
				if($z != 0)
					$list .= '</div></div></div></div>';
				if($blockname != 'Ticket Information') //hardcoded for tickets information block so that it ll not be displayed
					$list .= '<div class="widget-box"><div class = "widget-header">
							<h3 class = "widget-title">'. $blockname.'</h3></div>';
					$z = 0;
				
					$list .= '<div class = "widget-body"><div class="widget-main no-padding single-entity-view">
						<div style="width:auto;padding:12px;display:block;" id="tblLeadInformation">';
			}
			
			if($z==0 || $z%2==0)
			$list .= '<div class="row">';
			$list .= '<div class="form-group col-sm-6">
			<div class="row">
			<label class="col-sm-4 control-label no-padding-right">'
					.$block_array[$i]['fieldlabel'].'</label>';
			if(($z == 0 || $z%2 == 0) &&($i == ($field_count-1))){
				$list .= '<div class="col-sm-8 dvtCellInfo" align="left" valign="top">'.$data.'</div></div>';
			}
			else {
				$list .= '<div class="col-sm-8 dvtCellInfo" align="left" valign="top">&nbsp;'.$data.'</div></div>';
			}
			if($z%2 == 1 ||($i == ($field_count-1) )){
				$list .= '</div>';
			}
			$list .= '</div>';
		}	
	}
	$list.= '<tr><td colspan="4">&nbsp;</td></tr>';

	$tbl = '<aside class="right-side">';
	$tbl .= '<section class="content-header" style="box-shadow:none;">
			<div class="row-pad"><div class="col-sm-10">
				<input class="btn btn-primary btn-flat" type="button" value="Back" onclick="window.history.back();"/>
				</div></div></section>';
	$tbl .= $list;

	$tbl .= '</table></td></tr>';	
	$tbl .= '</table></td></tr></table></td></tr></table>';
	return $tbl;
}

function getblock_header($str,$headerspan='4',$ticketcloselink=false)
{
	$list = '';
	if ($ticketcloselink == true) {
		$list .='<tr><td colspan="'. ($headerspan-1) .'" class="detailedViewHeader"><b>'. $str.'</b></td>';
		$list .='<td class="detailedViewHeader"><div align="right">'.$ticketcloselink.'</div></td></tr>';
	} else {
		$list .='<tr><td colspan="'. ($headerspan) .'" class="detailedViewHeader"><b>'.$str.'</b></td></tr>';
	}	
	return $list;

}


//for quotes,notes and invoice

function getblock_fieldlistview($block_array,$block)
{

	if($block_array[0] == "#MODULE INACTIVE#"){
		$list.='<div class="row"><div class="form-group col-sm-12">'.$block.' Module Inactive </div></div>';
		return $list;
	}
	if($block_array == ''){
		$list.='<div class ="row"><div class="form-group col-sm-12">'.$block.'Not available </div></div>';
		return $list;
	}
	$header_arr =$block_array[0][$block]['head'][0];	
	$nooffields= is_array($header_arr) ? count($header_arr):0;
	$data_arr=$block_array[1][$block]['data'];
	$noofdata= is_array($data_arr) ? count($data_arr):0;

	

	if($nooffields!='')
	{
		$list .= '<div class="row"><div class="form-group col-sm-12">';
		$list .='<div class="box-body table-responsive no-padding"><table class="table table-hover"><tbody><tr>';
		for($i=0;$i<$nooffields;$i++)
			$list .= "<th>". $header_arr[$i]['fielddata']."</th>";
		$list .='</tr>';
	}	
	if($noofdata != '')
	{
		for($j=0;$j<$noofdata;$j++)
		{
			if($j==0 || $j%2==0)
				$list .='<tr>';
			else
				$list .='<tr>';

			for($i=0;$i<$nooffields;$i++)
			{
				$data =$data_arr[$j][$i]['fielddata'];
				if($data == '')
					$data ='&nbsp;';
				$list .= "<td>".$data."</td>";
			}
			$list .='</tr>';
		}
		
        $list .= '<tr><td colspan ="'.$nooffields.'">&nbsp;</td></tr>';
        $list .= '</table></div></div></div>';
}

return $list;
}


	public function getTickets_fieldlistview($result){

					$list = '';
					$closedlist = '';
				
					$list .= '<tr><td>';
	
					if($result == '') {
						$list .= '<tr"><td>';
						$list .= '<table class="table table-hover">';
						$list .= '<div class="box-header">';
						$list .= '<tr><td>No Tickets Submitted</td></tr></table>';
						$list .= '</tr></td>';
					} else {
					
						$header = $result[0]['head'][0];
						$nooffields = count($header);
						$data = $result[1]['data'];
						$rowcount = count($data);
						$showstatus = $_REQUEST['showstatus'];
						if($showstatus != '' && $rowcount >= 1) {
							$list .= '<div class="box">';
							$list .= '<div class="box-header">';
							$list .= '<h3 class="box-title" style="font-size:24px;">'.$showstatus.' Tickets </h3></div>';
							$list .= '<div class="box-body table-responsive no-padding"><table class="table table-hover">';
							$list .= '<tbody><tr>';
					
							for($i=0; $i<$nooffields; $i++)
							{
								$header_value = $header[$i]['fielddata'];
								$list .= '<th>'.$header_value.'</th>';
							}
							$list .= '</tr>';
					
							$ticketexist = 0;
							for($i=0;$i<count($data);$i++)
							{		
								$ticketlist = '';
						
								if ($i%2==0)
									$ticketlist .= '<tr>';
								else
									$ticketlist .= '<tr>';
							
								$ticket_status = '';
								for($j=0; $j<$nooffields; $j++) {			
									$ticketlist .= '<td>'.$data[$i][$j]['fielddata'].'</td>';
									if ($header[$j]['fielddata'] == 'Status') {
										$ticket_status = $data[$i][$j]['fielddata'];
									}
								}
								$ticketlist .= '</tr>';
					
								if($ticket_status == $showstatus){
									$list .= $ticketlist; 
									$ticketexist++;
								}		
							}
							if($ticketexist == 0)
							{
								$list .= '<tr><td> No Tickets Submitted</td><td><td><td><td><td><td></td></td></td></td></td></td></tr>';
							}
						
							$list .= '</table>';
						
						}
						else {
							$list .= '<div class="box">';
							$list .= '<div class="box-header">';
							$list .= '<h3 class="box-title" style="font-size:24px;"> My Open Tickets</h3></div>';
							$list .= '<div class="box-body table-responsive no-padding"><table class="table table-hover">';
							$list .= '<tbody><tr>';
						
							$closedlist .= '<div class="box">';
							$closedlist .= '<div class="box-header">';
							$closedlist .= '<h3 class="box-title" style="font-size:24px;">Closed Tickets</h3></div>';
							$closedlist .= '<div class="box-body table-responsive no-padding"><table class="table table-hover">';
							$closedlist .= '<tbody><tr>';
							
							for($i=0; $i<$nooffields; $i++)
							{
								$header_value = $header[$i]['fielddata'];
								$headerlist .= '<th>'. $header_value.'</th>';
							}
							$headerlist .= '</tr>';
							
							$list .= $headerlist;
							$closedlist .= $headerlist;
						
							for($i=0;$i<count($data);$i++)
							{
								$ticketlist = '';
								
								if ($i%2==0)
									$ticketlist .= '<tr>';
								else
									$ticketlist .= '<tr>';
								
								$ticket_status = '';
								for($j=0; $j<$nooffields; $j++) {		
									$ticketlist .= '<td>'.$data[$i][$j]['fielddata'].'</td>';
									if ($header[$j]['fielddata'] == 'Status') {
										$ticket_status = $data[$i][$j]['fielddata'];
									}
								}
								$ticketlist .= '</tr>';
						
								if($ticket_status == "Closed")
									$closedlist .= $ticketlist;
								elseif($ticket_status != '')
									$list .= $ticketlist;
							}	
						
							$list .= '</table>';
							$closedlist .= '</table></div>';
						
							$closedlist .= '</div></td></tr>';
						
							$list .= '</div></div><br><br>'.$closedlist;
						}
					}
					return $list;
	}


 
	public function ticketDetailHtml($ticketinfo,$commentresult,$ticketid){

			$commentscount = count($commentresult);
			
			 

			$ticket_status = '';
			foreach($ticketinfo as $key=>$value) {
				$fieldlabel = $value['fieldlabel'];
				$fieldvalue = $value['fieldvalue'];
				if ($fieldlabel == 'Status') {
					$ticket_status = $fieldvalue;
					break;
				}
			}

			//If the ticket is created by this customer and status is not Closed then allow him to Close this ticket otherwise not
                     $ticket_close_link = "Close this ticket";
				$list = ""; 


				  $list .= '<div style = "clear:both;"></div>
					
					<div class = "widget-box">
						<div class = "widget-header">
							<h5 class = "widget-title"> Ticket Information  <span style = "float:right;">';       
                     $list .= '</span></h5>
						</div>
						
						<div class = "widget-body">
							<div class="widget-main no-padding single-entity-view">
								<div style="width:auto;padding:12px;display:block;" id="tblLeadInformation">';



				$field_count = count($ticketinfo);

				if($field_count != 0){
			
				for($i=0;$i<$field_count;$i++,$z++){
					$blockname = $ticketinfo[$i]['blockname'];
					
					$data = $ticketinfo[$i]['fieldvalue'];
						
					if($ticketinfo[$i]['fieldlabel'] == 'Note'){
						$data = html_entity_decode($data);
					}
					
					if($data =='')
						$data ='&nbsp;';
						
						if(strcmp($blockname,$ticketinfo[$i-1]['blockname'])){
							
							if($z > 0 && ($z % 2) == 1)
								$list .= "</div>";
								
							if($blockname != 'Ticket Information'){
								$list .= '</div></div></div></div>
								<div class="widget-box">
									<div class = "widget-header">
										<h5 class = "widget-title">'. $blockname . '</h5>
									</div>
									<div class = "widget-body">
										<div class="widget-main no-padding single-entity-view">
											<div style="width:auto;padding:12px;display:block;" id="tblLeadInformation">';
							}
							
							$z = 0;
						}
						
						if($z==0 || $z%2==0)
							$list .= '<div class="row">';
							
						$list .= '<div class="form-group col-sm-6">
										<div class="row">
										<label class="col-sm-4 control-label no-padding-right">
											'. $ticketinfo[$i]['fieldlabel'].
										'</label>
										<div class="col-sm-6 dvtCellInfo" align="left" valign="top">
											&nbsp;
											'.$data.'
										</div>
										</div>
								</div>'; 
									
						if(
							$z%2 == 1 ||
							($i == ($field_count-1) ) 
						)
							$list .= '</div>';
					
					}	
				}



				$list .=  '<div class="widget-box">
								<div class = "widget-header">
									<h5 class = "widget-title">Comments</h5>
								</div>
								<div class = "widget-body">
									<div class="widget-main no-padding single-entity-view">
										<div style="width:auto;padding:12px;display:block;" id="tblLeadInformation">';
				if($commentscount >= 1 && is_array($commentresult)){
					
					$list .= '<div id="scrollTab2">
							<table width="100%"  border="0" cellspacing="5" cellpadding="5">';
							for($j=0;$j<$commentscount;$j++){
								$list .= '
									   <tr>
											<td width="5%" valign="top">'.($commentscount-$j).'</td>
											<td width="95%">'.$commentresult[$j]['comments'].'<br><span class="hdr"> Comment by : '.$commentresult[$j]['owner'].' on '.$commentresult[$j]['createdtime'].'</span></td>
									   </tr>';
							}
							$list .= '</table></div>';
				}
				
			 
				
				$list .= '</div></div></div></div>';
				
			 
				 

				 
			return $list;
		}  



		public function getLatestlyCreatedFaqList($product_array,$faq_array,$category_array)
		{	
			// return $faq_array;
			$list = '<div class="row"> <div class="col-md-3" > '; 

			if(!empty($faq_array)){
				$list .= '<div class = "widget-box">
			<div class="widget-header">
	                 <h5 class="widget-title">
	                   Filters
			         </h5>
	          </div></div>';


	          $list .= '<ul id="navigation">';

	          if((count($category_array))>0 && $category_array !=null){ 

	          	 $list .= '<li>Category <ul>';
	          	 for($i=0,$j=1;$i<count($category_array);$i++,$j++){
					$noof_faqs = $this->getNoofFaqsPerCategory($category_array[$i],$faq_array);
						$category_index = 0;

					$list .=	'<li>
							<a class="cursor category_index id_'.$i.'" >';
					 
						$list .=$this->text_length($category_array[$i]);
				 

					$list .='</a>
					<span class="hdr">('.$noof_faqs.')';
							 	
								 
						 
						$list .='</span>
					</li>';
				}

	          $list .= 	' </ul> </li>';
	          }







	          if($product_array != null){ 

	          	 $list .= '<li>Products <ul>';
	          	 for($i=0,$j=1;$i<count($product_array);$i++,$j++){
					$noof_faqs = $this->getNoofFaqsPerProduct($product_array[$i]['productid'],$faq_array);
						$category_index = 0;

					$list .=	'<li>
							<a class=" cursor productfaqDetail id_'.$product_array[$i]['productid'].' " >';
					$list .= $product_array[$i]['productname'];

					$list .='</a>
					<span class="hdr">('.$noof_faqs.')';
							 	
								 
						 
						$list .='</span></li>';
				}

	          $list .= 	' </ul> </li>';
	          }






	           $list .= ' </ul>';
			 }

		$list .= '</div>';


			$list .= '<div class="col-md-9"><div class="widget-header widget_titles"><h5><b> Recently Created Articles </b></h5></div>';
			$list .= '<div class="table-responsive">
						<table width="100%" border="0" cellspacing="1" cellpadding="3" class="lvt table table-striped table-bordered table-hover">';
			
			for($i=0;$i<count($faq_array);$i++)
			{
				$record_exist = true;
				$list .= '<tr>
							<td>
								<img src="assets/faq.png" valign="absmiddle">&nbsp;
								<a class="cursor  faqdetailView id_'.$faq_array[$i]['id'].'" >'.$faq_array[$i]['question'].'</a>
							</td>
					   	  </tr>
					   	  <tr>
							<td class="small" style="padding-left:35px;" >'.$faq_array[$i]['answer'].'</td>
			    		   	</tr>';
			}
			if(!$record_exist)
				$list .= "There are no Articles.";

			$list .= '</table></div></div></div></div>';
			return $list; 
		}


		public function getNoofFaqsPerCategory($category_name,$faq_array)
		{
		 
			$count = 0;
			for($i=0;$i<count($faq_array);$i++)
			{
				if($category_name == $faq_array[$i]['category'])
					$count++;
			}
			return $count;
		}

	public	function text_length($str){
			$length = strlen($str);
			if($length > 25){
				$str = substr($str,0,25)."..";
				return $str;
			}
			return $str;
		}

	public function getNoofFaqsPerProduct($productid,$faq_array)
	{
		 
		$count = 0;
		for($i=0;$i<count($faq_array);$i++)
		{
			if($productid == $faq_array[$i]['product_id'])
				$count++;
		}
		return $count;
	}




	


}

