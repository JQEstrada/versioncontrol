<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Project Entity
 *
 * @property int $id
 * @property string $root_folder_path
 * @property \Cake\I18n\Time $last_change
 * @property int $current_version_id
 * @property string $archive_folder_path
 * @property string $physicalfiles
 * @property string $handshake_code
 * @property \Cake\I18n\Time $created_on
 *
 * @property \App\Model\Entity\CurrentVersion $current_version
 * @property \App\Model\Entity\File[] $files
 * @property \App\Model\Entity\Version[] $versions
 */
class Project extends Entity
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
