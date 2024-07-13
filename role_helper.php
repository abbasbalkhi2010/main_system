<?php 


function hr_profile_photo_path() {
    $ci =& get_instance();
    $upload_path = FCPATH;
	$upload_path = rtrim($upload_path, DIRECTORY_SEPARATOR);
	$upload_path = dirname($upload_path) . DIRECTORY_SEPARATOR;
	 
	$currentUrl = base_url($ci->uri->uri_string());
	if (strpos($currentUrl, 'http://localhost/') === 0) {
		$upload_path .= 'mtz_hr' . DIRECTORY_SEPARATOR;
	} else {
		$upload_path .= 'hr.mtz-group.com' . DIRECTORY_SEPARATOR;
	}
	return $upload_path; 
}

function hr_profile_photo_url() {
    $ci =& get_instance();
    $currentUrl = base_url($ci->uri->uri_string());

    if (strpos($currentUrl, 'http://localhost/') === 0) {
        return "http://localhost/mtz_hr/storage/app/";
    } else {
        return "https://hr.mtz-logistics.com/storage/app/";
    }
}


function main_system_path() {
    $ci =& get_instance();
    $upload_path = FCPATH;
	$upload_path = rtrim($upload_path, DIRECTORY_SEPARATOR);
	$upload_path = dirname($upload_path) . DIRECTORY_SEPARATOR;
	 
	$currentUrl = base_url($ci->uri->uri_string());
	if (strpos($currentUrl, 'http://localhost/') === 0) {
		$upload_path .= 'new_mtz' . DIRECTORY_SEPARATOR;
	} else {
		$upload_path .= 'system.mtz-group.com' . DIRECTORY_SEPARATOR;
	}
	return $upload_path; 
}

function main_system_url() {
    $ci =& get_instance();
   
	$currentUrl = base_url($ci->uri->uri_string());
	if (strpos($currentUrl, 'http://localhost/') === 0) {
		$upload_path = 'http://localhost/new_mtz/' ;
	} else {
		$upload_path = 'https://system.mtz-group.com/';
	}
	return $upload_path; 
}
/* system number is diffrent from branches id 
$systems_no['1'] = 'MAIN SYSTEM';
$systems['2'] = 'TURKMEN SYSTEM';
$systems['3'] = 'HR SYSTEM';					
$systems['4'] = 'AFG SYSTEM';					
$systems['5'] = 'FREIGHT SYSTEM';	
$systems['6'] = 'ASHGABAT SYSTEM';
$systems['7'] = 'MARY SYSTEM';
$systems['8'] = 'IRAQ SYSTEM';
*/
// this two function is for selecting systemmmmmmmmmmmmmmmmmmmmmm
function selectedSystemNo(){
    return $system_no = 1;   //---------------------------must be the same as bottom no 
}


if (!function_exists('selectedBranch')) {
function selectedBranch(){
    $CI =& get_instance();
    $CI->load->database();     
    $system_no = 1; // and one another is on bottom ---------------------

    $CI->db->select('id');
    $CI->db->from('branches');
    $CI->db->where('system_no', $system_no);
    $query = $CI->db->get();

    // Check if the query was successful
    if ($query && $query->num_rows() > 0) {
        $row = $query->row();
        return $row->id;
    } else {
        // Return a default value or handle the case where no row is returned
        return null; // or return a default value like 0 or -1
    }
}
}




function get_notifications() {
    $CI =& get_instance();
    $CI->load->database();

    $user_id = $CI->session->userdata('user_id');

    // Customize this query based on your database structure
	
	$CI->db->join('users', 'notifications.notify_by_user_id = users.id','left');
	$CI->db->join('employees', 'users.employees_id = employees.id','left');
    $CI->db->where('user_id', $user_id);
    $CI->db->or_where('user_id', 0); // Include public notifications
    $CI->db->order_by('notifications.id', 'DESC'); // Include public notifications
    $CI->db->select('notifications.* , employees.upload_photo , users.username'); // Include public notifications

    $query = $CI->db->get('notifications');
//echo $CI->db->last_query();exit;
    return $query->result();
}

	
	
if (!function_exists('set_dropdown_options_from_pagesettings')) {
    function set_dropdown_options_from_pagesettings($pageTitle ) {
        $CI = &get_instance();
        $CI->load->database();

        // Get the options data for the specified page title
        $CI->db->select('options_id');
        $CI->db->from('pagesettings');
        $CI->db->where('pages', $pageTitle);
        $query = $CI->db->get();
		return $query; 
	}
}
	function get_agents_list_menu() {
	 $ci =& get_instance();
	 $ci -> db -> select('agents.id , name, lname , company , countries.title  , countries_id  ');
	 $ci -> db -> from('agents');
	 $ci->db->join('countries', 'agents.countries_id = countries.id','left');
	  $ci -> db -> group_by('countries_id');
	 $query2 = $ci -> db -> get();//echo $ci->db->last_query();exit;
	 return $query2->result();
 }
	function get_supplier_list() {
	 $ci =& get_instance();
	 $ci -> db -> select('supplier.id , category');
	 $ci -> db -> group_by('supplier.category');
	 $ci -> db -> order_by('category' , 'ASC');
	 
	 $query = $ci -> db -> get('supplier');//echo $ci->db->last_query();exit;
	 return $query->result();
 }
	 
	 
