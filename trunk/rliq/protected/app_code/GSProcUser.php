<?php
// Include TDbUserManager.php file which defines TDbUser
Prado::using('System.Security.TDbUserManager');
 
/**
 * GSProcUser Class.
 * GSProcUser represents the user data that needs to be kept in session.
 * Default implementation keeps username and role information.
 */
class GSProcUser extends TDbUser
{

   /**
     * Creates a BlogUser object based on the specified username.
     * This method is required by TDbUser. It checks the database
     * to see if the specified username is there. If so, a BlogUser
     * object is created and initialized.
     * @param string the specified username
     * @return BlogUser the user object, null if username is invalid.
     */
    public function createUser($username)
    {
        // use UserRecord Active Record to look for the specified username
        $userRecord=UserRecord::finder()->find('user_username = ?', $username);
        if($userRecord instanceof UserRecord) // if found
        {
            $user=new GSProcUser($this->Manager);
            $user->Name=$username;  // set username
            $userRoleRecord=UserRoleRecord::finder()->find('idtm_user_role = ?',$userRecord->idtm_user_role);
            $user->Roles=($userRoleRecord->idtm_user_role==1?'Administrator':$userRoleRecord->user_role_name); // set role
            $user->IsGuest=false;   // the user is not a guest
            return $user;
        }
        else
            return null;
    }
 
    /**
     * Checks if the specified (username, password) is valid.
     * This method is required by TDbUser.
     * @param string username
     * @param string password
     * @return boolean whether the username and password are valid.
     */
    public function validateUser($username,$password)
    {
        date_default_timezone_set('Europe/Berlin');
        $UserRecord = UserRecord::finder()->find('user_username = ? AND user_password = ?' ,$username,$password);
        if(count($UserRecord)==1){
            // use UserRecord Active Record to look for the (username, password) pair.
            $TempUserLog = new TTUserLogRecord();
            $TempUserLog->idtm_user = $UserRecord->idtm_user;
            $TempUserLog->ul_time = date("Y-m-d H:i:s");
            $TempUserLog->ul_status = "loggin";
            $TempUserLog->ul_ipadress = $_SERVER['HTTP_USER_AGENT'];
            $TempUserLog->save();
            return true;
        }else{
            $TempUserLog = new TTUserLogRecord();
            $TempUserLog->idtm_user = 0;
            $TempUserLog->ul_time = date("Y-m-d H:i:s");
            $TempUserLog->ul_status = "error loggin";
            $TempUserLog->ul_ipadress = $_SERVER['HTTP_USER_AGENT'];
            $TempUserLog->save();
            return false;
        }
    }
 
    /**
     * @return boolean whether this user is an administrator.
     */
    public function getIsAdmin()
    {
        return $this->isInRole('Administrator');
    }
    
    public function getUserId($username='')
    {
        if($username==''){
            $userRecord=UserRecord::finder()->find('user_username = ?', $this->Name);
        }else{
            $userRecord=UserRecord::finder()->find('user_username = ?', $username);
        }
        if(is_Object($userRecord)){
            return $userRecord->idtm_user;
        }else{
            return 0;
        }
    }
    
    public function getUserOrgId($userId){
        $UserRecord=OrganisationRecord::finder()->findByidtm_user($userId);
        if(count($UserRecord)==1){
            return $UserRecord->idtm_organisation;
        }else{
            return 0;
        }
    }

    public function getUserTheme($idtm_user,$modul){
        $myThemes = array(0=>"basic","golfplanner","hpartner","kulturplanner","npf","dieumsetzer");
        if(count(BerechtigungRecord::finder()->find('idtm_user = ? AND xx_modul = ?',$idtm_user,$modul))>0){
            $number = BerechtigungRecord::finder()->find('idtm_user = ? AND xx_modul = ?',$idtm_user,$modul)->xx_id;
        }else{
            $number = 0;
        }
        return $myThemes[$number];
    }

    public function getStartNode($idtm_user,$modul,$planungssicht=0){
        //hier muss noch eine pruefung hin, wenn es mehrere treffer gibt...
        if(count(BerechtigungRecord::finder()->find('idtm_user = ? AND xx_modul = ?',$idtm_user,$modul))>0){
            return BerechtigungRecord::finder()->find('idtm_user = ? AND xx_modul = ?',$idtm_user,$modul)->xx_id;
        }else{
            $SQL = "SELECT id".$modul." FROM ".$modul." WHERE parent_id".$modul." = 0";
            if($planungssicht>0){
                $SQL .= " AND idta_stammdatensicht = ".$planungssicht; //gilt nur für die planung
            }
            $SQL .= " LIMIT 1";
            $cleanmodul = preg_replace("/(^t[a-z]\_)/", "", $modul);
            preg_match("/(_[a-z])/", $cleanmodul, $matches);
            if(count($matches)>=1){
                $cleanmodul = preg_replace("/(_[a-z])/", ucfirst(substr($matches[1], 1, 1)), $cleanmodul);
            }
            $finderclass = ucfirst($cleanmodul)."Record";
            $Result = TActiveRecord::finder($finderclass)->findBySql($SQL);
            if(count($Result)>=1){
                $tmp = "id".$modul;
                return $Result->$tmp;
            }else{
                return 1;
            }
        }
    }

    public function getModulRights($ModulName){
        $CheckerRecord = BerechtigungRecord::finder()->find("idtm_user = ? AND xx_modul = ?",$this->getUserId(Prado::getApplication()->User->Name),$ModulName);
        if(count($CheckerRecord)!=1){
            return false;
        }else{
            if($CheckerRecord->xx_read == 1){
                return true;
            }else{
                return false;
            }
        }
    }

}
?>