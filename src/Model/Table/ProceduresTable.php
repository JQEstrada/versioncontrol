<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Procedures Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Projects
 *
 * @method \App\Model\Entity\Procedure get($primaryKey, $options = [])
 * @method \App\Model\Entity\Procedure newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Procedure[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Procedure|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Procedure patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Procedure[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Procedure findOrCreate($search, callable $callback = null)
 */
class ProceduresTable extends Table
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

        $this->table('procedures');
        $this->displayField('id');
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
            ->requirePresence('current_procedure', 'create')
            ->notEmpty('current_procedure');

        $validator
            ->requirePresence('due_procedure', 'create')
            ->notEmpty('due_procedure');

        $validator
            ->boolean('is_complete')
            ->requirePresence('is_complete', 'create')
            ->notEmpty('is_complete');

        $validator
            ->dateTime('last_change')
            ->requirePresence('last_change', 'create')
            ->notEmpty('last_change');

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
