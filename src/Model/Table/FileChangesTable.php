<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FileChanges Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Files
 * @property \Cake\ORM\Association\BelongsTo $Versions
 *
 * @method \App\Model\Entity\FileChange get($primaryKey, $options = [])
 * @method \App\Model\Entity\FileChange newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\FileChange[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FileChange|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FileChange patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FileChange[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\FileChange findOrCreate($search, callable $callback = null)
 */
class FileChangesTable extends Table
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

        $this->table('file_changes');
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
