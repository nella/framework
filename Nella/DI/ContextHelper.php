<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\DI;

/**
 * Console helper.
 *
 * @author	Patrik Votoček
 */
class ContextHelper extends \Symfony\Component\Console\Helper\Helper
{
	/** @var IContext */
    protected $_context;

    /**
     * Constructor
     *
     * @param IContext
     */
    public function __construct(IContext $context)
    {
        $this->_context = $context;
    }

	/**
     * Retrieves Nella context
     *
     * @return IContext
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'context';
    }
}
