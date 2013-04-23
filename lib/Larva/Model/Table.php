<?php

    /*
     * The MIT License
     *
     * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
     * Copyright (c) 2012 Toha <tohenk@yahoo.com>
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     */

    namespace Larva\Model;

    use Larva\FKDumper;
    use MwbExporter\Formatter\FormatterInterface;
    use MwbExporter\Model\Table as BaseTable;
    use MwbExporter\Writer\WriterInterface;

    class Table extends BaseTable
    {
        public function writeTable(WriterInterface $writer)
        {
            if (!$this->isExternal()) {

                $info = array(
                    "Schema"       => $this->getSchema()->getName(),
                    "Table name"   => $this->getRawTableName(),
                    null,
                    "Generated at" => date('Y-m-d H:i:s', time()),
                );

                $config    = $this->getDocument()->getConfig();
                $use       = $config['use'];
                $extends   = $config['extends'];
                $namespace = $config['namespace'];

//                $writer->open($this->getTableFileName())->write('<?php');
//
//                $this->writeHeader($writer, $use, $namespace);
//                $this->writeClassDocBlock($writer, $info);

//                if (count($this->getColumns())) {
//                    foreach ($this->getColumns() as $column) {
//                        $column->write($writer);
//                    }
//                }

                echo "\n\n[{$this->getRawTableName()}]\n";
                $this->getColumns()->write($writer);
                print_r($this->getColumns()->getRelations());

//                $writer->write(sprintf('class %s extends %s', $this->getModelName(), $extends))
//                    ->write('{')
//                    ->indent()
//                    ->write('/**')
//                    ->write(' * Set the table name')
//                    ->write(' * @var string')
//                    ->write(' */')
//                    ->write('protected $table = \'' . $this->getRawTableName() . '\';')
//                    ->write('')
//                    ->writeCallback(function(WriterInterface $writer, Table $table = null) {
//                        if (count($table->getRelations())) {
////                            foreach ($table->getRelations() as $relation) {
////                                $relation->write($writer);
////                            }
//
//                            $this->getRelationsDeep($writer);
//                        }
//                    })
//                    ->writeCallback(function(WriterInterface $writer, Table $table = null) {
//                        if (count($table->getColumns())) {
////                            foreach ($table->getColumns() as $column) {
////                                $column->write($writer);
////                            }
//
//                            $table->getColumns()->write($writer);
//                        }
//                    })
//                    ->writeCallback(function(WriterInterface $writer, Table $table = null) {
//                        $fillable = $this->getFillableColumns();
//                        $writer->write(sprintf('protected $fillable = array(%s);', implode(', ', $fillable)));
//                    })
//                    ->outdent()
//                    ->write('}')
//                    ->write('')
//                    ->close();

                return self::WRITE_OK;
            }

            return self::WRITE_EXTERNAL;
        }

        private function getRelationsDeep(WriterInterface $writer)
        {
            $config    = $this->getDocument()->getConfig();
            $indent = $config[FormatterInterface::CFG_INDENTATION];
            $indentation = str_repeat('\s', $indent);
            $namespace = addslashes($config['namespace'].'\\');

//            echo str_repeat('-', 20).$this->getRawTableName().str_repeat('-', 20)."\n";
            foreach($this->getRelations() as $rel)
            {
                $relation = FKDumper::dump($rel);
//                $writer->write(sprintf('public function %s()', $relation->name));
//                $writer->write('{');
//                $writer->indent();
//                    $writer->write(sprintf('return $this->%s(\'%s\', \'%s\');', $relation->ownership, $namespace.$relation->model, $relation->fk));
//                $writer->outdent();
//                $writer->write('}');
//                $writer->write('');
            }

//            echo str_repeat('-', 50)."\n\n\n";
        }

        private function writeHeader(WriterInterface $writer, $use, $namespace)
        {
            $writer->write(sprintf('namespace %s;', $namespace));
            $writer->write('');

            foreach($use as $statement)
                $writer->write(sprintf('use %s;', $statement));

            $writer->write('');
        }

        private function writeClassDocBlock(WriterInterface $writer, $info)
        {
            $writer->write('/**')
                ->write(' * Generated by [Larva]')
                ->write(' * ')
                ->writeCallback(function (WriterInterface $writer, Table $table = null) use ($info) {
                    foreach ($info as $title => $val) {
                        if (!$val) {
                            $writer->write(' *');
                            continue;
                        }

                        $writer->write(sprintf(" * %-20s%s", $title . ': ', $val));
                    }
                })
                ->write(' */');
        }

        private function getFillableColumns()
        {
            foreach($this->getColumns() as $column)
            {
                if($column->isPrimary())
                    continue;

                if($column->hasOneToManyRelation())
                    continue;

                if($column->getColumnName() == 'password')
                    continue;

                $columns[] = sprintf('"%s"', $column->getColumnName());
            }

            return $columns;
        }
    }