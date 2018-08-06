<?php

/**
 * This is only for logged in users
 * /api/medical_group_invite.
 */
class medical_group_invite extends frontend_controller
{
    /**
     * /api/medical_group_invite
     * list all invitations recevied by user.
     */
    public function index()
    {
        try {
            $user = $this->user;
            $pending_list = MedicalGroupInvitation::getInvitations(array('user_id' => $user->id, 'status' => 'PENDING'));
            echo json_encode(array('status' => true, 'pending_list' => $pending_list));
        } catch (Exception $ex) {
            echo json_encode(array('status' => false, 'message' => 'Invalid Data submitted.'.$ex->getMessage()));
        }
    }

    /**
     * /api/medical_group_invite/invite
     * bulk_invite and single invite
     * post: invite user to join group
     * params : group_id, emailid
     * save data into "group_invitations"
     * trigger email with secret.
     */
    public function invite()
    {
        try {
            $postdata = $this->input->post();
            $user = $this->user;
            $medical_group_id = $postdata['medical_group_id'];
            $mgroup = MedicalGroup::find_by_id($medical_group_id);
            //valid user &&&& valid medical group &&&& valid user for given group

            $errors = array(
                'mgroup' => $mgroup,
                'group_user' => $mgroup->checkForGroupUser($user->id, 0),
            );
            if (!empty($user) && !empty($mgroup) && ($mgroup->checkOwner($user->id) == true || $mgroup->checkForGroupUser($user->id, 0) == true)) {
                $invited_emailids = explode(',', $postdata['invited_emailid']);
                $count = 0;
                foreach ($invited_emailids as $emailid) {
                    $ginv = new MedicalGroupInvitation();
                    $ginv->email = $emailid;
                    $ginv->medical_group_id = $medical_group_id;

                    $save = $ginv->saveInvitation();
                    if ($save) {
                        ++$count;
                    }
                }
                echo json_encode(array('status' => true, 'message' => $count.' Invitation(s) Sent.'));
            } else {
                echo json_encode(array('status' => false, 'message' => 'Something went wrong. Please try again later.', 'errors' => $errors));
            }
        } catch (Exception $ex) {
            echo json_encode(array('status' => false, 'message' => 'Invalid Data submitted.'.$ex->getMessage()));
        }
    }

    /**
     * accept the invitation by the invited user.
     */
    public function invite_accept()
    {
        try {
            $postdata = $this->input->post();
            $user = $this->user;
            $invitation = new MedicalGroupInvitation();
            $invitation->email = $user->email;
            $invitation->user_id = $user->id;
            $invitation->secret = $postdata['invitation_key'];
            $inv_accept = $invitation->getAccepted();
            if ($inv_accept) {
                echo json_encode(array('status' => true, 'message' => 'success'));
            } else {
                echo json_encode(array('status' => false, 'message' => 'Something went wrong. Please try again later.'));
            }
        } catch (Exception $ex) {
            echo json_encode(array('status' => false, 'message' => 'Invalid Data submitted. '.$ex->getMessage()));
        }
    }

    /**
     * reject the invitation by the invited user.
     */
    public function invite_reject()
    {
        try {
            $postdata = $this->input->post();
            $user = $this->user;
            $invitation = new MedicalGroupInvitation();
            $invitation->email = $user->email;
            $invitation->user_id = $user->id;
            $invitation->secret = $postdata['invitation_key'];
            $inv_accept = $invitation->getReject();
            if ($inv_accept) {
                echo json_encode(array('status' => true, 'message' => 'success'));
            } else {
                echo json_encode(array('status' => false, 'message' => 'Something went wrong. Please try again later.'));
            }
        } catch (Exception $ex) {
            echo json_encode(array('status' => false, 'message' => 'Invalid Data submitted. '.$ex->getMessage()));
        }
    }
}
