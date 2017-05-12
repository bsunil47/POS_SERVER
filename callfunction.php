<?php

/**
 * Created by PhpStorm.
 * User: Kesav
 * Date: 2/23/2016
 * Time: 6:16 PM
 */
class callfunction extends api
{
    //public $base_url;
    private $server_url = "http://115.124.125.42/Tolls/api/2019/ps/";
    private $Tollr;
    public function __construct($db_array,$toll,$method)
    {
        parent::__construct();
        $this->setViewFile($method);
        $this->Tollr = $toll;
        $this->db = new MysqliDb($db_array);
        if (!$this->db->ping())
            $this->db->connect();
    }

    public function index(){
        //$server_output = $this->curl_hit('l',['toll_unique_number'=>'ElevatedSection_2_NH-7','username' => 'ElSe-NH7-1', 'password' =>'123456789']);

// further processing ....
        //print_r($server_output);
        //if ($server_output->Code == "200") { echo 'asda'; } else { echo 'asd'; }
        //print_r($this->Tollr); exit;
        echo $this->getRenderedHTML($this->viewFile);
    }

    public function read(){
        if($this->http_method != 'POST'){
            $this->output_other(['CODE'=>'403'],403);
        }
        if(!empty($_POST)){
            //$this->sucess_data(['CODE'=>'200','INFO'=> $this->Tollr]); exit;
            $post_array = $this->Tollr;
            $post_array['registration_number'] = $_POST['registration_number'];
            $server_output = $this->curl_hit('vs',$post_array);
            echo $server_output;
            //if($id)
            //    echo 'user was created. Id=' . $id;
            //$this->sucess_data(['CODE'=>'200','INFO'=> [$_POST,$_POST]]);
        }
    }


    public function method_not(){
        $this->output_other(['CODE'=>'404','Error'=> 'Not Found'],404);
    }

    /*
     * Trip details
     */
    public function tripdetails()
    {
        if($this->http_method != 'POST'){
            $this->output_other(['CODE'=>'403'],403);
        }
        if(!empty($_POST)){
            $trip_array=array('trip_details_id'=>$_POST['trip_details_id'],'trip_id'=>$_POST['trip_id'],'created_on'=>$_POST['created_on'],'user_id'=>$_POST['user_id'],'vechical_id'=>$_POST['vehical_id'],'vechical_type'=>$_POST['vehical_type'],'assigned_booth_id'=>$_POST['assigned_booth_id'],'trip_type'=>$_POST['trip_type']);
            $user_arry=array('user_id'=>$_POST['user_id'],'firstname'=>$_POST['firstname'],'lastname'=>$_POST['lastname'],'user_email'=>$_POST['user_email'],'mobile_number'=>$_POST['mobile_number'],'address1'=>$_POST['address1'],'address2'=>$_POST['address2'],'zipcode'=>$_POST['zipcode']);
            $vehical_array=array('vechical_id'=>$_POST['vehical_id'],'user_id'=>$_POST['user_id'],'registration_no'=>$_POST['registration_no'],'owner_name'=>$_POST['firstname'],'vechical_type_id'=>$_POST['vehical_type_id'],'vehical_drive_type'=>$_POST['vehical_drive_type']);
            $trip=$this->insert_entry('tbl_trip_details',$trip_array);
            $getuser=$this->db->query("select * from tbl_user_details where user_email='".$_POST['user_email']."'");
            if(empty($getuser)){ $user=$this->insert_entry('tbl_user_details',$user_arry);}
            $getvehical=$this->db->query("select * from tbl_user_vehicals where user_id = '".$_POST['user_id']."' and registration_no='".$_POST['registration_no']."'");
            if(empty($getvehical)){
                $vehical=$this->insert_entry('tbl_user_vehicals',$vehical_array);
            }
            if($trip==true && $user==true && $vehical==true){
                $this->sucess_data(['CODE'=>'200','INFO'=> 'Success']);
            }
        }
    }
    private function insert_entry($tbl,$data){

        foreach($data as $row){
            $col_name="";
            $col_values="";
            foreach($data as $key=>$value){
                $col_name .= "$key,";
                $col_values .= "'$value',";
            }
            $col_name = rtrim($col_name, ',');
            $col_values = rtrim($col_values, ',');
            $conn=$this->db->query("INSERT INTO $tbl ($col_name) VALUES ($col_values)");
        }
        return true;
    }
    /*
     * geofence hit api
     * **/
    public function geofence(){
        if($this->http_method != 'POST'){
            $this->output_other(['CODE'=>'403'],403);
        }
        $date=date("Y-m-d");
        if(!empty($_POST)){
            if(!empty($_POST['type']) && $_POST['type'] == 'Local'){
                $state = 1;
                //$result = $this->curl_hit('updtd',['trip_details_id'=>$_POST['trip_details_id'],'allowed_booth_id' => $_POST['booth_id'],'toll_user_id' => $_POST['toll_user_id'],'boothside_id' => $_POST['boothside_id']]);
            }else{
                $state = 2;
            }
            $trip=$this->db->query("select * from tbl_trip_details where trip_details_id = '".$_POST['trip_details_id']."'");
            if(!empty($trip)){
                $update=$this->db->query("update tbl_trip_details set updated_on = '$date',status='$state' where trip_details_id = '".$_POST['trip_details_id']."'");
                if(!empty($_POST['type']) && $_POST['type'] == 'Local'){
                    $this->curl_hit('updtd',['trip_details_id'=>$_POST['trip_details_id'],'allowed_booth_id' => $_POST['booth_id'],'toll_user_id' => $_POST['toll_user_id'],'boothside_id' => $_POST['boothside_id']]);
                    $vehicaldetails=$this->vehicaldetails(date('Y-m-d'));
                    $data['vehicaldetails']=$vehicaldetails;
                    $this->sucess_data(['Code'=>'200','Info'=> $data]);
                }else{
                    $this->sucess_data(['CODE'=>'200','INFO'=> 'Success']);
                }


            }else{
                $this->sucess_data(['CODE'=>'499','INFO'=> 'Insuffient Data']);
            }
        }
    }
    /*
     * Vechical Details APi on current date basis
     * **/
    public function vehicals()
    {
        if($this->http_method != 'POST'){
            $this->output_other(['CODE'=>'403'],403);
        }
        if(!empty($_POST)){
            $vehicals=$this->db->query("select * from tbl_user_vehicals as V JOIN tbl_trip_details as T ON V.user_id = T.user_id where date(T.updated_on)='".$_POST['date']."'");
            if(!empty($vehicals)){
                $this->sucess_data(['CODE'=>'200','INFO'=> $vehicals]);
            }else{
                $this->sucess_data(['CODE'=>'499','INFO'=> 'Insuffient Data']);
            }

        }
    }

