<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tempfiles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Projects
 *
 * @method \App\Model\Entity\Tempfile get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tempfile newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tempfile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tempfile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tempfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tempfile[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tempfile findOrCreate($search, callable $callback = null)
 */
class TempfilesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('tempfiles');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('path', 'create')
            ->notEmpty('path');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('extension', 'create')
            ->notEmpty('extension');

        $validator
            ->dateTime('added_to_project')
            ->requirePresence('added_to_project', 'create')
            ->notEmpty('added_to_project');

        $validator
            ->integer('size')
            ->requirePresence('size', 'create')
            ->notEmpty('size');

        $validator
            ->dateTime('last_modified')
            ->requirePresence('last_modified', 'create')
            ->notEmpty('last_modified');

        $validator
            ->dateTime('system_file_created_on')
            ->requirePresence('system_file_created_on', 'create')
            ->notEmpty('system_file_created_on');

        $validator
            ->requirePresence('origin', 'create')
            ->notEmpty('origin');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['project_id'], 'Projects'));

        return $rules;
    }
}
