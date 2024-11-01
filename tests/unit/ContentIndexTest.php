<?php

use Codeception\Test\Unit;
use TheContentIndex\ContentIndex;

class ContentIndexTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $unitTester;

    /** @var ContentIndex */
    protected $contentIndex;

    protected function setUp()
    {
        if (!defined('ABSPATH')) {
            define('ABSPATH', 1);
        }

        $this->contentIndex = new TheContentIndex\ContentIndex();
    }

    public function testJustConfirmTestsAreWorking()
    {
        $this->assertTrue(true);
    }

    public function testGetFormNounceId()
    {
        $this->assertEquals(
            'the-content-index-nounce',
            $this->contentIndex->getFormNounceId()
        );
    }
}
