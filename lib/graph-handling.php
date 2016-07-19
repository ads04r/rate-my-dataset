<?php

function load_dataset($uri)
{
	$g = new Graphite();
	$g->load($uri);

	return($g);
}
