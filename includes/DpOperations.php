<?php 
    class DbOperations{
        private $con; 
        function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }
        public function createUser($login, $password){
           if(!$this->isEmailExist($login)){
                $stmt = $this->con->prepare("INSERT INTO user (Login, Password) VALUES (?, ?)");
                $stmt->bind_param("ss", $login, $password);
                if($stmt->execute()){
                    return USER_CREATED; 
                }else{
                    return USER_FAILURE;
                }
           }
           return USER_EXISTS; 
        }
        private function isEmailExist($login){
            $stmt = $this->con->prepare("SELECT id FROM user WHERE Login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
        }
        public function userLogin($login, $password){
            if($this->isEmailExist($login)){
                $hashed_password = $this->getUsersPasswordByEmail($login); 
                if(password_verify($password, $hashed_password)){
                    return USER_AUTHENTICATED;
                }else{
                    return USER_PASSWORD_DO_NOT_MATCH; 
                }
            }else{
                return USER_NOT_FOUND; 
            }
        }

        
        public function getAllUsers(){
            $stmt = $this->con->prepare("SELECT id, Login FROM user");
            $stmt->execute(); 
            $stmt->bind_result($id, $login);
            $users = array(); 
            while($stmt->fetch()){ 
                $user = array(); 
                $user['ID'] = $id; 
                $user['Login']=$login; 
                array_push($users, $user);
            }             
            return $users; 
        }

        public function getUserByEmail($login){
            $stmt = $this->con->prepare("SELECT id, Login FROM user WHERE Login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute(); 
            $stmt->bind_result($id, $login);
            $stmt->fetch(); 
            $user = array(); 
            $user['ID'] = $id; 
            $user['Login']=$login; 
            return $user; 
        }

        private function getUsersPasswordByEmail($login){
            $stmt = $this->con->prepare("SELECT Password FROM user WHERE Login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute(); 
            $stmt->bind_result($password);
            $stmt->fetch(); 
            return $password; 
        }

        public function updateUser($email, $name, $school, $id){
            $stmt = $this->con->prepare("UPDATE users SET email = ?, name = ?, school = ? WHERE id = ?");
            $stmt->bind_param("sssi", $email, $name, $school, $id);
            if($stmt->execute())
                return true; 
            return false; 
        }

        public function deleteUser($id){
            $stmt = $this->con->prepare("DELETE FROM user WHERE Id = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute())
                return true; 
            return false; 
        }
        
    }