    public function login(){
        if($this->http_method != 'POST'){
            $this->output_other(['CODE'=>'403'],403);
        }
        $params = $_POST;
        if(!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])){

            $user=$this->db->query("select * from tbl_toll_users where toll_employee_id = '{$params['username']}' AND toll_password = '{$params['password']}'");

            if(!empty($user)){
                $date = date('Y-m-d H:i:s', strtotime(' +1 day'));

                $access_token = hash('sha256', $user[0]['toll_employee_id'].$date);

                $this->db->query("UPDATE `tbl_toll_users` SET `access_token`= '$access_token' ,`expiry_date`='$date' WHERE toll_user_id =  {$user[0]['toll_user_id']}");
                //print_r($user); exit;
                $user=$this->db->query("select * from tbl_toll_users where toll_user_id = {$user[0]['toll_user_id']}");
                $this->sucess_data(['Code'=>'200','Info'=> ['toll'=> ['toll_id' => $this->Tollr['toll_id']],'user'=> $user[0]]]);
            }else{
                $this->sucess_data(['Code'=>'499','Info'=> 'No User']);
            }

        }else{
            $this->sucess_data(['Code'=>'499','Info'=> 'Insuffient Data']);
        }
    }

    public function load(){
        $params = $_POST;
        $tolls = ['toll_id' => $this->Tollr['toll_id'],'toll_name'=>$this->Tollr['toll_name'],'toll_stretch' => $this->Tollr['toll_stretch'],'motorway_id' => $this->Tollr['motorway_id'],'toll_location' => $this->Tollr['toll_location'],'toll_km'=> $this->Tollr['toll_km']];
        $userdetails=$this->userdetails(array('access_token'=>$_POST['access_token']));
        $vehicaldetails=$this->vehicaldetails(date('Y-m-d'));
        if(!empty($_POST) && isset($_POST['access_token']))
        {
            $data['tolldetails']=$tolls;
            $data['userdetails']=$userdetails[0];
            $data['vehicaldetails']=$vehicaldetails;
            $this->sucess_data(['Code'=>'200','Info'=> $data]);
        }else{
            $this->sucess_data(['Code'=>'499','Info'=> 'Insuffient Data']);
        }
    }
    public function report(){
        if($this->http_method != 'POST'){
            $this->output_other(['CODE'=>'403'],403);
        }
        $params=$_POST;

        if(isset($_FILES['vehical_picture']['name'])){
            $pic=$this->upload_image($_FILES['vehical_picture']);
        }
        if($_POST['report_type']==1){
            $data=array('trip_id'=>$_POST['trip_id'],'trip_detail_id'=>$_POST['trip_details_id'],'trip_type'=>$_POST['trip_type'],'report_type'=>1,'registration_no'=>$_POST['registration_no'],'vehical_type'=>$_POST['vehical_type'],'amount'=>$_POST['amount'],'pic'=>$pic,'status'=>1);
            $ins_report=$this->insert_entry("tbl_report", $data);
            if(isset($ins_report)){
                $update=$this->db->query("update tbl_trip_details set status=1,allowed_booth_id='".$_POST['booth_id']."',toll_user_id='".$_POST['toll_user_id']."',boothside_id='".$_POST['boothside_id']."'  where trip_details_id='".$_POST['trip_details_id']."'");
                //$this->curl_hit('updtd',['trip_details_id'=>$_POST['trip_details_id'],'allowed_booth_id' => $_POST['booth_id'],'toll_user_id' => $_POST['toll_user_id'],'boothside_id' => $_POST['boothside_id']]);
                $this->sucess_data(['Code'=>'200','Info'=> $data]);
            }else{
                $this->sucess_data(['Code'=>'499','Info'=> 'Insuffient Data']);
            }
        }else if($_POST['report_type']==2){
            $data=array('report_type'=>1,'registration_no'=>$_POST['registration_no'],'vehical_type'=>$_POST['vehical_type'],'amount'=>$_POST['amount'],'pic'=>$pic,'status'=>1);
            $ins_report=$this->insert_entry("tbl_report", $data);
            if(isset($ins_report)){
                /* $update=$this->db->query("update tbl_trip_details set status=1,allowed_booth_id='".$_POST['booth_id']."',toll_user_id='".$_POST['toll_user_id']."',boothside_id='".$_POST['boothside_id']."'  where trip_details_id='".$_POST['trip_details_id']."'");
                  $this->curl_hit('updtd',['trip_details_id'=>$_POST['trip_details_id'],'allowed_booth_id' => $_POST['booth_id'],'toll_user_id' => $_POST['toll_user_id'],'boothside_id' => $_POST['boothside_id']]);  */
                $this->sucess_data(['Code'=>'200','Info'=> $data]);
            }else{
                $this->sucess_data(['Code'=>'499','Info'=> 'Insuffient Data']);
            }
        }

    }
    private function upload_image($file){
        $target_dir = "./vehical_images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $allwoed_extentions = array('jpg', 'png', 'jpeg', 'gif', 'JPG', 'PNG', 'JPEG', 'GIF');
        $time=date('Ymdhis');
        $targetfile_name="report".$time.".jpg";
        $target_file = $target_dir.$targetfile_name;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        if (!in_array($imageFileType, $allwoed_extentions)) {
            return 'Problem with Upload data';
        } else {
            if (move_uploaded_file($_FILES['pic']["tmp_name"],$target_file)) {
                return $targetfile_name;
            }

        }

    }



    private function userdetails($data){
        $userdetails=$this->db->query("select * from tbl_toll_users where access_token = '".$data['access_token']."'");
        if(!empty($userdetails)){
            return $userdetails;
        }
    }

    private function vehicaldetails($date){
        $vehicals=$this->db->query("select * from tbl_user_vehicals as V JOIN tbl_trip_details as T ON V.user_id = T.user_id where date(T.updated_on)='$date' AND T.status <> 1 ORDER BY T.status DESC");
        if(!empty($vehicals)){
            return $vehicals;
        }
    }


    protected function curl_hit($url, $data)
    {
        $url = $this->server_url . $url;
        $fields_string = "";
        foreach ($data as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

//open connection
        $ch = curl_init();

//set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute post
        $result = curl_exec($ch);

//close connection
        curl_close($ch);
        return $result;
    }




}