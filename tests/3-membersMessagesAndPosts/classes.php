<?php

/**
 * @name Base_Member
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Member
{
    // Table name
    const TABLENAME = 'member';
    
    // Fields name
    const FIELDNAME_IDMEMBER = 'idmember';
    const FIELDNAME_LOGIN = 'login';
    const FIELDNAME_ISADMIN = 'isadmin';
    const FIELDNAME_PASSWORD = 'password';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idMember;
    
    /** @var string  */
    protected $_login;
    
    /** @var bool  */
    protected $_isAdmin;
    
    /** @var string  */
    protected $_password;
    
    /**
     * Construct a member
     * @param $pdo PDO 
     * @param $idMember int 
     * @param $login string 
     * @param $password string 
     * @param $isAdmin bool 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idMember,$login,$password,$isAdmin=false,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idMember = $idMember;
        $this->_login = $login;
        $this->_password = $password;
        $this->_isAdmin = $isAdmin;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idMember] = $this;
        }
    }
    
    /**
     * Create a member
     * @param $pdo PDO 
     * @param $login string 
     * @param $password string 
     * @param $isAdmin bool 
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public static function create(PDO $pdo,$login,$password,$isAdmin=false,$lazyload=true)
    {
        // Add the member into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Member::TABLENAME.' ('.Member::FIELDNAME_LOGIN.','.Member::FIELDNAME_PASSWORD.','.Member::FIELDNAME_ISADMIN.') VALUES (?,?,?)');
        if (!$pdoStatement->execute(array($login,$password,$isAdmin))) {
            throw new Exception('Error while inserting a member into database');
        }
        
        // Construct the member
        return new Member($pdo,intval($pdo->lastInsertId()),$login,$password,$isAdmin,$lazyload);
    }
    
    /**
     * Count members
     * @param $pdo PDO 
     * @return int Number of members
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Member::FIELDNAME_IDMEMBER.') FROM '.Member::TABLENAME))) {
            throw new Exception('Error while counting members in database');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Select query
     * @param $pdo PDO 
     * @param $where string|array 
     * @param $orderby string|array 
     * @param $limit string|array 
     * @param $from string|array 
     * @return PDOStatement 
     */
    protected static function _select(PDO $pdo,$where=null,$orderby=null,$limit=null,$from=null)
    {
        return $pdo->prepare('SELECT DISTINCT '.Member::TABLENAME.'.'.Member::FIELDNAME_IDMEMBER.', '.Member::TABLENAME.'.'.Member::FIELDNAME_LOGIN.', '.Member::TABLENAME.'.'.Member::FIELDNAME_PASSWORD.', '.Member::TABLENAME.'.'.Member::FIELDNAME_ISADMIN.' '.
                             'FROM '.Member::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a member
     * @param $pdo PDO 
     * @param $idMember int 
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public static function load(PDO $pdo,$idMember,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idMember])) {
            return self::$_lazyload[$idMember];
        }
        
        // Load the member
        $pdoStatement = self::_select($pdo,Member::FIELDNAME_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($idMember))) {
            throw new Exception('Error while loading a member from database');
        }
        
        // Fetch the member from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load a member by its login
     * @param $pdo PDO 
     * @param $login string 
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public static function loadByLogin(PDO $pdo,$login,$lazyload=true)
    {
        // Load the member
        $pdoStatement = self::_select($pdo,Member::FIELDNAME_LOGIN.' = ?');
        if (!$pdoStatement->execute(array($login))) {
            throw new Exception('Error while loading a member by its login from database');
        }
        
        // Fetch the member from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Member::FIELDNAME_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($this->_idMember))) {
            throw new Exception('Error while reloading data of a member from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idMember,$login,$password,$isAdmin) = $values;
        
        // Save values
        $this->_login = $login;
        $this->_password = $password;
        $this->_isAdmin = $isAdmin;
    }
    
    /**
     * Load all members
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Member[] Array of members
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all members
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the members
        $members = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $members;
    }
    
    /**
     * Select all members
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all members from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next member from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idMember,$login,$password,$isAdmin) = $values;
        
        // Construct the member
        return $lazyload && isset(self::$_lazyload[intval($idMember)]) ? self::$_lazyload[intval($idMember)] :
               new Member($pdo,intval($idMember),$login,$password,$isAdmin ? true : false,$lazyload);
    }
    
    /**
     * Fetch all the members from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Member[] Array of members
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $members = array();
        while ($member = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $members[] = $member;
        }
        return $members;
    }
    
    /**
     * Equality test
     * @param $member Member 
     * @return bool Objects are equals ?
     */
    public function equals($member)
    {
        // Test if null
        if ($member == null) { return false; }
        
        // Test class
        if (!($member instanceof Member)) { return false; }
        
        // Test ids
        return $this->_idMember == $member->_idMember;
    }
    
    /**
     * Check if the member exists in database
     * @return bool The member exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Member::FIELDNAME_IDMEMBER.') FROM '.Member::TABLENAME.' WHERE '.Member::FIELDNAME_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($this->getIdMember()))) {
            throw new Exception('Error while checking that a member exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete member
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated sents
        $select = $this->selectSents();
        while ($sent = Message::fetch($this->_pdo,$select)) {
            $sent->delete();
        }
        
        // Delete associated receiveds
        $select = $this->selectReceiveds();
        while ($received = Message::fetch($this->_pdo,$select)) {
            $received->delete();
        }
        
        // Delete associated posts
        foreach ($this->loadPosts() as $post) {
            $post->delete();
        }
        
        // Delete member
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Member::TABLENAME.' WHERE '.Member::FIELDNAME_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($this->getIdMember()))) {
            throw new Exception('Error while deleting a member in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idMember])) {
            unset(self::$_lazyload[$this->_idMember]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update a field in database
     * @param $fields array 
     * @param $values array 
     * @return bool Successful operation ?
     */
    protected function _set($fields,$values)
    {
        // Prepare update
        $updates = array();
        foreach ($fields as $field) {
            $updates[] = $field.' = ?';
        }
        
        // Update field
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Member::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Member::FIELDNAME_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdMember())))) {
            throw new Exception('Error while updating a member\'s field in database');
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update all fields in database
     * @return bool Successful operation ?
     */
    public function update()
    {
        return $this->_set(array(Member::FIELDNAME_LOGIN,Member::FIELDNAME_ISADMIN,Member::FIELDNAME_PASSWORD),array($this->_login,$this->_isAdmin,$this->_password));
    }
    
    /**
     * Get the idMember
     * @return int 
     */
    public function getIdMember()
    {
        return $this->_idMember;
    }
    
    /**
     * Get the login
     * @return string 
     */
    public function getLogin()
    {
        return $this->_login;
    }
    
    /**
     * Set the login
     * @param $login string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setLogin($login,$execute=true)
    {
        // Save into object
        $this->_login = $login;
        
        // Save into database (or not)
        return $execute ? Member::_set(array(Member::FIELDNAME_LOGIN),array($login)) : true;
    }
    
    /**
     * Get the isAdmin
     * @return bool 
     */
    public function getIsAdmin()
    {
        return $this->_isAdmin;
    }
    
    /**
     * Set the isAdmin
     * @param $isAdmin bool 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setIsAdmin($isAdmin,$execute=true)
    {
        // Save into object
        $this->_isAdmin = $isAdmin;
        
        // Save into database (or not)
        return $execute ? Member::_set(array(Member::FIELDNAME_ISADMIN),array($isAdmin)) : true;
    }
    
    /**
     * Get the password
     * @return string 
     */
    public function getPassword()
    {
        return $this->_password;
    }
    
    /**
     * Set the password
     * @param $password string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setPassword($password,$execute=true)
    {
        // Save into object
        $this->_password = $password;
        
        // Save into database (or not)
        return $execute ? Member::_set(array(Member::FIELDNAME_PASSWORD),array($password)) : true;
    }
    
    /**
     * Select sents
     * @return PDOStatement 
     */
    public function selectSents()
    {
        return Message::selectBySender($this->_pdo,$this);
    }
    
    /**
     * Select receiveds
     * @return PDOStatement 
     */
    public function selectReceiveds()
    {
        return Message::selectByRecipient($this->_pdo,$this);
    }
    
    /**
     * Load posts
     * @return Post[] Array of posts
     */
    public function loadPosts()
    {
        // Init array
        $posts = array();
        
        // Add questions to array
        $select = Question::selectByMember($this->_pdo,$this);
        while ($question = Question::fetch($this->_pdo,$select)) {
            $posts[] = $question;
        }
        
        // Add answers to array
        $select = Answer::selectByMember($this->_pdo,$this);
        while ($answer = Answer::fetch($this->_pdo,$select)) {
            $posts[] = $answer;
        }
        
        // Return array
        return $posts;
    }
    
    /**
     * ToString
     * @return string String representation of member
     */
    public function __toString()
    {
        return '[Member idMember="'.$this->_idMember.'" login="'.$this->_login.'" isAdmin="'.($this->_isAdmin?'true':'false').'" password="'.$this->_password.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of member
     */
    public function serialize($serialize=true)
    {
        // Serialize the member
        $array = array('idmember' => $this->_idMember,'login' => $this->_login,'password' => $this->_password,'isadmin' => $this->_isAdmin);
        
        // Return the serialized (or not) member
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of member
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the member
        return $lazyload && isset(self::$_lazyload[$array['idmember']]) ? self::$_lazyload[$array['idmember']] :
               new Member($pdo,$array['idmember'],$array['login'],$array['password'],$array['isadmin'],$lazyload);
    }
    
}

/**
 * @name Member
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Member extends Base_Member
{
    
    // Put your code here...
    
}

/**
 * @name Base_Message
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Message
{
    // Table name
    const TABLENAME = 'message';
    
    // Fields name
    const FIELDNAME_IDMESSAGE = 'idmessage';
    const FIELDNAME_SENDER_IDMEMBER = 'sender_idmember';
    const FIELDNAME_RECIPIENT_IDMEMBER = 'recipient_idmember';
    const FIELDNAME_TITLE = 'title';
    const FIELDNAME_DATE = 'date';
    const FIELDNAME_CONTENT = 'content';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idMessage;
    
    /** @var int sender's id */
    protected $_sender;
    
    /** @var int recipient's id */
    protected $_recipient;
    
    /** @var string  */
    protected $_title;
    
    /** @var int  */
    protected $_date;
    
    /** @var string  */
    protected $_content;
    
    /**
     * Construct a message
     * @param $pdo PDO 
     * @param $idMessage int 
     * @param $sender int sender's id
     * @param $recipient int recipient's id
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idMessage,$sender,$recipient,$title,$date,$content,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idMessage = $idMessage;
        $this->_sender = $sender;
        $this->_recipient = $recipient;
        $this->_title = $title;
        $this->_date = $date;
        $this->_content = $content;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idMessage] = $this;
        }
    }
    
    /**
     * Create a message
     * @param $pdo PDO 
     * @param $sender Member 
     * @param $recipient Member 
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @param $lazyload bool Enable lazy load ?
     * @return Message 
     */
    public static function create(PDO $pdo,Member $sender,Member $recipient,$title,$date,$content,$lazyload=true)
    {
        // Add the message into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Message::TABLENAME.' ('.Message::FIELDNAME_SENDER_IDMEMBER.','.Message::FIELDNAME_RECIPIENT_IDMEMBER.','.Message::FIELDNAME_TITLE.','.Message::FIELDNAME_DATE.','.Message::FIELDNAME_CONTENT.') VALUES (?,?,?,?,?)');
        if (!$pdoStatement->execute(array($sender->getIdMember(),$recipient->getIdMember(),$title,date('Y-m-d H:i:s',$date),$content))) {
            throw new Exception('Error while inserting a message into database');
        }
        
        // Construct the message
        return new Message($pdo,intval($pdo->lastInsertId()),$sender->getIdMember(),$recipient->getIdMember(),$title,$date,$content,$lazyload);
    }
    
    /**
     * Count messages
     * @param $pdo PDO 
     * @return int Number of messages
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Message::FIELDNAME_IDMESSAGE.') FROM '.Message::TABLENAME))) {
            throw new Exception('Error while counting messages in database');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Select query
     * @param $pdo PDO 
     * @param $where string|array 
     * @param $orderby string|array 
     * @param $limit string|array 
     * @param $from string|array 
     * @return PDOStatement 
     */
    protected static function _select(PDO $pdo,$where=null,$orderby=null,$limit=null,$from=null)
    {
        return $pdo->prepare('SELECT DISTINCT '.Message::TABLENAME.'.'.Message::FIELDNAME_IDMESSAGE.', '.Message::TABLENAME.'.'.Message::FIELDNAME_SENDER_IDMEMBER.', '.Message::TABLENAME.'.'.Message::FIELDNAME_RECIPIENT_IDMEMBER.', '.Message::TABLENAME.'.'.Message::FIELDNAME_TITLE.', '.Message::TABLENAME.'.'.Message::FIELDNAME_DATE.', '.Message::TABLENAME.'.'.Message::FIELDNAME_CONTENT.' '.
                             'FROM '.Message::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a message
     * @param $pdo PDO 
     * @param $idMessage int 
     * @param $lazyload bool Enable lazy load ?
     * @return Message 
     */
    public static function load(PDO $pdo,$idMessage,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idMessage])) {
            return self::$_lazyload[$idMessage];
        }
        
        // Load the message
        $pdoStatement = self::_select($pdo,Message::FIELDNAME_IDMESSAGE.' = ?');
        if (!$pdoStatement->execute(array($idMessage))) {
            throw new Exception('Error while loading a message from database');
        }
        
        // Fetch the message from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Message::FIELDNAME_IDMESSAGE.' = ?');
        if (!$pdoStatement->execute(array($this->_idMessage))) {
            throw new Exception('Error while reloading data of a message from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idMessage,$sender,$recipient,$title,$date,$content) = $values;
        
        // Save values
        $this->_sender = $sender;
        $this->_recipient = $recipient;
        $this->_title = $title;
        $this->_date = $date === null ? null : strtotime($date);
        $this->_content = $content;
    }
    
    /**
     * Load all messages
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Message[] Array of messages
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all messages
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the messages
        $messages = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $messages;
    }
    
    /**
     * Select all messages
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all messages from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next message from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Message 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idMessage,$sender,$recipient,$title,$date,$content) = $values;
        
        // Construct the message
        return $lazyload && isset(self::$_lazyload[intval($idMessage)]) ? self::$_lazyload[intval($idMessage)] :
               new Message($pdo,intval($idMessage),$sender,$recipient,$title,strtotime($date),$content,$lazyload);
    }
    
    /**
     * Fetch all the messages from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Message[] Array of messages
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $messages = array();
        while ($message = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $messages[] = $message;
        }
        return $messages;
    }
    
    /**
     * Equality test
     * @param $message Message 
     * @return bool Objects are equals ?
     */
    public function equals($message)
    {
        // Test if null
        if ($message == null) { return false; }
        
        // Test class
        if (!($message instanceof Message)) { return false; }
        
        // Test ids
        return $this->_idMessage == $message->_idMessage;
    }
    
    /**
     * Check if the message exists in database
     * @return bool The message exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Message::FIELDNAME_IDMESSAGE.') FROM '.Message::TABLENAME.' WHERE '.Message::FIELDNAME_IDMESSAGE.' = ?');
        if (!$pdoStatement->execute(array($this->getIdMessage()))) {
            throw new Exception('Error while checking that a message exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete message
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete message
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Message::TABLENAME.' WHERE '.Message::FIELDNAME_IDMESSAGE.' = ?');
        if (!$pdoStatement->execute(array($this->getIdMessage()))) {
            throw new Exception('Error while deleting a message in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idMessage])) {
            unset(self::$_lazyload[$this->_idMessage]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update a field in database
     * @param $fields array 
     * @param $values array 
     * @return bool Successful operation ?
     */
    protected function _set($fields,$values)
    {
        // Prepare update
        $updates = array();
        foreach ($fields as $field) {
            $updates[] = $field.' = ?';
        }
        
        // Update field
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Message::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Message::FIELDNAME_IDMESSAGE.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdMessage())))) {
            throw new Exception('Error while updating a message\'s field in database');
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update all fields in database
     * @return bool Successful operation ?
     */
    public function update()
    {
        return $this->_set(array(Message::FIELDNAME_SENDER_IDMEMBER,Message::FIELDNAME_RECIPIENT_IDMEMBER,Message::FIELDNAME_TITLE,Message::FIELDNAME_DATE,Message::FIELDNAME_CONTENT),array($this->_sender,$this->_recipient,$this->_title,date('Y-m-d H:i:s',$this->_date),$this->_content));
    }
    
    /**
     * Get the idMessage
     * @return int 
     */
    public function getIdMessage()
    {
        return $this->_idMessage;
    }
    
    /**
     * Get the sender
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public function getSender($lazyload=true)
    {
        return Member::load($this->_pdo,$this->_sender,$lazyload);
    }
    
    /**
     * Get the sender's id
     * @return int sender's id
     */
    public function getSenderId()
    {
        return $this->_sender;
    }
    
    /**
     * Set the sender
     * @param $sender Member 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setSender(Member $sender,$execute=true)
    {
        // Save into object
        $this->_sender = $sender->getIdMember();
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_SENDER_IDMEMBER),array($sender->getIdMember())) : true;
    }
    
    /**
     * Set the sender by id
     * @param $idMember int 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setSenderById($idMember,$execute=true)
    {
        // Save into object
        $this->_sender = $idMember;
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_SENDER_IDMEMBER),array($idMember)) : true;
    }
    
    /**
     * Select messages by sender
     * @param $pdo PDO 
     * @param $sender Member 
     * @return PDOStatement 
     */
    public static function selectBySender(PDO $pdo,Member $sender)
    {
        $pdoStatement = self::_select($pdo,Message::FIELDNAME_SENDER_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($sender->getIdMember()))) {
            throw new Exception('Error while selecting all messages by sender in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Get the recipient
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public function getRecipient($lazyload=true)
    {
        return Member::load($this->_pdo,$this->_recipient,$lazyload);
    }
    
    /**
     * Get the recipient's id
     * @return int recipient's id
     */
    public function getRecipientId()
    {
        return $this->_recipient;
    }
    
    /**
     * Set the recipient
     * @param $recipient Member 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setRecipient(Member $recipient,$execute=true)
    {
        // Save into object
        $this->_recipient = $recipient->getIdMember();
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_RECIPIENT_IDMEMBER),array($recipient->getIdMember())) : true;
    }
    
    /**
     * Set the recipient by id
     * @param $idMember int 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setRecipientById($idMember,$execute=true)
    {
        // Save into object
        $this->_recipient = $idMember;
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_RECIPIENT_IDMEMBER),array($idMember)) : true;
    }
    
    /**
     * Select messages by recipient
     * @param $pdo PDO 
     * @param $recipient Member 
     * @return PDOStatement 
     */
    public static function selectByRecipient(PDO $pdo,Member $recipient)
    {
        $pdoStatement = self::_select($pdo,Message::FIELDNAME_RECIPIENT_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($recipient->getIdMember()))) {
            throw new Exception('Error while selecting all messages by recipient in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Get the title
     * @return string 
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * Set the title
     * @param $title string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setTitle($title,$execute=true)
    {
        // Save into object
        $this->_title = $title;
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_TITLE),array($title)) : true;
    }
    
    /**
     * Get the date
     * @return int 
     */
    public function getDate()
    {
        return $this->_date;
    }
    
    /**
     * Set the date
     * @param $date int 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setDate($date,$execute=true)
    {
        // Save into object
        $this->_date = $date;
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_DATE),array(date('Y-m-d H:i:s',$date))) : true;
    }
    
    /**
     * Get the content
     * @return string 
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    /**
     * Set the content
     * @param $content string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setContent($content,$execute=true)
    {
        // Save into object
        $this->_content = $content;
        
        // Save into database (or not)
        return $execute ? Message::_set(array(Message::FIELDNAME_CONTENT),array($content)) : true;
    }
    
    /**
     * ToString
     * @return string String representation of message
     */
    public function __toString()
    {
        return '[Message idMessage="'.$this->_idMessage.'" sender="'.$this->_sender.'" recipient="'.$this->_recipient.'" title="'.$this->_title.'" date="'.date('m/d/Y H:i:s',$this->_date).'" content="'.$this->_content.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of message
     */
    public function serialize($serialize=true)
    {
        // Serialize the message
        $array = array('idmessage' => $this->_idMessage,'sender' => $this->_sender,'recipient' => $this->_recipient,'title' => $this->_title,'date' => $this->_date,'content' => $this->_content);
        
        // Return the serialized (or not) message
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of message
     * @param $lazyload bool Enable lazy load ?
     * @return Message 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the message
        return $lazyload && isset(self::$_lazyload[$array['idmessage']]) ? self::$_lazyload[$array['idmessage']] :
               new Message($pdo,$array['idmessage'],$array['sender'],$array['recipient'],$array['title'],$array['date'],$array['content'],$lazyload);
    }
    
}

/**
 * @name Message
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Message extends Base_Message
{
    
    // Put your code here...
    
}

/**
 * @name Base_Post
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Post
{
    // Table name
    const TABLENAME = 'post';
    
    // Fields name
    const FIELDNAME_IDPOST = 'idpost';
    const FIELDNAME_MEMBER_IDMEMBER = 'fk_idmember';
    const FIELDNAME_TITLE = 'title';
    const FIELDNAME_DATE = 'date';
    const FIELDNAME_CONTENT = 'content';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var int  */
    protected $_idPost;
    
    /** @var int member's id */
    protected $_member;
    
    /** @var string  */
    protected $_title;
    
    /** @var int  */
    protected $_date;
    
    /** @var string  */
    protected $_content;
    
    /**
     * Construct a post
     * @param $pdo PDO 
     * @param $idPost int 
     * @param $member int member's id
     * @param $title string 
     * @param $date int 
     * @param $content string 
     */
    protected function __construct(PDO $pdo,$idPost,$member,$title,$date,$content)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idPost = $idPost;
        $this->_member = $member;
        $this->_title = $title;
        $this->_date = $date;
        $this->_content = $content;
    }
    
    /**
     * Create a post
     * @param $pdo PDO 
     * @param $member Member 
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @return Post 
     */
    protected static function _create(PDO $pdo,Member $member,$title,$date,$content)
    {
        // Add the post into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Post::TABLENAME.' ('.Post::FIELDNAME_MEMBER_IDMEMBER.','.Post::FIELDNAME_TITLE.','.Post::FIELDNAME_DATE.','.Post::FIELDNAME_CONTENT.') VALUES (?,?,?,?)');
        if (!$pdoStatement->execute(array($member->getIdMember(),$title,date('Y-m-d H:i:s',$date),$content))) {
            throw new Exception('Error while inserting a post into database');
        }
        
        // Return idPost
        return intval($pdo->lastInsertId());
    }
    
    /**
     * Count posts
     * @param $pdo PDO 
     * @return int Number of posts
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Post::FIELDNAME_IDPOST.') FROM '.Post::TABLENAME))) {
            throw new Exception('Error while counting posts in database');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Load a post
     * @param $pdo PDO 
     * @param $idPost int 
     * @return Post 
     */
    public static function load(PDO $pdo,$idPost)
    {
        if ($post = Question::load($pdo,$idPost)) { return $post; }
        if ($post = Answer::load($pdo,$idPost)) { return $post; }
        return null;
    }
    
    /**
     * Equality test
     * @param $post Post 
     * @return bool Objects are equals ?
     */
    public function equals($post)
    {
        // Test if null
        if ($post == null) { return false; }
        
        // Test class
        if (!($post instanceof Post)) { return false; }
        
        // Test ids
        return $this->_idPost == $post->_idPost;
    }
    
    /**
     * Delete post
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete post
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Post::TABLENAME.' WHERE '.Post::FIELDNAME_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPost()))) {
            throw new Exception('Error while deleting a post in database');
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update a field in database
     * @param $fields array 
     * @param $values array 
     * @return bool Successful operation ?
     */
    protected function _set($fields,$values)
    {
        // Prepare update
        $updates = array();
        foreach ($fields as $field) {
            $updates[] = $field.' = ?';
        }
        
        // Update field
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Post::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Post::FIELDNAME_IDPOST.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdPost())))) {
            throw new Exception('Error while updating a post\'s field in database');
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update all fields in database
     * @return bool Successful operation ?
     */
    public function update()
    {
        return $this->_set(array(Post::FIELDNAME_MEMBER_IDMEMBER,Post::FIELDNAME_TITLE,Post::FIELDNAME_DATE,Post::FIELDNAME_CONTENT),array($this->_member,$this->_title,date('Y-m-d H:i:s',$this->_date),$this->_content));
    }
    
    /**
     * Get the idPost
     * @return int 
     */
    public function getIdPost()
    {
        return $this->_idPost;
    }
    
    /**
     * Get the member
     * @param $lazyload bool Enable lazy load ?
     * @return Member 
     */
    public function getMember($lazyload=true)
    {
        return Member::load($this->_pdo,$this->_member,$lazyload);
    }
    
    /**
     * Get the member's id
     * @return int member's id
     */
    public function getMemberId()
    {
        return $this->_member;
    }
    
    /**
     * Set the member
     * @param $member Member 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setMember(Member $member,$execute=true)
    {
        // Save into object
        $this->_member = $member->getIdMember();
        
        // Save into database (or not)
        return $execute ? Post::_set(array(Post::FIELDNAME_MEMBER_IDMEMBER),array($member->getIdMember())) : true;
    }
    
    /**
     * Set the member by id
     * @param $idMember int 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setMemberById($idMember,$execute=true)
    {
        // Save into object
        $this->_member = $idMember;
        
        // Save into database (or not)
        return $execute ? Post::_set(array(Post::FIELDNAME_MEMBER_IDMEMBER),array($idMember)) : true;
    }
    
    /**
     * Get the title
     * @return string 
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * Set the title
     * @param $title string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setTitle($title,$execute=true)
    {
        // Save into object
        $this->_title = $title;
        
        // Save into database (or not)
        return $execute ? Post::_set(array(Post::FIELDNAME_TITLE),array($title)) : true;
    }
    
    /**
     * Get the date
     * @return int 
     */
    public function getDate()
    {
        return $this->_date;
    }
    
    /**
     * Set the date
     * @param $date int 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setDate($date,$execute=true)
    {
        // Save into object
        $this->_date = $date;
        
        // Save into database (or not)
        return $execute ? Post::_set(array(Post::FIELDNAME_DATE),array(date('Y-m-d H:i:s',$date))) : true;
    }
    
    /**
     * Get the content
     * @return string 
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    /**
     * Set the content
     * @param $content string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setContent($content,$execute=true)
    {
        // Save into object
        $this->_content = $content;
        
        // Save into database (or not)
        return $execute ? Post::_set(array(Post::FIELDNAME_CONTENT),array($content)) : true;
    }
    
    /**
     * ToString
     * @return string String representation of post
     */
    public function __toString()
    {
        return '[Post idPost="'.$this->_idPost.'" member="'.$this->_member.'" title="'.$this->_title.'" date="'.date('m/d/Y H:i:s',$this->_date).'" content="'.$this->_content.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of post
     */
    public function serialize($serialize=true)
    {
        // Serialize the post
        $array = array('idpost' => $this->_idPost,'member' => $this->_member,'title' => $this->_title,'date' => $this->_date,'content' => $this->_content);
        
        // Return the serialized (or not) post
        return $serialize ? serialize($array) : $array;
    }
    
}

/**
 * @name Post
 * @version 01/28/2014 (mm/dd/yyyy)
 */
abstract class Post extends Base_Post
{
    
    // Put your code here...
    
}

/**
 * @name Base_Question
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Question extends Post
{
    // Table name
    const TABLENAME = 'question';
    
    // Fields name
    const FIELDNAME_PARENT_IDPOST = 'parent_idpost';
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /**
     * Construct a question
     * @param $pdo PDO 
     * @param $idPost int 
     * @param $member int member's id
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idPost,$member,$title,$date,$content,$lazyload=true)
    {
        // Call parent constructor
        parent::__construct($pdo,$idPost,$member,$title,$date,$content);
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idPost] = $this;
        }
    }
    
    /**
     * Create a question
     * @param $pdo PDO 
     * @param $member Member 
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @param $lazyload bool Enable lazy load ?
     * @return Question 
     */
    public static function create(PDO $pdo,Member $member,$title,$date,$content,$lazyload=true)
    {
        // Create parent
        $idPost = parent::_create($pdo,$member,$title,$date,$content);
        
        // Add the question into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Question::TABLENAME.' ('.Question::FIELDNAME_PARENT_IDPOST.') VALUES (?)');
        if (!$pdoStatement->execute(array($idPost))) {
            throw new Exception('Error while inserting a question into database');
        }
        
        // Construct the question
        return new Question($pdo,$idPost,$member->getIdMember(),$title,$date,$content,$lazyload);
    }
    
    /**
     * Count questions
     * @param $pdo PDO 
     * @return int Number of questions
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Question::FIELDNAME_PARENT_IDPOST.') FROM '.Question::TABLENAME))) {
            throw new Exception('Error while counting questions in database');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Select query
     * @param $pdo PDO 
     * @param $where string|array 
     * @param $orderby string|array 
     * @param $limit string|array 
     * @param $from string|array 
     * @return PDOStatement 
     */
    protected static function _select(PDO $pdo,$where=null,$orderby=null,$limit=null,$from=null)
    {
        return $pdo->prepare('SELECT DISTINCT '.Post::TABLENAME.'.'.Post::FIELDNAME_IDPOST.', '.Post::TABLENAME.'.'.Post::FIELDNAME_MEMBER_IDMEMBER.', '.Post::TABLENAME.'.'.Post::FIELDNAME_TITLE.', '.Post::TABLENAME.'.'.Post::FIELDNAME_DATE.', '.Post::TABLENAME.'.'.Post::FIELDNAME_CONTENT.' '.
                             'FROM '.Post::TABLENAME.', '.Question::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ' WHERE '.Post::FIELDNAME_IDPOST.' = '.Question::FIELDNAME_PARENT_IDPOST.''.($where != null ? ' AND ('.$where.')' : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a question
     * @param $pdo PDO 
     * @param $idPost int 
     * @param $lazyload bool Enable lazy load ?
     * @return Question 
     */
    public static function load(PDO $pdo,$idPost,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idPost])) {
            return self::$_lazyload[$idPost];
        }
        
        // Load the question
        $pdoStatement = self::_select($pdo,Question::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($idPost))) {
            throw new Exception('Error while loading a question from database');
        }
        
        // Fetch the question from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Question::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->_idPost))) {
            throw new Exception('Error while reloading data of a question from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idPost,$member,$title,$date,$content) = $values;
        
        // Save values
        $this->_member = $member;
        $this->_title = $title;
        $this->_date = $date === null ? null : strtotime($date);
        $this->_content = $content;
    }
    
    /**
     * Load all questions
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Question[] Array of questions
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all questions
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the questions
        $questions = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $questions;
    }
    
    /**
     * Select all questions
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all questions from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next question from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Question 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idPost,$member,$title,$date,$content) = $values;
        
        // Construct the question
        return $lazyload && isset(self::$_lazyload[intval($idPost)]) ? self::$_lazyload[intval($idPost)] :
               new Question($pdo,intval($idPost),$member,$title,strtotime($date),$content,$lazyload);
    }
    
    /**
     * Fetch all the questions from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Question[] Array of questions
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $questions = array();
        while ($question = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $questions[] = $question;
        }
        return $questions;
    }
    
    /**
     * Equality test
     * @param $question Question 
     * @return bool Objects are equals ?
     */
    public function equals($question)
    {
        // Test if null
        if ($question == null) { return false; }
        
        // Test class
        if (!($question instanceof Question)) { return false; }
        
        // Test parent
        return parent::equals();
    }
    
    /**
     * Check if the question exists in database
     * @return bool The question exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Question::FIELDNAME_PARENT_IDPOST.') FROM '.Question::TABLENAME.' WHERE '.Question::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPost()))) {
            throw new Exception('Error while checking that a question exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete question
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated answers
        $select = $this->selectAnswers();
        while ($answer = Answer::fetch($this->_pdo,$select)) {
            $answer->delete();
        }
        
        // Delete question
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Question::TABLENAME.' WHERE '.Question::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPost()))) {
            throw new Exception('Error while deleting a question in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idPost])) {
            unset(self::$_lazyload[$this->_idPost]);
        }
        if ($pdoStatement->rowCount() != 1) { return false; }
        
        // Delete parent
        return parent::delete();
    }
    
    /**
     * Select answers
     * @return PDOStatement 
     */
    public function selectAnswers()
    {
        return Answer::selectByQuestion($this->_pdo,$this);
    }
    
    /**
     * Select questions by member
     * @param $pdo PDO 
     * @param $member Member 
     * @return PDOStatement 
     */
    public static function selectByMember(PDO $pdo,Member $member)
    {
        $pdoStatement = self::_select($pdo,Post::FIELDNAME_MEMBER_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($member->getIdMember()))) {
            throw new Exception('Error while selecting all questions by member in database');
        }
        return $pdoStatement;
    }
    
    /**
     * ToString
     * @return string String representation of question
     */
    public function __toString()
    {
        return parent::__toString().'<-'.'[Question]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of question
     */
    public function serialize($serialize=true)
    {
        // Serialize the question
        $array = array_merge(parent::serialize(false),array());
        
        // Return the serialized (or not) question
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of question
     * @param $lazyload bool Enable lazy load ?
     * @return Question 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the question
        return $lazyload && isset(self::$_lazyload[$array['idpost']]) ? self::$_lazyload[$array['idpost']] :
               new Question($pdo,$array['idpost'],$array['member'],$array['title'],$array['date'],$array['content'],$lazyload);
    }
    
}

