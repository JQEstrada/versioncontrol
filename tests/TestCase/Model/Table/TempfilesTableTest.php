<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TempfilesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TempfilesTable Test Case
 */
class TempfilesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TempfilesTable
     */
    public $Tempfiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.tempfiles',
        'app.projects',
        'app.files',
        'app.file_changes',
        'app.versions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Tempfiles') ? [] : ['className' => 'App\Model\Table\TempfilesTable'];
        $this->Tempfiles = TableRegistry::get('Tempfiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tempfiles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
