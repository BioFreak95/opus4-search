<?php
/**
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the Cooperative Library Network Berlin-Brandenburg,
 * the Saarland University and State Library, the Saxon State Library -
 * Dresden State and University Library, the Bielefeld University Library and
 * the University Library of Hamburg University of Technology with funding from
 * the German Research Foundation and the European Regional Development Fund.
 *
 * LICENCE
 * OPUS is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or any later version.
 * OPUS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * @category    Framework
 * @package     Opus_Search
 * @author      Oliver Marahrens <o.marahrens@tu-harburg.de>
 * @copyright   Copyright (c) 2008, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id$
 */

class Opus_Search_Index_Document extends Zend_Search_Lucene_Document
{
    /**
     * Constructor
     * 
     * @param Opus_Search_Adapter_DocumentAdapter &$document Document to index
     * @param Opus_Search_Adapter_FileAdapter 	  &$file 	 (Optional) File to index
     */
    public function __construct(Opus_Search_Adapter_DocumentAdapter &$document, Opus_Search_Adapter_FileAdapter &$file = null)
    {
        $doc = $document->getDocument();
        if ($file !== null) {
                $this->addField(Zend_Search_Lucene_Field::UnIndexed('docurl', $file->getURL()));
                $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $file->getFulltext()));
                $this->addField(Zend_Search_Lucene_Field::UnIndexed('source', $file->_path));
        } else {
                $this->addField(Zend_Search_Lucene_Field::UnIndexed('docurl', join("/", $doc['frontdoorUrl'])));
                $this->addField(Zend_Search_Lucene_Field::UnStored('contents', ''));
                $this->addField(Zend_Search_Lucene_Field::UnIndexed('source', 'Metadaten'));
        }
        $this->addField(Zend_Search_Lucene_Field::Keyword('docid', $doc['id']));
        #$this->addField(Zend_Search_Lucene_Field::UnIndexed('werkurl', $doc['frontdoorUrl']));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('year', $doc['year']));
        $this->addField(Zend_Search_Lucene_Field::Text('teaser', $doc['abstract']));
        $this->addField(Zend_Search_Lucene_Field::Text('title', $doc['title']));
		$authoriterator = new Opus_Search_Iterator_PersonsListIterator($doc['author']);
		$aut = '';
		foreach ($authoriterator as $obj) {
			$pers = $obj->get();
			$aut .= $pers['lastName'] . ', ' . $pers['firstName'];
			if ($authoriterator->hasNext() === true) {
				$aut .= '; ';
			}
		}
        $this->addField(Zend_Search_Lucene_Field::Text('author', $aut));
        $this->addField(Zend_Search_Lucene_Field::Keyword('urn', $doc['urn']));
    }
}