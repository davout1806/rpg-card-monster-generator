<?php

/**
 * Created by PhpStorm.
 * User: mikefuller
 * Date: 5/31/15
 * Time: 1:00 PM
 */


include "CsvImporter.php";

class JsonGenerator {
    const RUNNING_ON_MAC = false;            // set this to false if not running on a MAC OS

    const DATA_FILE_NAME = "card_data.txt";     // name of tab delimited file this script looks for

    const ICON_ABERRATION = 'alien-skull';          // icon to use for monsters of type aberration
    const ICON_BEAST = 'boar-tusks';                // icon to use for monsters of type beast
    const ICON_CELESTIAL = 'angel-wings';           // icon to use for monsters of type celestial
    const ICON_CONSTRUCT = 'robot-golem';           // icon to use for monsters of type construct
    const ICON_DRAGON = 'dragon-head';              // icon to use for monsters of type dragon
    const ICON_ELEMENTAL = 'pyromaniac';            // icon to use for monsters of type elemental
    const ICON_FEY = 'fairy-wand';                  // icon to use for monsters of type fey
    const ICON_FIEND = 'imp';                       // icon to use for monsters of type fiend
    const ICON_GIANT = 'muscle-fat';                // icon to use for monsters of type giant
    const ICON_HUMANOID = 'run';                    // icon to use for monsters of type humanoid
    const ICON_MONSTROSITY = 'gluttonous-smile';    // icon to use for monsters of type monstrosity
    const ICON_OOZE = 'gloop';                      // icon to use for monsters of type ooze
    const ICON_PLANT = 'beanstalk';                 // icon to use for monsters of type plant
    const ICON_UNDEAD = 'crowned-skull';            // icon to use for monsters of type undead

    // Field names used in first row of tab delimited file
    const FLD_MONSTER_NAME = 'Monster';         // name of monster
    const FLD_TYPE = 'Type';
    const FLD_SUBTYPE = 'Subtype';
    const FLD_SIZE = 'Size';
    const FLD_ALIGNMENT = 'Align';
    const FLD_STR = 'Str';
    const FLD_DEX = 'Dex';
    const FLD_CON = 'Con';
    const FLD_INT = 'Int';
    const FLD_WIS = 'Wis';
    const FLD_CHA = 'Cha';
    const FLD_AC = 'AC';
    const FLD_HP = 'HP';
    const FLD_SPEED = 'Speed';
    const FLD_OTHER_SPEED = 'Other Speed';
    // Those fields below affects label on cards
    const FLD_SAVES = 'Saves';
    const FLD_SKILLS = 'Skills';
    const FLD_DAMAGE_VULNER = 'Damage Vulnerabilities';
    const FLD_DAMAGE_RESIST = 'Damage Resistances';
    const FLD_DAMAGE_IMMUNE = 'Damage Immunities';
    const FLD_CONDITION_IMMUNE = 'Condition Immunities';
    const FLD_SENSES = 'Senses';
    const FLD_LANGUAGES = 'Languages';

    /* ======================================================================================= */

    public function format( $data ) {
        $newData = [ ];
        foreach ( $data as $record ) {
            $newData[] = [
                'count'    => 1,
                'title'    => $record[ self::FLD_MONSTER_NAME ],
                'contents' => $this->contents( $record ),
                'tags'     => [ ],
                'color'    => '',
                'icon'     => $this->icon( $record[ self::FLD_TYPE ] )
            ];
        }

        return json_encode( $newData );
    }

    private function contents( $record ) {
        $data = $this->sizeTypeAlign( $record );

        $data = $this->baseCombat( $record, $data );

        $data = $this->attributes( $record, $data );

        $data = $this->miscProperties( $record, $data );

        $data = $this->traits( $record, $data );

        $data = $this->actions( $record, $data );

        $data = $this->reactions( $record, $data );

        return $data;
    }

    private function sizeTypeAlign( $record ) {
        $sizeTypeAlign = "subtitle| {$record[ self::FLD_SIZE ]} {$record[ self::FLD_TYPE ]}";

        if ( ! empty( $record[ self::FLD_SUBTYPE ] ) ) {
            $sizeTypeAlign .= ' (' . $record[ self::FLD_SUBTYPE ] . ')';
        }

        $sizeTypeAlign .= ' ' . $record[ self::FLD_ALIGNMENT ];

        return [ $sizeTypeAlign, 'rule' ];
    }

