<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Manage_page_Model;

class Manage_page_Controller extends Controller
{
    var $Page_title = "Manage Page";
	var $Page_name  = "manage_page";
	var $Page_view  = "manage_page";
	var $Page_menu  = "manage_page";
	var $page_controllers = "manage_page";
	var $Page_tbl   = "tbl_page";
	public function index(Request $request,$action_type="")
	{
		/******************session***********************/
		$user_id = Session::get('admin_user_id');
		$user_type = Session::get('admin_user_type');
		/******************session***********************/
		$Page_title = $this->Page_title;
		$Page_name 	= $this->Page_name;
		$Page_view 	= $this->Page_view;
		$Page_menu 	= $this->Page_menu;
		$Page_tbl 	= $this->Page_tbl;
		$page_controllers 	= $this->page_controllers;
		//$this->Admin_Model->permissions_check_or_set($Page_title,$Page_name,$user_type);
		$data['title1'] = $Page_title." || View";
		$data['title2'] = "View";
		$data['Page_name'] = $Page_name;
		$data['Page_menu'] = $Page_menu;

		if($action_type==""){
			return redirect("vp-admin/".$page_controllers."/view");
		}

        $breadcrumbs = array(
            "Admin"=>"vp-admin/",
            "$Page_title"=>"vp-admin/$page_controllers/view",
            "View"=>"",);
        $data["breadcrumbs"] = $breadcrumbs;

		$tbl = $Page_tbl;

        $page_child_page = "";
        $data['page_child_page'] = $page_child_page;
        $data['page_url'] = "";

		$page_type = "page";

		if($action_type=="add"){
			$input = $request->all();
			if(!empty($input)){
				//print_r($input);
				$Manage_page_Model = new Manage_page_Model();

				$Manage_page_Model->title = $input['title'];
				$Manage_page_Model->page_type = $page_type;
				$Manage_page_Model->save();
				$id = $Manage_page_Model->id;

				return redirect("vp-admin/".$page_controllers."/edit?id=".$id);
			}
		}

		if($action_type=="edit"){
			$id = $_GET["id"];

			$input = $request->all();
			if(!empty($input['title'])){
				/********************************* */
				$Manage_page_Model = Manage_page_Model::find($id);

				$Manage_page_Model->title = $input['title'];
				$Manage_page_Model->page_type = $page_type;
				$Manage_page_Model->save();
			}

			/********************************* */
			$where = array('page_type'=>$page_type,'id'=>$id);
			$data["result"] = Manage_page_Model::where($where)->get();
		}

		if($action_type=="view"){
        	$where = array('page_type'=>$page_type);
  			$data["result"] = DB::table($tbl)->where($where)->get();
		}

		return view("vp-admin/$Page_view/$action_type")->with($data);
	}

	public function delete_page_rec()
	{
		$id = $_POST["id"];
		$Page_title = $this->Page_title;
		$Page_tbl = $this->Page_tbl;
		$result = $this->db->query("delete from $Page_tbl where id='$id'");
		if($result)
		{
			$message = "Delete Successfully.";
		}
		else
		{
			$message = "Not Delete.";
		}
		$message = $Page_title." - ".$message;
		$this->Admin_Model->Add_Activity_log($message);
		echo "ok";
	}
	public function check_url_api()
	{
		$Page_tbl 	= $this->Page_tbl;
		$id 		= $_POST["id"];
		$url 		= $_POST["url"];
		$child_page = $_POST["page_url"];
		if($child_page=="page"){
			$child_page = "";
		}
		$where = "";
		if($id!=""){
			$where = " and id!='$id'";
		}
		$query = $this->db->query("select id from $Page_tbl where url='$url' and child_page='$child_page' and page_type='page' $where")->row();
		if($query->id)
		{
			echo "Error";
		}
		else
		{
			echo "ok";
		}
	}
}
