<?php
/**
 * QSF Portal
 * Copyright (c) 2006-2015 The QSF Portal Development Team
 * https://github.com/Arthmoor/QSF-Portal
 *
 * Based on:
 *
 * Quicksilver Forums
 * Copyright (c) 2005-2011 The Quicksilver Forums Development Team
 * http://code.google.com/p/quicksilverforums/
 * 
 * MercuryBoard
 * Copyright (c) 2001-2006 The Mercury Development Team
 * https://github.com/markelliot/MercuryBoard
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 **/

if (!defined('QUICKSILVERFORUMS') || !defined('QSF_ADMIN')) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

require_once $set['include_path'] . '/admincp/admin.php';

/**
 * Query Interface
 *
 * @author Jason Warner <jason@mercuryboard.com>
 * @since RC1
 **/
class query extends admin
{
	/**
	 * Query Interface
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since RC1
	 * @return string HTML
	 **/
	function execute()
	{
		$this->set_title($this->lang->query);
		$this->tree($this->lang->query);

		if (!isset($this->post['submit'])) {
			$token = $this->generate_token();

			return $this->message($this->lang->query, "
			<form action='{$this->self}?a=query' method='post'>
				<div>
				<textarea class='input' name='sql' cols='30' rows='15' style='width:100%'>SELECT * FROM %pgroups</textarea><br /><br />
				<input type='hidden' name='token' value='$token' />
				<input type='submit' name='submit' value='{$this->lang->submit}' />
				</div>
			</form>");
		} else {
			if( !$this->is_valid_token() ) {
				return $this->message( $this->lang->query, $this->lang->invalid_token );
			}

			$result = $this->db->query($this->post['sql']);

			if (is_resource($result)) {
				$sql = htmlspecialchars($this->post['sql']);
				$show_headers = true;

				$out = $this->message($this->lang->query, "<form action='{$this->self}?a=query' method='post'><div>
					<textarea class='input' name='sql' cols='30' rows='15' style='width:100%'>$sql</textarea><br /><br />
					<input type='submit' name='submit' value='{$this->lang->submit}' /></div>
					</form><br />");
				$out .= $this->table;

				while ($row = $this->db->nqfetch($result))
				{
					if ($show_headers) {
						$out .= "<span class=\"head\">\n";

						foreach ($row as $col => $data)
						{
							$out .= "<span class='starter'>$col</span>\n";
						}

						$out .= "</span>\n<p></p>";

						$show_headers = false;
					}

					foreach ($row as $col => $data)
					{
						$out .= "<span class='starter'>" . $this->format($data, FORMAT_HTMLCHARS) . "</span>\n";
					}
					$out .= "<p class='list_line'></p>\n";
				}
				return $out . $this->etable;
			} else {
				return $this->message($this->lang->query, $this->lang->query_your . ' ' . ($result ? $this->lang->query_success : $this->lang->query_failed));
			}
		}
	}
}
?>