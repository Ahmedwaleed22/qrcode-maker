<?php

class Link extends Dbh
{
	public function all() {
		$sql = "SELECT * FROM links";
		$stmt = $this->connect()->query($sql);
		$count = $stmt->rowCount();
		$rows = $stmt->fetchAll();

		return $count ? $rows : [];
	}

	public function create($link, $destination, $filename)
	{
		$sql = "INSERT INTO links (file_name, link, destination, created_date) VALUES (?, ?, ?, now())";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($filename, $link, $destination));
		$count = $stmt->rowCount();

		return $link;
	}

	public function getLink($link) {
		$sql = "SELECT * FROM links WHERE link = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($link));
		$row = $stmt->fetch();

		return $row;
	}

	public function addAccess($link, $ip, $country, $countryCode) {
		$sql = "INSERT INTO access (link_id, ip_address, country, country_code, created_at) VALUES (?, ?, ?, ?, now())";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($link, $ip, $country, $countryCode));
		$count = $stmt->rowCount();

		return $count ? true : false;
	}

	public function addImage($id, $image) {
		$sql = "UPDATE links SET `image` = ? WHERE `link` = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array('svgs/' . $id . '.svg', $id));
		$count = $stmt->rowCount();

		return $count ? true : false;
	}

	public function updateData($name, $link, $destination) {
		$sql = "UPDATE links SET `file_name` = ?, `destination` = ? WHERE `link` = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($name, $destination, $link));
		$count = $stmt->rowCount();

		return $count ? true : false;
	}

	public function delete($link) {
		$sql = "DELETE FROM links WHERE ID = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($link));

		return true;
	}

	public function track($linkID) {
		$sql = "SELECT country_code, COUNT(country_code) as count FROM access WHERE link_id = ? GROUP BY country_code HAVING COUNT(country_code) >= 1";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($linkID));
		$rows = $stmt->fetchAll();

		return $rows;
	}

	public function requests($linkID) {
		$sql = "SELECT * FROM access WHERE link_id = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute(array($linkID));
		$rows = $stmt->fetchAll();

		return $rows;
	}
}
