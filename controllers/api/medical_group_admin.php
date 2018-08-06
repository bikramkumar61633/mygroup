<?php 
class medical_group_admin extends frontend_controller{
    /**
     * list admins for a group
     * params : group_id
     * owner can access the list
     * admin can access the list
     * /api/medical_group_admin/index/group/[group_id]
     */
    function index($medical_group_id){
        try{
            $user=$this->user;
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            $makeadmin=0;
            if($medical_group_id>0){
                //get group details
                $mgroup=MedicalGroup::find_by_id($medical_group_id);
                if(!empty($mgroup) &&( $user->id==$mgroup->owner || $mgroup->checkForGroupUser($user->id,1)==true)){
                    $mgroup=$mgroup->getBasicInfo();
                    //get group user details
                    $group_users=MedicalGroupUsers::getActiveUserList(array("medical_group_id"=>$medical_group_id),1);
                    $response=array("status"=>TRUE,"group_admin_list"=>$group_users,"group_info"=>$mgroup);
                }
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /**
     * post: assign new admin for a group 
     * params : group_user_id
     * trigger email to the user for assign admin accept
     */
    function assign(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //get the group user id
            $mgroup_user=MedicalGroupUsers::find_by_id($group_user_id);
            //get the group
            $mgroup=MedicalGroup::find_by_id($mgroup_user->medical_group_id);
            //validation for GROUP_USER_ID && OWNER && 
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($mgroup_user) && $mgroup->checkOwner($user->id)==TRUE && $mgroup_user->active==1){
                $mgroup_user->admin_approval="PENDING";
                $mgroup_user->save();
                $response=array("status"=>TRUE,"message"=>"Success");
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /**
     * get: list of admin assign pending requests for a user
     */
    function get_pending_assigns(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $admin_assign_requests=MedicalGroupUsers::getPendingAdminAssigns(array("user_id"=>$user->id,"admin_approval"=>"PENDING"));
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($admin_assign_requests)){
                $response=array("status"=>TRUE,"message"=>"Success","pending_assign_request"=>$admin_assign_requests);
            }
            echo json_encode($response);
        }
        catch(Exception $ex){
            echo json_encode(array("status"=>FALSE,"message"=>"Invalid Data submitted.".$ex->getMessage()));
        }
    }
    /**
     * params : group_user_id
     * if data exist update "group_users" with group_admin=1
     * update "group_admin_invitations" table with status='ACCEPTED'
     * trigger email to the sender of acceptance
     */
    function accept_assign(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);

            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && $group_user_data->user_id==$user->id && $group_user_data->admin_approval=="PENDING"){
                $group_user_data->admin=1;
                $group_user_data->admin_approval="ACCEPTED";
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
     * params : group_user_id
     * if data exist update "group_users" with admin=0
     * update "group_admin_invitations" table with status='REJECTED'
     * trigger email to the sender of acceptance
     */
    function reject_assign(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);

            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && $group_user_data->user_id==$user->id && $group_user_data->admin_approval=="PENDING"){
                $group_user_data->admin_approval="REJECTED";
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
     * post: unassign admin for group
     * params : group_user_id
     * trigger email to the group admin user of unassigning admin role
     * update "group_users" table with group_admin=0
     */
    function unassign(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);
            $group_data=MedicalGroup::find_by_id($group_user_data->medical_group_id);

            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && $group_data->owner==$user->id && $group_user_data->admin==1){
                $group_user_data->admin=0;
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
     * post: leave group for admin (unassign admin/leave group)
     * params : group_user_id
     * trigger email to the group admin user of unassigning admin role
     * update "group_users" table with group_admin=0
     */
    function leave(){
        try{
            $postdata=$this->input->post();
            $user=$this->user;
            $group_user_id=$postdata["group_user_id"];
            //validate the group user id submitted
            $group_user_data=MedicalGroupUsers::find_by_id($group_user_id);
            $response=array("status"=>FALSE,"message"=>"Something went wrong. Please try again later.");
            if(!empty($group_user_data) && $group_user_data->admin==1 && $group_user_data->user_id==$user->id){
                $group_user_data->admin=0;
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