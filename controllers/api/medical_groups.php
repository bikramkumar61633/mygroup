<?php 
use Hoa\Console\Chrome\Exception;

class medical_groups extends frontend_controller{
    /**
     * list of active groups available
     */
    function index(){
        try{
            $user=$this->user;
            $group_list=MedicalGroup::getGroupList($user->id);
            echo json_encode(array("status"=>TRUE,"group_list"=>$group_list));
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>false,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    
    /**
     * post: create new group 
     * params : group_name, type(private/public)
     * insert data into "groups" table
     * insert data into "group_users" table as group_id, user_id, group_admin=1, group_owner=1
     */
    function create(){
        try{
            $formdata=$this->input->post();
            $user=User::get_logged_in_user();
            if(!empty($user)){
				$data=$this->input->post();
                $mgroup=new MedicalGroup();
                $mgroup->name=$data['name'];
                $mgroup->type=$data['type'];
                $mgroup->description=$data['description'];
                $mgroup->owner=$user->id;
                $mgroup->save();
                echo json_encode(array('status'=>true,'message'=>'New Medical Group Saved'));
            }
            else{
                echo json_encode(array('status'=>false,'message'=>'Unauthorized access.'));
            }
        }
        catch(Exception $ex){
            echo json_encode(array('status'=>false,'message'=>'Invalid data submitted. '.$ex->getMessage()));
        }
    }
    /**
     * post: update existing medical group
     * group admin/owner can update the group details
     */
    function update(){
        try{ 
            $data=$this->input->post();
            $id=$data['id'];
            $mgroup=MedicalGroup::find_by_id($id);
            $user=User::get_logged_in_user();
            if(!empty($mgroup) && ($user->id==$mgroup->owner || $mgroup->checkForGroupUser($id,$user->id,1)==true)){
                $mgroup->name=$data['name'];
                $mgroup->type=$data['type'];
                $mgroup->description=$data['description'];
                $mgroup->updated_on=time();
                $mgroup->save();
                echo json_encode(array("status"=>true,"message"=>"Medical Group has been updated"));
            }
            else{
                echo json_encode(array("status"=>false,"message"=>"Something went wrong. Please try again later."));
            }
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>false,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /*
    *Deactive the existing group
    *Only Owner of the group can deactive the group [active = 0]
    */
    function deactive(){
        try{
            $data=$this->input->post();
            $user=User::get_logged_in_user();
            $id=$data['id'];
            $mgroup=MedicalGroup::find_by_id($id);
            //validate group and group owner
            if(!empty($mgroup) && ($mgroup->owner==$user->id)){
                $mgroup->active=0;
                $mgroup->save();
                echo json_encode(array("status"=>true,"message"=>"Medical group has been Deacivated"));
            }
            else{
                echo json_encode(array("status"=>false,"message"=>"Something went wrong. Please try again later."));
            }
        }
        catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
    /*
    *Activate the existing group
    *Only Owner of the group can deactive the group [active = 0]
    */
    function active(){
        try{
            $data=$this->input->post();
            $user=User::get_logged_in_user();
            $id=$data['id'];
            $mgroup=MedicalGroup::find_by_id($id);
            //validate group and group owner
            if(!empty($mgroup) && ($mgroup->owner==$user->id)){
                $mgroup->active=1;
                $mgroup->save();
                echo json_encode(array("status"=>true,"message"=>"Medical group has been Activated"));
            }
            else{
                echo json_encode(array("status"=>false,"message"=>"Something went wrong. Please try again later."));
            }
        }
        catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
    /**
     * get list of groups of logged in user
     */
    function mygroups(){
        try{
            $user=$this->user;
            $group_list=MedicalGroup::getMyGroupList($user->id);
            echo json_encode(array("status"=>TRUE,"group_list"=>$group_list));
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>false,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
}