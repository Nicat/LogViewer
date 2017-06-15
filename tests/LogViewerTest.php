<?php
use app\Models\LogViewer;

/**
 * Class LogViewerTest
 */
class LogViewerTest extends PHPUnit_Framework_TestCase
{
    protected $logViewer;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->logViewer = new LogViewer();
    }


    /**
     * Test Error
     */
    public function testError()
    {
        $lines = $this->logViewer->setPath('/fake/path/')->lines()->get();
        $this->assertArrayHasKey('error', $lines);
    }

    /**
     * Test Caching
     */
    public function testCache()
    {
        $lines = $this->logViewer->setPath('/fake/path/')->lines()->get();
        $this->assertArrayHasKey('cache', $lines);
        $this->assertTrue($lines['cache']);
        // Caching time must be bless than 1 second
        $this->assertLessThan(1, $lines['time']);

        /* Disabled cache */
        $lines = $this->logViewer->setPath('/fake/path/')->setCache(false)->lines()->get();
        $this->assertArrayHasKey('cache', $lines);
        $this->assertFalse($lines['cache']);
    }

    /**
     * Test Real Path
     */
    public function testRealPath()
    {
        $lines = $this->logViewer->setPath('storage/logs/test.log')->lines()->get();

        $this->assertArrayHasKey('prev', $lines);
        $this->assertArrayHasKey('current', $lines);
        $this->assertArrayHasKey('next', $lines);
        $this->assertArrayHasKey('type', $lines);

        $this->assertEquals(10, count($lines['current']));
        $this->assertEquals(10, count($lines['next']));
        $this->assertEquals('file', $lines['type']);
        $this->assertEquals(false, $lines['prev']);
    }

    /**
     * Test Next Page
     */
    public function testNextPage()
    {
        $lines = $this->logViewer->setPath('storage/logs/test.log')->nextPage()->get();

        $this->assertArrayHasKey('prev', $lines);
        $this->assertArrayHasKey('current', $lines);
        $this->assertArrayHasKey('next', $lines);
        $this->assertArrayHasKey('type', $lines);

        $this->assertEquals(true, $lines['prev']);
        $this->assertEquals(10, count($lines['current']));
    }

    /**
     * Test Last Page
     */
    public function testLastPage()
    {
        $lines = $this->logViewer->setPath('storage/logs/test.log')->lastPage()->get();

        $this->assertArrayHasKey('prev', $lines);
        $this->assertArrayHasKey('current', $lines);
        $this->assertArrayHasKey('next', $lines);

        $this->assertEquals(false, $lines['next']);
    }

    /**
     * Test Limit
     */
    public function testLimit()
    {
        $limit = 20;
        $lines = $this->logViewer->setPath('storage/logs/test.log')->setLimit($limit)->nextPage()->get();

        $this->assertEquals($limit, count($lines['current']));
    }
}