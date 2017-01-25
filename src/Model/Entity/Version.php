<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Version Entity
 *
 * @property int $id
 * @property int $project_id
 * @property string $archive_folder_present
 * @property string $archive_folder_path
 * @property \Cake\I18n\Time $created_on
 *
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\FileChange[] $file_changes
 */
class Version extends Entity
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
