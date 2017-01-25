<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FileChangesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FileChangesTable Test Case
 */
class FileChangesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\FileChangesTable
     */
    public $FileChanges;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.file_changes',
        'app.files',
        'app.projects',
        'app.current_versions',
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
        $config = TableRegistry::exists('FileChanges') ? [] : ['className' => 'App\Model\Table\FileChangesTable'];
        $this->FileChanges = TableRegistry::get('FileChanges', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FileChanges);

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
