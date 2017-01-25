<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FileChange Entity
 *
 * @property int $id
 * @property int $file_id
 * @property int $version_id
 * @property string $action
 *
 * @property \App\Model\Entity\File $file
 * @property \App\Model\Entity\Version $version
 */
class FileChange extends Entity
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
