<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TempfileChanges Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Files
 * @property \Cake\ORM\Association\BelongsTo $Versions
 *
 * @method \App\Model\Entity\TempfileChange get($primaryKey, $options = [])
 * @method \App\Model\Entity\TempfileChange newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TempfileChange[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TempfileChange|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TempfileChange patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TempfileChange[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TempfileChange findOrCreate($search, callable $callback = null)
 */
class TempfileChangesTable extends Table
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

        $this->table('tempfile_changes');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Files', [
            'foreignKey' => 'file_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Versions', [
            'foreignKey' => 'version_id',
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
            ->requirePresence('action', 'create')
            ->notEmpty('action');

        $validator
            ->dateTime('datetime')
            ->requirePresence('datetime', 'create')
            ->notEmpty('datetime');

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
        $rules->add($rules->existsIn(['file_id'], 'Files'));
        $rules->add($rules->existsIn(['version_id'], 'Versions'));

        return $rules;
    }
}
