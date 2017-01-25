<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tempfile Entity
 *
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $path
 * @property string $type
 * @property string $extension
 * @property \Cake\I18n\Time $added_to_project
 * @property int $size
 * @property \Cake\I18n\Time $last_modified
 * @property \Cake\I18n\Time $system_file_created_on
 * @property string $origin
 *
 * @property \App\Model\Entity\Project $project
 */
class Tempfile extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