function page_perrmission($page_id ){
	$ci =& get_instance();
	
	$user_id =  $ci->session->userdata('userID'); 
	$system_no = 1;
	$string=$page_id;
	$array=array_map('intval', explode(',', $string));
	$array = implode("','",$array);
	$sql="SELECT id  
	FROM page_permissions 
	WHERE pages_id IN ('".$array."') 
	and	page = 1 
	and	user_id = $user_id
	and FIND_IN_SET( ".  $system_no  . " , page_permissions.systems_view) > 0 
	";    
    
	$query = $ci->db->query($sql);
	$res = $query->num_rows();//

	if($res > 0 ){
		return true;
	}else{
		return false;
	}
}


function pro_cat_perrmissions(){
	$ci =& get_instance();
	
	$ci->db->select('view , businesses_id');
	$ci->db->from('pro_cat_permissions');
	$ci->db->where('view', 1 );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$query = $ci->db->get();
	
	
	$items = array();
	foreach ($query->Result() as $row)
	{						
		array_push($items, "$row->businesses_id");								
	}
	return $items ;	
	
} 

function view_perrmission($pages_id){
	$ci =& get_instance();
	$system_no = 1;
	$ci->db->select('view');
	$ci->db->from('page_permissions');
	$ci->db->where('page_permissions.pages_id', $pages_id );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$ci->db->where("FIND_IN_SET( $system_no , page_permissions.systems_view) > ", 0);
	$query = $ci->db->get();
	
	$res = $query->row();
	if(!empty($res->view)){return $res->view;}else{return false;}
	
}
function insert_perrmission($pages_id){
	$ci =& get_instance();
	
	$ci->db->select('insert');
	$ci->db->from('page_permissions');
	$ci->db->where('page_permissions.pages_id ', $pages_id );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$query = $ci->db->get();  
	$res = $query->row();
	if(!empty($res->insert)){return $res->insert;}else{return false;}
	
}
function edit_perrmission($pages_id){
	$ci =& get_instance();
	
	$ci->db->select('edit');
	$ci->db->from('page_permissions');
	$ci->db->where('page_permissions.pages_id ', $pages_id );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$query = $ci->db->get();  
	$res = $query->row();
	
	if(!empty($res->edit)){return $res->edit;}else{return false;}
}

function delete_perrmission($pages_id){
	$ci =& get_instance();
	
	$ci->db->select('delete');
	$ci->db->from('page_permissions');
	$ci->db->where('page_permissions.pages_id ', $pages_id );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$query = $ci->db->get();  
	$res = $query->row();
	if(!empty($res->delete)){return $res->delete;}else{return false;}
	
}
function search_perrmission($pages_id){
	$ci =& get_instance();
	
	$ci->db->select('search');
	$ci->db->from('page_permissions');
	$ci->db->where('page_permissions.pages_id ', $pages_id );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$query = $ci->db->get();  
	$res = $query->row();

	if(!empty($res->search)){return $res->search;}else{return false;}
}
function print_perrmission($pages_id){
	$ci =& get_instance();
	
	$ci->db->select('print');
	$ci->db->from('page_permissions');
	$ci->db->where('page_permissions.pages_id ', $pages_id );
	$ci->db->where('user_id = ', $ci->session->userdata('userID') );
	$query = $ci->db->get();  
	$res = $query->row();
	
	if(!empty($res->print)){return $res->print;}else{return false;}
}


function bank_currencies(){
	$ci =& get_instance();
	
	$ci->db->select('id , currency.title as curTitle ');
	$ci->db->from('currency');
	
	//$ci->db->join("currency ", 'banks.currency_id = currency.id', 'left');
	$ci->db->group_by('id');
	$query = $ci->db->get();  
	$res = $query;
	
	if(!empty($res)){return $res;}else{return false;}
}


function cus_ship_exp_place(){
	$ci =& get_instance();
	
	$ci->db->select('id , cus_ship_exp_place.title  ');
	$ci->db->from('cus_ship_exp_place');
	
	//$ci->db->join("currency ", 'banks.currency_id = currency.id', 'left');
	$ci->db->group_by('id');
	$query = $ci->db->get();  
	$res = $query;
	
	if(!empty($res)){return $res;}else{return false;}
}











function view(){
	return $view = [1,2,3,4,5,6,7,8];
}
function insert(){
	return $insert = [ 2 , 5 , 6 , 8 ]; 
}
function edit(){
	return $edit = [3 , 5 , 7 , 8 ];
}
function delete(){
	return $delete = [4 , 6 , 7 , 8 ]; 
}
function user_admin(){
	$ci =& get_instance();
	$admin = $ci->session->userdata('admin'); 
	return $admin; 
}
function user_role(){
	$ci =& get_instance();
	$role = $ci->session->userdata('role'); 
	return $role; 
}

