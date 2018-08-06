<?php
class MedicalGroupUsers extends BaseModel{
    static $table="medical_group_users";
    /**
     * Create new user
     * static function as required in multiple places
     * required in accept invitation
     * required in join request
     */
    static function createUser($data){
        $groupdata=MedicalGroup::find_by_id($data["medical_group_id"]);
        // create the medical group user
        $group_user=new MedicalGroupUsers();
        if($groupdata->type=="PRIVATE"){
            $group_user->status="PENDING";
            $group_user->active=0;
        }
        $group_user->user_id=$data["user_id"];
        $group_user->medical_group_id=$data["medical_group_id"];
        $group_user->invitation_id=$data["invitation_id"];
        if($group_user->checkGroupUser(array("medical_group_id"=>$data["medical_group_id"],"user_id"=>$data["user_id"],"admin"=>0))==FALSE){
            $group_user->save();
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    /**
     * get the list of all active users of a supplied group
     * admin flag, email, name, user_id has been fetched
     */
    static function getActiveUserList($data,$admin=0){
        $conditions=array();
        if($admin==1){
            $conditions=array("conditions"=>array("medical_group_id=? and active=1 and admin=1 and status='APPROVED'",$data["medical_group_id"]));
        }
        else{
            $conditions=array("conditions"=>array("medical_group_id=? and active=1 and status='APPROVED'",$data["medical_group_id"]));
        }
        $group_users_data=MedicalGroupUsers::find("all",$conditions);
        $group_users=array();

        foreach($group_users_data as $user){
            $username=User::find_by_id($user->user_id)->get_basic_info();
            $group_users[]=array(
                "id"=>$user->id,
                "user_id"=>$user->user_id,
                "name"=>$username["name"],
                "image"=>$username["image"],
                "admin"=>$user->admin,
                "status"=>$user->status,
                "active"=>$user->active
            );
        }
        return $group_users;
    }
    /**
     * $data["medical_grou_id", "user_id","admin"]
     */
    static function checkGroupUser($data){
        if($data['admin']==1){
            $conditions=array("conditions"=>array("medical_group_id=? and user_id=? and active=1 and status='APPROVED' and admin=1",$data["medical_group_id"],$data["user_id"]));
        }
        else{
            $conditions=array("conditions"=>array("medical_group_id=? and user_id=? and active=1 and status='APPROVED'",$data["medical_group_id"],$data["user_id"]));
        }
        $check_data=MedicalGroupUsers::find("all",$conditions);
        if(!empty($check_data)){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * get the pending list of admin assign requests of user
     */
    static function getPendingAdminAssigns($data){
        $get_pendings=MedicalGroupUsers::find_by_sql("select * from medical_group_users where user_id=".$data["user_id"]." and admin_approval='".$data["admin_approval"]."'");
        $requestlist=array();
        if(!empty($get_pendings)){
            foreach($get_pendings as $li){
                $group_details=MedicalGroup::find_by_id($li->medical_group_id)->getBasicInfo();
                $owner=User::find_by_id($group_details["owner"])->get_basic_info();
                $requestlist[]=array(
                    "id"=>$li->id,
                    "group_info"=>$group_details,
                    "owner"=>$owner["name"]
                );
            }
        }
        return $requestlist;
    }
}