<?php 
class medical_group_user extends frontend_controller{
    /**
     * list users for a group
     * validate the user should an active member of the group
     * params : group_id
     * get data from "group_users" by group_id
     * if owner has logged in then 
     */
    function index($group_id){
        try{
            $user=$this->user;
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            $makeadmin=0;
            
            //get group details
            $group_details=MedicalGroup::find_by_id($group_id);
            if(!empty($group_details)){
                $group_details=$group_details->getBasicInfo();
                
                //make admin flag if admin has logged in
                if($user->id==$group_details["owner"]){
                    $makeadmin=1;
                }

                //get group user details
                $group_users=MedicalGroupUsers::getActiveUserList(array("medical_group_id"=>$group_id));
                $response=array("status"=>TRUE,"group_user_list"=>$group_users,"group_info"=>$group_details,"owner"=>$makeadmin);
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    
    /**
     * any user can send join request for any group
     * params : user_id, group_id
     * validate the group type
     * if public
     * insert data into "group_users" table with status='ACCEPTED' and active=1
     * else
     * insert data into "group_users" table with status='PENDING' and active=0
     * trigger email to all the admins to approve the user to join
     */
    function join(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $medical_group_id=$postdata["medical_group_id"];
            $mgroup=MedicalGroup::find_by_id($medical_group_id);
            //valid user && valid medical group
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($user) && !empty($mgroup)){
                $create_user=MedicalGroupUsers::createUser(array("medical_group_id"=>$medical_group_id,"user_id"=>$user->id,"invitation_id"=>''));
                if($create_user){
                    $response=array("status"=>TRUE,"message"=>"Success");
                }
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /**
     * user leaving the group
     * params : user_id, group_user_id, along with note
     * validate the group_user_id
     */
    function leave(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && $group_user_data->user_id==$user->id){
                $group_user_data->active=0;
                $group_user_data->note=$postdata["note"];
                $group_user_data->save();
                $response=array("status"=>TRUE,"message"=>"Success");
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /**
     * validate the loggedin user should admin of the requested group
     * post : group_user_id 
     * update "group_users" table make status='APPROVED' and active=1
     * trigger email to the user
     */
    function approve(){ 
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);
            $approval=$group_user_data->getApprove();

            $group_data=MedicalGroup::find_by_id($group_user_data->medical_group_id);
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && ($group_data->checkForGroupUser($user->id,1)==TRUE || $group_data->checkOwner($user->id)==TRUE)){
                $group_user_data->active=1;
                $group_user_data->status="APPROVED";
                $group_user_data->save();
                $response=array("status"=>TRUE,"message"=>"Success");
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /**
     * validate the loggedin user should admin of the requested group
     * post : group_user_id 
     * update "group_users" table make status='REJECTED'
     * trigger email to the user
     * reject or suspend existing user by admin
     */
    function reject(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);
            $group_data=MedicalGroup::find_by_id($group_user_data->medical_group_id);
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && ($group_data->checkForGroupUser($user->id,1)==TRUE || $group_data->checkOwner($user->id)==TRUE)){
                $group_user_data->active=0;
                $group_user_data->status="REJECTED";
                $group_user_data->note=$postdata["note"];
                $group_user_data->save();
                $response=array("status"=>TRUE,"message"=>"Success");
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    
}