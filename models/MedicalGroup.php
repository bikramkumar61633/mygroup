<?php
class MedicalGroup extends BaseModel{
    static $table="medical_groups";
    /**
     * check the logged user is admin or not for the supplied group
     */
    function getBasicInfo(){
		try{
			$info=array();
			$info['id']=$this->id;
			$info['name']=$this->name;
			$info['description']=$this->description;
			$info['type']=$this->type;
			$info['owner']=$this->owner;
			$info['active']=$this->active;
			return $info;
		}
		catch(Exception $e){

		}
    }
    /**
     * List all the active groups 
     * excluding the groups user registered with
     */
    static function getGroupList($user_id){
        $grouplist_received = MedicalGroupUsers::find_by_sql("SELECT gr.name, gr.description, gr.type, gr.image, gr.icon FROM medical_groups gr where gr.active=1 and gr.id not in(select medical_group_id from medical_group_users where user_id=".$user_id.")");
        $grouplist=array();
        if($grouplist_received!=null){
            foreach($grouplist_received as $group){
                $grouplist[]=array(
                    "id"=>$group->id,
                    "name"=>$group->name,
                    "description"=>$group->description,
                    "type"=>$group->type,
                    "image"=>$group->image,
                    "icon"=>$group->icon,
                    "active"=>$group->active
                );
            }
        }
        return $grouplist;
    }
    /**
     * List all the active groups by user id
     */
    static function getMyGroupList($user_id){
        try{   
            $grouplist_received = MedicalGroupUsers::find_by_sql("SELECT gru.id, gru.admin, gru.status, gru.active, gr.name, gr.description, gr.type, gr.image, gr.icon FROM medical_group_users gru left join medical_groups gr on gru.medical_group_id=gr.id where gru.user_id=".$user_id." and gru.active=1");
            $grouplist=array();
            if($grouplist_received!=null){
                foreach($grouplist_received as $group){
                    $grouplist[]=array(
                        "id"=>$group->id,
                        "admin"=>$group->admin,
                        "status"=>$group->status,
                        "active"=>$group->active,
                        "name"=>$group->name,
                        "description"=>$group->description,
                        "type"=>$group->type,
                        "image"=>$group->image,
                        "icon"=>$group->icon
                    );
                }
            }
            return $grouplist;
        }
        catch(Exception $ex){

        }
    }
    function checkOwner($user_id){
        if($this->owner==$user_id){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * check user registered with the group
     * check user exist for the grou $admin=0
     * check user is admin for the group $admin=1
     * return TRUE or FALSE
     */
    function checkForGroupUser($user_id,$admin=0){
        $check=MedicalGroupUsers::checkGroupUser(array(
            "user_id"=>$user_id,
            "medical_group_id"=>$this->id,
            "admin"=>$admin
        ));
        if($check){
            return TRUE;
        }
        return FALSE;
    }
}