<?php

function extract_metadata($g)
{

	global $cfg;

	$subjects = array();
	$predicates = array();
	$objects = array();
	$types = array();

	foreach($g->allSubjects() as $res)
	{
		$uri = "" . $res;
		if(in_array($uri, $subjects)) { continue; }

		$subjects[] = $uri;
		foreach($res->relations() as $rel)
		{
			$uri = "" . $rel;
			if(in_array($uri, $predicates)) { continue; }

			$predicates[] = $uri;
			if(strcmp($uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type") == 0) { continue; }
			foreach($res->all($uri) as $obj)
			{
				$type = trim("" . $obj->datatype());
				$obj_txt = trim("" . $obj);
				if(strlen($type) > 0) { continue; }
				if(in_array($obj_txt, $objects)) { continue; }
				if(preg_match("/^([a-zA-Z]+)\\:/", $obj_txt) == 0) { continue; }

				$objects[] = $obj_txt;
			}
		}
	}


	foreach($subjects as $uri)
	{
		$res = $g->resource($uri);
		foreach($res->all("rdf:type") as $type)
		{
			$type_uri = "" . $type;
			if(in_array($type_uri, $types)) { continue; }

			$types[] = $type_uri;
		}
	}

	sort($types);
	sort($subjects);
	sort($predicates);
	sort($objects);

	return(array(
		"types" => $types,
		"predicates" => $predicates,
		"subjects" => $subjects,
		"objects" => $objects
	));
}


function extract_vocabularies($uri_list)
{
	$namespaces = array();
	$prefixes = array();
	foreach($uri_list as $type)
	{
		$ns = get_namespace($type);
		if(strlen($ns) == 0) { continue; }
		if(in_array($ns, $namespaces)) { continue; }

		$namespaces[] = $ns;
	}

	foreach($namespaces as $uri)
	{
		$prefix = get_prefix($uri);
		if(!(array_key_exists("prefix", $prefix))) { continue; }

		$prefixes[] = $prefix;
	}

	return($prefixes);
}

function extract_classes($type_list)
{
	$ret = array();
	foreach($type_list as $uri)
	{
		$g = new Graphite();
		$g->load($uri);
		$res = $g->resource($uri);
		$label = "" . $res->label();
		if(strcmp($label, "[NULL]") == 0)
		{
			$label = preg_replace("|^(.*)([/#])([^/#]+)$|", "$3", $uri);
		}

		$item = array();
		$item['label'] = $label;
		$item['uri'] = $uri;
		$ret[] = $item;
	}
	return($ret);
}

function extract_domains($objects)
{
	$ret = array();
	foreach($objects as $uri)
	{
		$m = array();
		if(preg_match("|://([^/]+)/|", $uri, $m) == 0) { continue; }

		$domain = $m[1];
		if(in_array($domain, $ret)) { continue; }

		$ret[] = $domain;
	}
	return($ret);
}
