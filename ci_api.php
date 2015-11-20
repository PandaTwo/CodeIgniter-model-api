<?php
/**
 * Created by PhpStorm.
 * User: UserPC
 * Date: 2015/11/17
 * Time: 11:04
 */

class ci_api extends CI_Controller
{

     const MDL_END ='_mdl';
    function __construct()
    {
        parent :: __construct();
    }

	//get postData
    function get_post_data()
    {
        $postData = file_get_contents("php://input");
        $json = $this->json_clean_decode($postData,true);

        return $json;
    }
	
	//clean post json code
    function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {

        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

        if(version_compare(phpversion(), '5.4.0', '>=')) {
            $json = json_decode($json, $assoc, $depth, $options);
        }
        elseif(version_compare(phpversion(), '5.3.0', '>=')) {
            $json = json_decode($json, $assoc, $depth);
        }
        else {
            $json = json_decode($json, $assoc);
        }

        return $json;
    }


    /*
     * 访问api
     * */
    function call_function()
    {
        $postdata = $this->get_post_data();
        $modelName = $postdata['model_name'];
        $functionName =$postdata['function_name'];
        $parameters = isset($postdata['parameters']) ? $postdata['parameters'] : null;

        $mdl = $modelName.self::MDL_END;
        $mdl_name = 'm_'.$mdl;
        $this->load->model($mdl,'m_'.$mdl);

        if($parameters == null)
        {
            $data =  call_user_func(array($this->{$mdl_name},$functionName));
        }else{
            $data = call_user_func_array( array($this->{$mdl_name},$functionName) , $parameters);
        }
        echo json_encode($data);
    }

}