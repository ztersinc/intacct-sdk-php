<?php

/**
 * Copyright 2016 Intacct Corporation.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"). You may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "LICENSE" file accompanying this file. This file is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Intacct\Functions\Company;

use Intacct\Xml\XMLWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class AttachmentFileTest extends \PHPUnit_Framework_TestCase
{

    /** @var vfsStreamDirectory */
    private $root;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $structure = [
            'csv' => [
                'input.csv' => "hello,world\nunit,test",
            ]
        ];
        $this->root = vfsStream::setup('root', null, $structure);
    }

    /**
     * @covers Intacct\Functions\Company\AttachmentFile::writeXml
     */
    public function testConstruct()
    {
        $expected = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<attachment>
    <attachmentname>input</attachmentname>
    <attachmenttype>csv</attachmenttype>
    <attachmentdata>aGVsbG8sd29ybGQKdW5pdCx0ZXN0</attachmentdata>
</attachment>
EOF;

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startDocument();

        $record = new AttachmentFile();

        $this->assertTrue($this->root->hasChild('csv/input.csv'));

        $record->setFilePath($this->root->url() . '/csv/input.csv');

        $record->writeXml($xml);

        $this->assertXmlStringEqualsXmlString($expected, $xml->flush());
    }
}
