<?php
require_once "../html/classes/DB.php";
require_once "../html/classes/Video.php";


class VideoTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $video_id, $fk_owner_id, $title, $filename, $extension, $mime, $size, $description, $emne;
    private $video, $videos;

    protected function _before() {
        $db = DB::getDBConnection();
        $this->fk_owner_id = '1';
        $this->video['title'] = $this->title;
        $this->video['filename'] = $this->filename;
        $this->video['extension'] = '.webm';
        $this->video['size'] = '2165175';
        $this->video['mime'] = 'application/octet-stream';
        $this->video['description'] = 'Test description working';
        $this->video['emne'] = 'IMT9000';
        $this->videos = new Video($db);
    }

    protected function _after()
    {
    }

    // tests
    public function testNewVideo() {
        $data = $this->videos->newVideo($this->video);
        $this->assertEquals('OK', $data['status'], 'Failed to create video');
        $videoID = $data['id'];
        $this->assertTrue($videoID>0, 'Video ID should be > 0');

    }

    public function testGetVideo() {
        $data = $this->videos->newVideo($this->newVideo());
        $videoID = $data['id'];

        $data = $this->video->getVideo($this->newVideo());
        $this->assertEquals('OK', $data['status'], 'Unable to find video');
    }

    public function testGetAllVideos() {
        $data = $this->videos->getAllVideos();
        $this->assertEquals('OK', $data['status'], 'Unable to get list of videos');
        $this->assertEquals($this->video_id,
            $data['video'][0]['video_id'],
            'Given video did not match');
    }





}