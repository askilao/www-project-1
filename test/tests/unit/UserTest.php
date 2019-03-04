<?php 
require_once "../html/classes/User.php";
require_once "../html/classes/DB.php";
class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $mail, $firstname, $userdata;
    private $password = 'password';
    private $user;
    private $db;
    protected function _before(){
        $db = DB::getDBConnection();
        $this->mail = md5(date('l jS \of F Y h:i:s A'));
        $this->firstname = md5(date('l jS h:i:s A \of F Y '));
        $this->userdata['firstname'] = $this->firstname;
        $this->userdata['mail'] = $this->firstname.'@'.$this->firstname.'.test';
        $this->userdata['password'] = $this->password;
        $this->userdata['role'] = 2;
        $this->userdata['isAdmin'] = 1;

        $this->user = new User($db);
    }
    protected function _after(){
    }
    // tests
    public function testCreateUser(){
        $data = $this->user->addUser($this->userdata);
        $this->assertEquals('OK', $data['status'], 'Failed to create user!');
        $this->assertTrue($data['id']>0, 'ID of new user should be > 0.');

        $delete = $this->user->deleteUser($data)['id'];
        $this->assertEquals('OK', $delete['status'], 'Failed to delete!');
    }
    public function testCanLogIn(){
        $userdata = $this->user->addUser($this->userdata);
        $data = $this->user->login($this->userdata['mail'], $this->userdata['password'], false);
        $this->assertEquals('OK', $data['status'], 'User cant login!');
        $deleteResult = $this->user->deleteUser();
        $this->assertEquals('OK', $data['status'], 'Failed to delete!');
    }
}