function getBranchesName($ids){
	$ci =& get_instance();
	//.print_r($ids);
	$idsArray = explode(',', $ids);
	$ci->db->select('id, name ')->from('branches')->where_in('id', $idsArray)->order_by("id", "DESC");
	$query = $ci->db->get();

	foreach($query->result() as $value){
		echo $value->name . '</br>';
	}
	
}

function getBranchesCountry($ids){
	$ci =& get_instance();

	$ci->db->select('branches.id, countries.title ')
				->from('branches , countries')
				->where('countries_id = countries.id')
				->where('branches.id', $ids)
				->order_by("branches.id", "DESC");
	$query = $ci->db->get();

	foreach($query->result() as $value){
		echo $value->title ;
	}
	
}

function getCurrencyRate($currency_id = 0 ){
	$ci =& get_instance();
	$ci->db->select('rate')->from('currency_rate')->where('currency_id', $currency_id)->order_by("id", "DESC")->limit(1);
	$query = $ci->db->get();
   
	$res = $query->row();
		if($res != null){
		return $res->rate;	
		}else{return 0; }
	
}






function getUSDid(){
	$ci =& get_instance();
	
	$data = $ci->db->query("select id from currency where isUSD = 1  ");
	$usd =  $data->row()->id;
	return $usd; 
	
	
}

function get_all_branches_cash( ){
	$ci =& get_instance();
	 
	$ci->db->select('currency.*, payments_total , expenses_total , send_total,receiver_total,  currency.title as currTitle');
	

	$ci->db->select('customer_purchases_payments_total , customer_shipping_payments_total , customer_shipping_payments_trading_total,
	purchase_payments_total , sale_payments_total , agents_payments_total
	');

	
	$ci->db->from('currency');
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as payments_total ,currency_id ,branches_id from payments  group by currency_id ) as  payments ", 'payments.currency_id = currency.id', 'left');
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as expenses_total ,currency_id ,branches_id from expenses  group by currency_id ) as  expenses ", 'expenses.currency_id = currency.id', 'left');
	
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as send_total ,currency_id ,sender_agency from transfer_money  group by currency_id ) as  sender_money ", 'sender_money.currency_id = currency.id', 'left');
	$ci->db->join("(select COALESCE(sum(to_amount), 0 ) as receiver_total ,to_currency_id ,receiver_agency from transfer_money  group by to_currency_id ) as  receiver_money ", 'receiver_money.to_currency_id = currency.id', 'left');
	
	
	
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as customer_purchases_payments_total ,currency_id  from customer_purchases_payments  group by currency_id ) as  customer_purchases_payments ", 'customer_purchases_payments.currency_id = currency.id', 'left');
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as customer_shipping_payments_total ,currency_id  from customer_shipping_payments  group by currency_id ) as  customer_shipping_payments ", 'customer_shipping_payments.currency_id = currency.id', 'left');
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as customer_shipping_payments_trading_total ,currency_id  from customer_shipping_payments_trading  group by currency_id ) as  customer_shipping_payments_trading ", 'customer_shipping_payments_trading.currency_id = currency.id', 'left');
	$ci->db->join("(select COALESCE(sum(amount), 0 ) as agents_payments_total ,currency_id  from agents_payments  group by currency_id ) as  agents_payments ", 'agents_payments.currency_id = currency.id', 'left');
	
	
	$ci->db->join("(select COALESCE(sum(to_amount), 0 ) as purchase_payments_total ,to_currency_id  from purchase_payments  group by to_currency_id ) as  purchase_payments ", 'purchase_payments.to_currency_id = currency.id', 'left');
	$ci->db->join("(select COALESCE(sum(to_amount), 0 ) as sale_payments_total ,to_currency_id  from sale_payments  group by to_currency_id ) as  sale_payments ", 'sale_payments.to_currency_id = currency.id', 'left');

	
	
	$ci->db->group_by('currency.id');
	$query = $ci->db->get();
  //echo $ci->db->last_query();exit;
	$res = $query->result();
	
	
		foreach($res as $value){
			
			if( $value->payments_total || $value->expenses_total|| $value->send_total || $value->receiver_total ||
			$value->customer_purchases_payments_total || $value->customer_shipping_payments_total||  $value->agents_payments_total|| 
			$value->customer_shipping_payments_trading_total || $value->purchase_payments_total || $value->sale_payments_total
			
			){
				echo "
				<div class='card-body' style='font-size:20px;'>";
				echo number_format($value->payments_total - $value->expenses_total - $value->send_total + $value->receiver_total - 
				$value->customer_purchases_payments_total -  $value->customer_shipping_payments_total -  $value->customer_shipping_payments_trading_total 
				- $value->purchase_payments_total - $value->sale_payments_total  - $value->agents_payments_total  , 2 ) ."<span class='badgetext badge badge-info badge-pill'style='font-size:20px;' >$value->currTitle</span>
				</div>";
				 
			}
			
		}
	
	
		
}


function toast_notification($message, $type = 'info')
{
    $CI =& get_instance();

    $CI->load->library('session');

    $CI->session->set_flashdata('toast', array(
        'message' => $message,
        'type' => $type
    ));
}

?>
