<?php
function getEmailData($type,$email_data){
	switch($type){
		case 'welcome_patient':
			return $email_data;
		break;
		case 'appointment_booked_patient':
			$appointment_id=$email_data['appointment_id'];
			$app=Appointment::find_by_id($appointment_id);
			$data=array();
			$user=$app->user;
			$doctor=$app->getDoctorDetails();
			$time=$app->getFormatedDate();
			$data['name']=$user->get_name();
			$data['doctor']=$doctor['info']['name'];
			$data['date']=$time;
			return $data;
		break;
		case 'appointment_confirmed_patient':
			$appointment_id=$email_data['appointment_id'];
			$app=Appointment::find_by_id($appointment_id);
			$data=array();
			$user=$app->user;
			$doctor=$app->getDoctorDetails();
			$time=$app->getFormatedDate();
			$data['name']=$user->get_name();
			$data['doctor']=$doctor['info']['name'];
			$data['date']=$time;
			return $data;
		break;
		case 'appointment_cancelled_patient':
			$appointment_id=$email_data['appointment_id'];
			$app=Appointment::find_by_id($appointment_id);
			$data=array();
			$user=$app->user;
			$doctor=$app->getDoctorDetails();
			$time=$app->getFormatedDate();
			$data['name']=$user->get_name();
			$data['doctor']=$doctor['info']['name'];
			$data['date']=$time;
			return $data;
		break;
		case 'record_request_doctor':
			$record_id=$email_data['record_id'];
			$app=PatientRecord::find_by_id($record_id);
			$data=array();
			$doctor=$app->doctor;
			$patient=$app->patient;
			$time=date("l, F d",strtotime($app->created_on))." at ".date("h:i A",strtotime($app->created_on)) ;
			$data['doctor_name']=$doctor->get_name();
			$data['patient_name']=$patient->get_name();
			$data['date']=$time;
			return $data;
		break;
		case 'clinic_welcome_provider':
			$clinic_doctor_id=$email_data['clinic_doctor_id'];
			$clinic_doctor=ClinicDoctor::find_by_id($clinic_doctor_id);
			
			if(!empty($clinic_doctor)){
				$clinic=$clinic_doctor->clinic;
				$doctor=$clinic_doctor->doctor;
				$data['clinic']=$clinic;
				$data['doctor']=$doctor;
				return $data;
			}
		break;
		case 'clinic_invite_provider':
			$invitation=$email_data['invitation'];
			$user_invite=UserInvite::find_by_user_id_and_active($invitation['user_id'],1);
			if(!empty($user_invite)){
				$clinic=Clinic::find_by_id($email_data['clinic_id']);
				$data['clinic']=$clinic;
				$data['doctor']=User::find_by_id($invitation['user_id']);
				$data['invitation_id']=$user_invite->invitation_code;
				return $data;
			}
		break;
		case 'clinic_invite_user':
			$invitation=$email_data['invitation'];
			$user_invite=UserInvite::find_by_user_id_and_active($invitation['user_id'],1);
			if(!empty($user_invite)){
				$clinic=Clinic::find_by_id($email_data['clinic_id']);
				$data['clinic']=$clinic;
				$data['user']=User::find_by_id($invitation['user_id']);
				$data['invitation_id']=$user_invite->invitation_code;
				return $data;
			}
		break;
		case 'clinic_welcome_user':
			$clinic=Clinic::find_by_id($email_data['clinic_id']);
			$data['clinic']=$clinic;
			$data['user']=User::find_by_id($email_data['user_id']);
			return $data;
			
		break;
		case 'clinic_invite_patient':
			$invitation=$email_data['invitation'];
			$user_invite=PatientInvite::find_by_id($invitation['id']);
			if(!empty($user_invite)){
				$data['from']=$user_invite->from;
				$clinic=Clinic::find_by_id($email_data['clinic_id']);
				$data['invitation_id']=$user_invite->invitation_code;
				$data['clinic']=$clinic;
				$data['user']=User::find_by_id($user_invite->user_id);
				return $data;
			}
		break;
		case 'claim_profile_provider':
			$claim=$email_data['claim'];
			$claim_data=DoctorProfileClaim::find_by_id_and_active($claim['id'],1);
			if(!empty($claim_data)){
				$data['user']=User::find_by_id($claim_data->user_id);
				$data['activation']=$claim_data->activation_code;
				return $data;
			}
		break;
		case 'profile_verification_request':
			$claim=$email_data['user'];
			$user=User::find_by_id($claim['id']);
			if(!empty($user)){
				$data['user']=$user;
				$data['phone']=$claim['phone'];
				$data['email']=$claim['email'];
				return $data;
			}
		break;
		case 'request_profile_access':
			$invitation_id=$email_data['invitation']['id'];
			$uli=UserLynkInvite::find_by_id($invitation_id);
			if(!empty($uli)){
				$invitee=User::find_by_id($uli->invited_by);
				$data['invitee']=$invitee;
				$data['receiver']=array("name"=>$uli->first_name);
				$data['link']=$email_data['link'];
				return $data;
			}
		break;
		case 'share_profile_access':
			$invitation_id=$email_data['invitation']['id'];
			$uli=UserLynkInvite::find_by_id($invitation_id);
			if(!empty($uli)){
				$invitee=User::find_by_id($uli->invited_by);
				$data['invitee']=$invitee;
				$data['receiver']=array("name"=>$uli->first_name);
				$data['link']=$email_data['link'];
				return $data;
			}
		break;
		case 'clinic_patient_upload':
			$count=$email_data['count'];
			$data['count']=$count;
			$data['user']=User::find_by_id($email_data['user']);
			return $data;
		break;
		//doctor Link
		case 'doctor_lynk_provider':
			$invitation=$email_data['invitation'];
			$user_link=User::find_by_id($invitation['user']);
			$linkData=DoctorLink::find_by_id($invitation['id']);
			$link_requested=User::find_by_id($invitation['requested_user']);
			if(!empty($user_link)){
				$data['user']=$user_link->username;
				$data['requested_user']=$link_requested->username;
				$data['link_id']=$invitation['id'];
				$data['link_id']=$linkData['secret'];
				return $data;
			}
		break;
		case 'doctor_lynk_accepted':
			$invitation=$email_data['invitation'];
			$user_link=User::find_by_id($invitation['user']);
			$linkData=DoctorLink::find_by_id($invitation['id']);
			$link_requested=User::find_by_id($invitation['requested_user']);
			if(!empty($user_link)){
				$data['user']=$user_link->username;
				$data['requested_user']=$link_requested->username;
				$data['link_id']=$invitation['id'];
				return $data;
			}
		break;
		case 'doctor_lynk_rejected':
			$invitation=$email_data['invitation'];
			$user_link=User::find_by_id($invitation['user']);
			$linkData=DoctorLink::find_by_id($invitation['id']);
			$link_requested=User::find_by_id($invitation['requested_user']);
			if(!empty($user_link)){
				$data['user']=$user_link->username;
				$data['requested_user']=$link_requested->username;
				$data['link_id']=$invitation['id'];
				return $data;
			}
		break;
		case 'medical_group_invitation':
			$invitation=$email_data['invitation'];
			$group_info=$invitation["group_info"];
			$sender=$invitation["sender"];
			if(!empty($user_link)){
				$data['sender_name']=$sender["name"];
				$data['group_name']=$group_info["name"];
				$data['group_description']=$group_info["description"];
				$data['link']=$email_data['join_link'];
				return $data;
			}
		break;
	}
}
?>