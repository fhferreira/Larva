<?php
    namespace Larva\Model;

    use MwbExporter\Model\Base;
    use MwbExporter\Model\Columns as BaseColumns;
    use MwbExporter\Writer\WriterInterface;

    class Columns extends BaseColumns
    {
        private $relations = array();

        public function write(WriterInterface $writer)
        {
            // display column
            foreach ($this->columns as $column) {
                if (!$column->isPrimary() && ($column->getLocalForeignKey() || $column->hasOneToManyRelation())) {
                    // do not output fields of relations.
                    continue;
                }
                $column->write($writer);
            }
            // display column relations
            foreach ($this->columns as $column) {
                $column->writeRelations($writer);
            }

            return $this;
        }

        public function setRelationsForTable($tableName, $relations)
        {
            if(!isset($this->relations[$tableName]))
                $this->relations[$tableName] = array();

            $this->relations[$tableName] = array_merge($relations, $this->relations[$tableName]);
        }

        public function getRelations()
        {
            return $this->relations;
        }
    }