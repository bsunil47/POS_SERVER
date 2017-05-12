<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/25/2016
 * Time: 5:58 PM
 */
class Toll
{
    public $db;
    public $base_url;
    private $server_url = "http://115.124.125.42/Tolls/api/2019/ps/";

    public function __construct()
    {
    }

    public function intialize()
    {
        if (!empty($_POST)) {
            $server_output = $this->curl_hit('l', ['toll_unique_number' => $_POST['toll_unique_id'], 'username' => $_POST['username'], 'password' => $_POST['spassword']]);
            if ($server_output) {
                $data = json_decode($server_output);
                if ($data->Code == 200) {
                    //print_r($data->Toll_users); exit;
                    include_once('sqlfile.php');
                    $conn = new mysqli($_POST['host'], $_POST['user'], $_POST['password']);
                    if ($conn->connect_errno) {
                        echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
                    } else {
                        if ($conn->query($sql)) {
                            $conn->select_db('Tollr_POS_Db');
                            if ($conn->query($sql_toll_user) && $conn->query($sql_user_details) && $conn->query($sql_trip_details) && $conn->query($sql_user_vehicals) && $conn->query($sql_vehical_types) && $conn->query($sql_report_table)) {
                                $conn->query("INSERT INTO `tbl_vehical_types` (`vehical_type_id`, `name`, `axel`) VALUES ('1', 'Bike', '1'), ('2', 'Car/Jeep/Van', '2'), ('3', 'LCV', '2'), ('4', 'Bus', '2'), ('5', 'up to 3 Axle Vehicle', '3'), ('6', '4 to 6 Axle ', '4'), ('7', 'HCM/EME', '5'), ('8', '7 or More', '7')");
                                $this->insert_data($conn,$data->Toll_users);
                                $file = 'config.php';
                                $params = ['db_array' => ['host' => $_POST['host'], 'username' => $_POST['user'], 'password' => $_POST['password'], 'db' => "Tollr_POS_Db", "port" => 3306, "prefix" => "",
                                    "charset" => "utf8"], 'Toll_details' => (array)$data->Info];
                                $current = '<?php $params = ' . var_export($params, TRUE) . ';';
                                // Write the contents back to the file
                                $fp = fopen($file,'w');
                                fwrite($fp, $current);
                                fclose($fp);
                                file_put_contents($file, $current);
                            }

                            header("Location:$this->base_url");
                        } else {
                            echo $conn->error;
                        }
                        exit;
                    }
                }
            }
        }
        echo $this->getRenderedHTML('install.php');

    }

    private function getRenderedHTML($path)
    {
        ob_start();
        include($path);
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute post
        $result = curl_exec($ch);

//close connection
        curl_close($ch);
        return $result;
    }

    private function insert_data($conn,$data_array){
        foreach($data_array as $row){
            $col_name ="";
            $col_values ="";
            foreach($row as $key => $value){
                $col_name .= "$key,";
                $col_values .= "'$value',";
            }

            $col_name = rtrim($col_name, ',');
            $col_values = rtrim($col_values, ',');
            //echo "INSERT INTO tbl_toll_users ($col_name) VALUES ($col_values)";
            $conn->query("INSERT INTO tbl_toll_users ($col_name) VALUES ($col_values)");
            $conn->error;
        }

        return true;
    }

}