    private function attributes( $record, $data ) {
        $attrs           = [ self::FLD_STR, self::FLD_DEX, self::FLD_CON, self::FLD_INT, self::FLD_WIS, self::FLD_CHA ];
        $attributeValues = 'dndstats';
        foreach ( $attrs as $attr ) {
            $attributeValues .= '|' . $record[ $attr ];
        }

        return array_merge( $data, [ $attributeValues, 'rule' ] );
    }

    private function baseCombat( $record, $data ) {
        $fields = [
            self::FLD_AC,
            self::FLD_HP,
        ];

        $data = array_merge( $data, $this->properties( $fields, $record ) );

        $data[] = $this->speedProperty( $record );

        $data[] = 'rule';

        return $data;
    }

    private function miscProperties( $record, $data ) {
        $fields = [
            self::FLD_SAVES,
            self::FLD_SKILLS,
            self::FLD_DAMAGE_VULNER,
            self::FLD_DAMAGE_RESIST,
            self::FLD_DAMAGE_IMMUNE,
            self::FLD_CONDITION_IMMUNE,
            self::FLD_SENSES,
            self::FLD_LANGUAGES
        ];

        $data = array_merge( $data, $this->properties( $fields, $record ) );

        $data[] = 'fill|1';

        return $data;
    }

    private function traits( $record, $data ) {
        return array_merge( $data, $this->conditionalSections( 'Trait', $record ) );
    }

    private function actions( $record, $data ) {
        return array_merge( $data, $this->conditionalSections( 'Action', $record ) );
    }

    private function reactions( $record, $data ) {
        return array_merge( $data, $this->conditionalSections( 'Reaction', $record ) );
    }

    private function properties( $fields, $record ) {
        $data = [ ];
        foreach ( $fields as $field ) {
            if ( ! empty( $record[ $field ] ) ) {
                $data[] = "property|$field|$record[$field]";
            }
        }

        return $data;
    }

    private function conditionalSections( $property, $record ) {
        $fields = $this->sequentialFields( $property );
        $data   = [ ];
        $empty  = true;
        for ( $i = 0; $i < count( $fields ); $i ++ ) {
            if ( ! empty( $record[ $fields[ $i ] ] ) ) {
                $empty   = false;
                $data [] = 'text|' . $record[ $fields[ $i ] ];
            }
        }

        if ( ! $empty ) {
            $data   = array_merge( [ 'section|' . $property . 's' ], $data );
            $data[] = 'fill|1';
        }

        return $data;
    }

    private function sequentialFields( $propertyPrefix ) {
        $fields = [ ];
        for ( $i = 1; $i <= 5; $i ++ ) {
            $fields[] = $propertyPrefix . $i;
        }

        return $fields;
    }

    private function speedProperty( $record ) {
        $speed = 'property|Speed|' . $record[ self::FLD_SPEED ];
        if ( ! empty( $record[ self::FLD_OTHER_SPEED ] ) ) {
            $speed .= '; ' . $record[ self::FLD_OTHER_SPEED ];

            return $speed;
        }

        return $speed;
    }

    private function icon( $type ) {
        switch ( strtolower( trim($type ) ) ) {
            case 'aberration' :
                return self::ICON_ABERRATION;
            case 'beast' :
                return self::ICON_BEAST;
            case 'celestial' :
                return self::ICON_CELESTIAL;
            case 'construct' :
                return self::ICON_CONSTRUCT;
            case 'dragon' :
                return self::ICON_DRAGON;
            case 'elemental' :
                return self::ICON_ELEMENTAL;
            case 'fey' :
                return self::ICON_FEY;
            case 'fiend' :
                return self::ICON_FIEND;
            case 'giant' :
                return self::ICON_GIANT;
            case 'humanoid' :
                return self::ICON_HUMANOID;
            case 'monstrosity' :
                return self::ICON_MONSTROSITY;
            case 'ooze' :
                return self::ICON_OOZE;
            case 'plant' :
                return self::ICON_PLANT;
            case 'undead' :
                return self::ICON_UNDEAD;
        }

        return '';
    }
}

ini_set('auto_detect_line_endings', JsonGenerator::RUNNING_ON_MAC);
$importer      = new CsvImporter( JsonGenerator::DATA_FILE_NAME, true );
$data          = $importer->get();
$jsonGenerator = new JsonGenerator();
print_r( $jsonGenerator->format( $data ) );