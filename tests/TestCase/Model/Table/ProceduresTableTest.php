<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProceduresTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProceduresTable Test Case
 */
class ProceduresTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ProceduresTable
     */
    public $Procedures;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.procedures',
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
        $config = TableRegistry::exists('Procedures') ? [] : ['className' => 'App\Model\Table\ProceduresTable'];
        $this->Procedures = TableRegistry::get('Procedures', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Procedures);

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
