<?php

/**
 * Field Reveal plugin for Craft CMS 3
 *
 * View where your custom fields are being used.
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\fieldreveal\services;

use Craft;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\UrlHelper;

use yii\base\Component;
use yii\data\Pagination;

use nav33d\fieldreveal\events\FieldRevealEvent;
use nav33d\fieldreveal\FieldReveal as FieldRevealPlugin;

class FieldReveal extends Component
{

    /**
     * @event FieldRevealEvent The event that is triggered before field data is sent back
     */
    const EVENT_BEFORE_FIELD_DATA = 'beforeFieldData';

    /**
     * Get all fields
     * 
     * @param string $fieldName
     * @param string $fieldGroup
     * 
     * @return array
     */
    public function getFields($fieldName, $fieldGroup)
    {
        $query = (new Query())
        ->select([
            'id' => 'fields.id',
            'name' => 'fields.name',
            'handle' => 'fields.handle',
            'type' => 'fields.type',
            'groupName' => 'fieldgroups.name',
        ])
        ->from('{{%fieldgroups}} fieldgroups')
        ->innerJoin(['{{%fields}} fields'], '[[fieldgroups.id]] = [[fields.groupId]]');

        if ( $fieldName )
        {
            $query->andWhere(['like', 'fields.name', $fieldName]);
        }

        if ( $fieldGroup )
        {
            $query->andWhere(['like', 'fieldgroups.name', $fieldGroup]);
        }

        $fields = $query->all();

        foreach ($fields as &$field) 
        {
            $field['displayName'] = (new \ReflectionClass($field['type']))->getShortName();
        }

        return $fields;
    }


    /**
     * Get ids of the field not being used at the moment
     * 
     * @return array
     */
    public function getUnusedFieldIds()
    {
        $fieldIds = (new Query())
            ->select(['id'])
            ->from(['{{%fields}}'])
            ->column();

        $usedFieldIds = (new Query())
            ->distinct(true)
            ->select(['fieldId'])
            ->from(['{{%fieldlayoutfields}}'])
            ->column();

        $diff = array_diff($fieldIds, $usedFieldIds);

        $response = [];
        foreach ($diff as $item )
        {
            $response[] = $item;
        }

        return $response;
    }


    /**
     * Get field groups
     * 
     * @return array
     */
    public function getFieldGroups()
    {
        $fieldGroups = (new Query())
            ->select(['name'])
            ->from(['{{%fieldgroups}}'])
            ->all();

        return $fieldGroups;
    }


    /**
     * Delete a field
     * 
     * @param int $fieldId
     * 
     * @return boolean
     */
    public function deleteField($fieldId)
    {
        return Craft::$app->fields->deleteFieldById($fieldId);
    }
    


    /**
     * Get field data
     * 
     * @param int $fieldId
     * 
     * @return array
     */
    public function getFieldData($fieldId)
    {
        $elements = (new Query())
            ->select([
                'fieldLayoutId' => 'fieldlayouts.id',
                'type' => 'fieldlayouts.type',
            ])
            ->from(['{{%fieldlayoutfields}} fieldlayoutfields'])
            ->where(['fieldId' => $fieldId])
            ->innerJoin(['{{%fieldlayouts}} fieldlayouts'], '[[fieldlayoutfields.layoutId]] = [[fieldlayouts.id]]')
            ->all();
            
        $data = [];
        foreach ( $elements as $item ) 
        {
            $elementType = $item['type'];
            $fieldLayoutId = $item['fieldLayoutId'];
            $shortName = (new \ReflectionClass($elementType))->getShortName();
    
            $data[$elementType]['name'] = $elementType;
            $data[$elementType]['shortName'] = $shortName;

            $sectionData = $this->getFieldSections($elementType, $fieldLayoutId);
            $data[$elementType]['sectionTitle'] = $sectionData['sectionTitle'] ?? 'Section';
            $data[$elementType]['sections'][] = $sectionData['data'];
        }

        $this->normalizeData($data);
        
        return $data;
    }


    /**
     * Get sections where a field is being used for a given element type
     * 
     * @param string $elementType
     * @param int $fieldLayoutId
     * @param string $fieldLayoutColumn
     * 
     * @return array
     */
    private function getFieldSections($elementType, $fieldLayoutId, $fieldLayoutColumn = "fieldLayoutId")
    {
        $sectionTable = "";
        $sectionTitle = "Section";

        switch ($elementType) 
        {
            case 'craft\\elements\\Entry':
                $sectionTable = "entrytypes";
                break;
            
            case 'craft\\elements\\GlobalSet':
                $sectionTable = "globalsets";
                $sectionTitle = "Global set";
                break;
            
            case 'craft\\elements\\Tag':
                $sectionTable = "taggroups";
                $sectionTitle = "Tag group";
                break;
            
            case 'craft\\elements\\Category':
                $sectionTable = "categorygroups";
                $sectionTitle = "Category group";
                break;
            
            case 'craft\\elements\\Asset':
                $sectionTable = "volumes";
                $sectionTitle = "Volume";
                break;

            case 'craft\\elements\\User':
                $sectionTable = "";
                break;

            default:
                $sectionTable = "";
                break;
        }


        $section = [];

        if ( $sectionTable )
        {
            $section = (new Query())
                ->select([
                    'name' => 'name',
                ])
                ->from(["{{%{$sectionTable}}}"])
                ->where(['fieldLayoutId' => $fieldLayoutId])
                ->all();
        }

        // Trigger a 'beforeFieldData' event
        $event = new FieldRevealEvent([
            'elementType' => $elementType,
            'fieldLayoutId' => $fieldLayoutId,
        ]);
        $this->trigger(self::EVENT_BEFORE_FIELD_DATA, $event);

        $sectionTitle = $event->sectionTitle ?? $sectionTitle;
        $section = $event->sectionData ?? $section;

        $response['sectionTitle'] = $sectionTitle;
        $response['data'] = $section;
        
        return $response;
    }


    /**
     * Normalize field data before returning back
     * 
     * @param array $data
     * 
     * @return array
     */
    private function normalizeData(&$data)
    {
        foreach ($data as &$item) 
        {
            if ( $item['sections'][0] )
            {
                $new = [];
                foreach ( $item['sections'] as $value ) 
                {
                    $new = array_merge_recursive($new, $value);
                }

                $item['sections'] = $new;
            }

            if ( !$item['sections'][0] )
            {
                unset( $item['sections'][0] );
            }
        }

        return $data;
    }
    
}
