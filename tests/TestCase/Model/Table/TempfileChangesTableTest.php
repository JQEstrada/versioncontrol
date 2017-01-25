<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TempfileChangesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TempfileChangesTable Test Case
 */
class TempfileChangesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TempfileChangesTable
     */
    public $TempfileChanges;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.tempfile_changes',
        'app.files',
        'app.projects',
        'app.versions',
        'app.file_changes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('TempfileChanges') ? [] : ['className' => 'App\Model\Table\TempfileChangesTable'];
        $this->TempfileChanges = TableRegistry::get('TempfileChanges', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TempfileChanges);

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
