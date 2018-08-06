<?php
class MedicalGroupInvitation extends BaseModel{
    static $table="medical_group_invitations";
    /**
     * get list of pending invitations
     */
    static function getInvitations($data){
        $list=MedicalGroupInvitation::find_by_sql("SELECT gr.name, gr.image, gri.id, gri.medical_group_id, gri.user_id, gri.email, gri.status, gri.active, gri.created_by, gri.created_on, gri.secret FROM `medical_group_invitations` gri left join medical_groups gr on gri.medical_group_id=gr.id where gri.user_id=".$data["user_id"]." and gri.status='".$data["status"]."'");
        $invitation_list=array();
        if(!empty($list)){
            foreach($list as $li){
                $invitation_list[]=array(
                    "id"=>$li->id,
                    "name"=>$li->name,
                    "image"=>$li->image,
                    "status"=>$li->status,
                    "secret"=>$li->secret
                );
            }
        }
        return $invitation_list;
    }
    /**
     * save invitations
     */
    function saveInvitation(){
        $registered=$this->checkRegistered();
        $this->type="USER";
        $this->active=1;
        if($this->checkPendingInvitation()){
            $this->secret=md5(date("YmdHis").$this->email.$this->user_id.$this->medical_group_id);
            $this->save();
            //send invitation email
            $this->sendInvitationEmail();
            return TRUE;
        }
        else{
            return FALSE    ;
        }
    }
    /**
     * check the supplied email id exist or not in users
     * if exist return id else return false
     */
    function checkRegistered(){
        $registered_user=User::find('all',array('conditions' => array('email=? and active=1',$this->email)));
        $registered_user_id='';
        if(!empty($registered_user)){
            foreach($registered_user as $user){
                $registered_user_id=$user->id;
            }
            $this->user_id=$registered_user_id;
        }
        return null;
    }
    /**
     * check if pending invitation is there for the user
     * for medical_group_id and user_id if the active = 0 then only another invitation can initiate
     * if active=1 for particular user return false
     */
    function checkPendingInvitation(){
        $checkarray=array(
            "medical_group_id"=>$this->medical_group_id,
            "active"=>$this->active,
            "type"=>$this->type
        );
        if($this->user_id!=null){
            $checkarray["user_id"]=$this->user_id;
        }
        else{
            $checkarray["user_id"]=NULL;
            $checkarray["email"]=$this->email;
        }
        if($this->getCount($checkarray)==true){
            return false;
        }
        return true;
    }
    /**
     * check invitation secret and userid
     */
    function validateInvitationSecret(){
        $invitation=MedicalGroupInvitation::find('all',array('conditions' => array('(user_id=? or email=?) and secret=? and active=1',$this->user_id,$this->email,$this->secret)));
        if(!empty($invitation)){
            foreach($invitation as $invt){
                return $invt->id;
            }
        }
        else{
            return false;
        }
    }
    /**
     * ACCEPT THE GROUP JOIN INVITATION
     */
    function getAccepted(){
        //check for the requested invitation available or not
        // $get_invite_id=$this->validateInvitationSecret();
        $invitation=MedicalGroupInvitation::find('all',array('conditions' => array('(user_id=? or email=?) and secret=? and active=1',$this->user_id,$this->email,$this->secret)));
        
        if(!empty($invitation)){
            foreach($invitation as $inv){
                $inv->status="ACCEPTED";
                $inv->active=0;
                $inv->updated_on=time();
                $inv->save();
                MedicalGroupUsers::createUser(array("medical_group_id"=>$inv->medical_group_id,"user_id"=>$inv->user_id,"invitation_id"=>$inv->id));
            }
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    /**
     * Reject group join invitations
     */
    function getReject(){
        //check for the requested invitation available or not
        // $get_invite_id=$this->validateInvitationSecret();
        $invitation=MedicalGroupInvitation::find('all',array('conditions' => array('(user_id=? or email=?) and secret=? and active=1',$this->user_id,$this->email,$this->secret)));
        
        if(!empty($invitation)){
            foreach($invitation as $inv){
                $inv->status="REJECTED";
                $inv->active=0;
                $inv->updated_on=time();
                $inv->save();
            }
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    /**
     * send invitation email
     */
    function sendInvitationEmail(){
        $sender=User::find_by_id($this->created_by)->get_basic_info();
        $group=MedicalGroup::find_by_id($this->medical_group_id)->getBasicInfo();
        $name="";
        $invited=array("email"=>$this->email);
        if($this->user_id!=NULL){
            $invited=User::find_by_id($this->user_id)->get_basic_info();
            $name=$invited["name"];
        }
        $worker=new BackgroundWorker();
		$task_data=array(
			'type'=>'EMAIL',
			'email_data'=>array(
				"subject"=>$sender["name"]." has invited you to join ".$group["name"]." Group at HealthLynked",
				"email"=>$sender["email"],
				"invitation"=>array(
                    "group_info"=>$group,
                    "sender"=>$sender,
                    "invited"=>$invited,
                    "secret"=>$this->secret,
                    "join_link"=>$this->generateInviteLink()
				)
			),
			"email_type"=>"medical_group_invitation",
			"receiver"=>array("name"=>$name,"email"=>$this->email)
        );
		$worker->push('SENDEMAIL',$task_data);
    }
    /**
     * generate the invitation link
     */
    function generateInviteLink(){
		return "https://app.healthlynked.com/#!/group_join_invite/".$this->secret."?return_url=access_control";
	}
}