/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

$(document).ready(function() {
	$('input[type=file][multiple][data-nella-mfu-token]').each(function() {
		$this = $(this);
		$this.nellaMultipleFileUploader({
			token: $this.attr('data-nella-mfu-token')
		});
	});
});