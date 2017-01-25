<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VersionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VersionsTable Test Case
 */
class VersionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\VersionsTable
     */
    public $Versions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.versions',
        'app.projects',
        'app.current_versions',
        'app.files',
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
        $config = TableRegistry::exists('Versions') ? [] : ['className' => 'App\Model\Table\VersionsTable'];
        $this->Versions = TableRegistry::get('Versions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Versions);

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
