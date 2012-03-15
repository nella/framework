<?php
/**
 * This file is part of the Nella Framework.
 * 
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 * 
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Mapping;

/**
 * I realy do feel bad for definning class in foreign namespace
 * but I have a good reason. This little hack prevents me from doing much uglier things.
 *
 * In order to be able to define own annotation without namespace prefix (ugly) I'm forced
 * to create another AnnotationReader instance and read the damn class fucking twice,
 * to be able to have annotation in my own namespace, without prefix.
 *
 * So fuck it, this is the best god damn fucking way. Don't you dare to question my sanity.
 */

/**
 * @author	Filip Procházka
 */
class DiscriminatorEntry extends \Doctrine\Common\Annotations\Annotation
{
	public $name;
}

class_alias('Nella\Doctrine\Mapping\DiscriminatorEntry', 'Doctrine\ORM\Mapping\DiscriminatorEntry');