/**
 * @name Question
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Question extends Base_Question
{
    
    // Put your code here...
    
}

/**
 * @name Base_Answer
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Answer extends Post
{
    // Table name
    const TABLENAME = 'answer';
    
    // Fields name
    const FIELDNAME_PARENT_IDPOST = 'parent_idpost';
    const FIELDNAME_QUESTION_IDPOST = 'fk_idpost';
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int question's id */
    protected $_question;
    
    /**
     * Construct a answer
     * @param $pdo PDO 
     * @param $idPost int 
     * @param $member int member's id
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @param $question int question's id
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idPost,$member,$title,$date,$content,$question,$lazyload=true)
    {
        // Call parent constructor
        parent::__construct($pdo,$idPost,$member,$title,$date,$content);
        
        // Save attributes
        $this->_question = $question;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idPost] = $this;
        }
    }
    
    /**
     * Create a answer
     * @param $pdo PDO 
     * @param $member Member 
     * @param $title string 
     * @param $date int 
     * @param $content string 
     * @param $question Question 
     * @param $lazyload bool Enable lazy load ?
     * @return Answer 
     */
    public static function create(PDO $pdo,Member $member,$title,$date,$content,Question $question,$lazyload=true)
    {
        // Create parent
        $idPost = parent::_create($pdo,$member,$title,$date,$content);
        
        // Add the answer into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Answer::TABLENAME.' ('.Answer::FIELDNAME_PARENT_IDPOST.','.Answer::FIELDNAME_QUESTION_IDPOST.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($idPost,$question->getIdPost()))) {
            throw new Exception('Error while inserting a answer into database');
        }
        
        // Construct the answer
        return new Answer($pdo,$idPost,$member->getIdMember(),$title,$date,$content,$question->getIdPost(),$lazyload);
    }
    
    /**
     * Count answers
     * @param $pdo PDO 
     * @return int Number of answers
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Answer::FIELDNAME_PARENT_IDPOST.') FROM '.Answer::TABLENAME))) {
            throw new Exception('Error while counting answers in database');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Select query
     * @param $pdo PDO 
     * @param $where string|array 
     * @param $orderby string|array 
     * @param $limit string|array 
     * @param $from string|array 
     * @return PDOStatement 
     */
    protected static function _select(PDO $pdo,$where=null,$orderby=null,$limit=null,$from=null)
    {
        return $pdo->prepare('SELECT DISTINCT '.Post::TABLENAME.'.'.Post::FIELDNAME_IDPOST.', '.Post::TABLENAME.'.'.Post::FIELDNAME_MEMBER_IDMEMBER.', '.Post::TABLENAME.'.'.Post::FIELDNAME_TITLE.', '.Post::TABLENAME.'.'.Post::FIELDNAME_DATE.', '.Post::TABLENAME.'.'.Post::FIELDNAME_CONTENT.', '.Answer::TABLENAME.'.'.Answer::FIELDNAME_QUESTION_IDPOST.' '.
                             'FROM '.Post::TABLENAME.', '.Answer::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ' WHERE '.Post::FIELDNAME_IDPOST.' = '.Answer::FIELDNAME_PARENT_IDPOST.''.($where != null ? ' AND ('.$where.')' : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a answer
     * @param $pdo PDO 
     * @param $idPost int 
     * @param $lazyload bool Enable lazy load ?
     * @return Answer 
     */
    public static function load(PDO $pdo,$idPost,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idPost])) {
            return self::$_lazyload[$idPost];
        }
        
        // Load the answer
        $pdoStatement = self::_select($pdo,Answer::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($idPost))) {
            throw new Exception('Error while loading a answer from database');
        }
        
        // Fetch the answer from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Answer::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->_idPost))) {
            throw new Exception('Error while reloading data of a answer from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idPost,$member,$title,$date,$content,$question) = $values;
        
        // Save values
        $this->_member = $member;
        $this->_title = $title;
        $this->_date = $date === null ? null : strtotime($date);
        $this->_content = $content;
        $this->_question = $question;
    }
    
    /**
     * Load all answers
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Answer[] Array of answers
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all answers
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the answers
        $answers = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $answers;
    }
    
    /**
     * Select all answers
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all answers from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next answer from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Answer 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idPost,$member,$title,$date,$content,$question) = $values;
        
        // Construct the answer
        return $lazyload && isset(self::$_lazyload[intval($idPost)]) ? self::$_lazyload[intval($idPost)] :
               new Answer($pdo,intval($idPost),$member,$title,strtotime($date),$content,$question,$lazyload);
    }
    
    /**
     * Fetch all the answers from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Answer[] Array of answers
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $answers = array();
        while ($answer = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $answers[] = $answer;
        }
        return $answers;
    }
    
    /**
     * Equality test
     * @param $answer Answer 
     * @return bool Objects are equals ?
     */
    public function equals($answer)
    {
        // Test if null
        if ($answer == null) { return false; }
        
        // Test class
        if (!($answer instanceof Answer)) { return false; }
        
        // Test parent
        return parent::equals();
    }
    
    /**
     * Check if the answer exists in database
     * @return bool The answer exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Answer::FIELDNAME_PARENT_IDPOST.') FROM '.Answer::TABLENAME.' WHERE '.Answer::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPost()))) {
            throw new Exception('Error while checking that a answer exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete answer
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete answer
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Answer::TABLENAME.' WHERE '.Answer::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPost()))) {
            throw new Exception('Error while deleting a answer in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idPost])) {
            unset(self::$_lazyload[$this->_idPost]);
        }
        if ($pdoStatement->rowCount() != 1) { return false; }
        
        // Delete parent
        return parent::delete();
    }
    
    /**
     * Update a field in database
     * @param $fields array 
     * @param $values array 
     * @return bool Successful operation ?
     */
    protected function _set($fields,$values)
    {
        // Prepare update
        $updates = array();
        foreach ($fields as $field) {
            $updates[] = $field.' = ?';
        }
        
        // Update field
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Answer::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Answer::FIELDNAME_PARENT_IDPOST.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdPost())))) {
            throw new Exception('Error while updating a answer\'s field in database');
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update all fields in database
     * @return bool Successful operation ?
     */
    public function update()
    {
        // Update parent
        parent::update();
        
        // Update all fields in database
        return $this->_set(array(Answer::FIELDNAME_QUESTION_IDPOST),array($this->_question));
    }
    
    /**
     * Get the question
     * @param $lazyload bool Enable lazy load ?
     * @return Question 
     */
    public function getQuestion($lazyload=true)
    {
        return Question::load($this->_pdo,$this->_question,$lazyload);
    }
    
    /**
     * Get the question's id
     * @return int question's id
     */
    public function getQuestionId()
    {
        return $this->_question;
    }
    
    /**
     * Set the question
     * @param $question Question 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setQuestion(Question $question,$execute=true)
    {
        // Save into object
        $this->_question = $question->getIdPost();
        
        // Save into database (or not)
        return $execute ? Answer::_set(array(Answer::FIELDNAME_QUESTION_IDPOST),array($question->getIdPost())) : true;
    }
    
    /**
     * Set the question by id
     * @param $idPost int 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setQuestionById($idPost,$execute=true)
    {
        // Save into object
        $this->_question = $idPost;
        
        // Save into database (or not)
        return $execute ? Answer::_set(array(Answer::FIELDNAME_QUESTION_IDPOST),array($idPost)) : true;
    }
    
    /**
     * Select answers by question
     * @param $pdo PDO 
     * @param $question Question 
     * @return PDOStatement 
     */
    public static function selectByQuestion(PDO $pdo,Question $question)
    {
        $pdoStatement = self::_select($pdo,Answer::FIELDNAME_QUESTION_IDPOST.' = ?');
        if (!$pdoStatement->execute(array($question->getIdPost()))) {
            throw new Exception('Error while selecting all answers by question in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Select answers by member
     * @param $pdo PDO 
     * @param $member Member 
     * @return PDOStatement 
     */
    public static function selectByMember(PDO $pdo,Member $member)
    {
        $pdoStatement = self::_select($pdo,Post::FIELDNAME_MEMBER_IDMEMBER.' = ?');
        if (!$pdoStatement->execute(array($member->getIdMember()))) {
            throw new Exception('Error while selecting all answers by member in database');
        }
        return $pdoStatement;
    }
    
    /**
     * ToString
     * @return string String representation of answer
     */
    public function __toString()
    {
        return parent::__toString().'<-'.'[Answer question="'.$this->_question.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of answer
     */
    public function serialize($serialize=true)
    {
        // Serialize the answer
        $array = array_merge(parent::serialize(false),array('question' => $this->_question));
        
        // Return the serialized (or not) answer
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of answer
     * @param $lazyload bool Enable lazy load ?
     * @return Answer 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the answer
        return $lazyload && isset(self::$_lazyload[$array['idpost']]) ? self::$_lazyload[$array['idpost']] :
               new Answer($pdo,$array['idpost'],$array['member'],$array['title'],$array['date'],$array['content'],$array['question'],$lazyload);
    }
    
}

/**
 * @name Answer
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Answer extends Base_Answer
{
    
    // Put your code here...
    
}

